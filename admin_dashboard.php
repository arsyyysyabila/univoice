<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php?error=Access Denied - Admin Only");
    exit();
}

$adminName = $_SESSION['name'] ?? 'Admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    if (isset($_POST['update_status_inline'])) {
        $complaint_id = mysqli_real_escape_string($conn, $_POST['complaint_id']);
        $new_status = mysqli_real_escape_string($conn, $_POST['status_id']);
        
        $update_sql = "UPDATE complaints SET status_id = '$new_status' WHERE complaint_id = '$complaint_id'";
        if (mysqli_query($conn, $update_sql)) {
            $success_msg = "Status updated successfully!";
        } else {
            $error_msg = "Failed to update status: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['update_status'])) {
        $complaint_id = mysqli_real_escape_string($conn, $_POST['complaint_id']);
        $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
        
        $update_sql = "UPDATE complaints SET status_id = '$new_status' WHERE complaint_id = '$complaint_id'";
        if (mysqli_query($conn, $update_sql)) {
            $success_msg = "Status updated successfully!";
        } else {
            $error_msg = "Failed to update status: " . mysqli_error($conn);
        }
    }
    
    if (isset($_POST['delete_complaint'])) {
        $complaint_id = mysqli_real_escape_string($conn, $_POST['complaint_id']);
        
        $get_file = mysqli_query($conn, "SELECT attachment FROM complaints WHERE complaint_id = '$complaint_id'");
        if ($row = mysqli_fetch_assoc($get_file)) {
            if (!empty($row['attachment'])) {
                $file_path = "uploads/" . $row['attachment'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
        }
        
        $delete_sql = "DELETE FROM complaints WHERE complaint_id = '$complaint_id'";
        if (mysqli_query($conn, $delete_sql)) {
            $success_msg = "Complaint deleted successfully!";
        } else {
            $error_msg = "Failed to delete: " . mysqli_error($conn);
        }
    }
}


$total_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints");
$total_complaints = mysqli_fetch_assoc($total_query)['total'];

$resolved_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE status_id = 3");
$resolved_cases = mysqli_fetch_assoc($resolved_query)['total'];


$student_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE student_id IS NOT NULL");
$pelajar_total = mysqli_fetch_assoc($student_total)['total'];

$student_progress = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE student_id IS NOT NULL AND status_id = 2");
$pelajar_progress = mysqli_fetch_assoc($student_progress)['total'];

$student_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE student_id IS NOT NULL AND status_id = 1");
$pelajar_pending = mysqli_fetch_assoc($student_pending)['total'];

$student_resolved = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE student_id IS NOT NULL AND status_id = 3");
$pelajar_resolved = mysqli_fetch_assoc($student_resolved)['total'];


$staff_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE staff_id IS NOT NULL");
$staff_total_count = mysqli_fetch_assoc($staff_total)['total'];

$staff_progress = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE staff_id IS NOT NULL AND status_id = 2");
$staff_progress_count = mysqli_fetch_assoc($staff_progress)['total'];

$staff_pending = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE staff_id IS NOT NULL AND status_id = 1");
$staff_pending_count = mysqli_fetch_assoc($staff_pending)['total'];

$staff_resolved = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE staff_id IS NOT NULL AND status_id = 3");
$staff_resolved_count = mysqli_fetch_assoc($staff_resolved)['total'];


$student_dept_query = "SELECT d.department_name, COUNT(*) as total 
                       FROM complaints c 
                       LEFT JOIN department d ON c.department_id = d.department_id 
                       WHERE c.student_id IS NOT NULL 
                       GROUP BY d.department_name 
                       ORDER BY total DESC";
$student_dept_result = mysqli_query($conn, $student_dept_query);
$student_dept_data = [];
while ($row = mysqli_fetch_assoc($student_dept_result)) {
    $student_dept_data[] = $row;
}

$staff_dept_query = "SELECT d.department_name, COUNT(*) as total 
                     FROM complaints c 
                     LEFT JOIN department d ON c.department_id = d.department_id 
                     WHERE c.staff_id IS NOT NULL 
                     GROUP BY d.department_name 
                     ORDER BY total DESC";
$staff_dept_result = mysqli_query($conn, $staff_dept_query);
$staff_dept_data = [];
while ($row = mysqli_fetch_assoc($staff_dept_result)) {
    $staff_dept_data[] = $row;
}

$filter_status = isset($_GET['status']) ? $_GET['status'] : '';
$filter_role = isset($_GET['role']) ? $_GET['role'] : '';
$filter_campus = isset($_GET['campus']) ? $_GET['campus'] : '';

// UPDATED SQL: Added c.is_anonymous
$sql = "SELECT 
    c.complaint_id,
    c.issue,
    c.description,
    c.campus,
    c.attachment,
    c.submit_at,
    c.status_id,
    c.student_id,
    c.staff_id,
    c.is_anonymous,
    d.department_name,
    s.status_name,
    COALESCE(st.name, sf.name) as user_name
FROM complaints c
LEFT JOIN department d ON c.department_id = d.department_id
LEFT JOIN complaints_status s ON c.status_id = s.status_id
LEFT JOIN student st ON c.student_id = st.student_id
LEFT JOIN staff sf ON c.staff_id = sf.staff_id
WHERE 1=1";

if (!empty($filter_status)) {
    $sql .= " AND c.status_id = '$filter_status'";
}

if (!empty($filter_role)) {
    if ($filter_role === 'student') {
        $sql .= " AND c.student_id IS NOT NULL";
    } else if ($filter_role === 'staff') {
        $sql .= " AND c.staff_id IS NOT NULL";
    }
}

if (!empty($filter_campus)) {
    $sql .= " AND c.campus = '$filter_campus'";
}

$sql .= " ORDER BY c.submit_at DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Dashboard | UniVoice</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --uitm-purple: #401685;
            --uitm-purple-light: #5216b5;
            --uitm-gold: #b28e2c;
            --formal-gray: #1a1a1b;
            --light-bg: #f8f9fa;
            --border-color: #e2e8f0;
            --white: #ffffff;
            --sidebar-width: 260px;
            --transition: all 0.3s ease;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--light-bg);
            color: var(--formal-gray);
            line-height: 1.6;
        }

        /* SIDEBAR */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: var(--uitm-purple);
            color: white;
            padding: 30px 0;
            z-index: 1000;
            transition: var(--transition);
            overflow-y: auto;
        }

        .sidebar.collapsed {
            left: calc(-1 * var(--sidebar-width));
        }

        .sidebar-header {
            padding: 0 25px 30px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            font-size: 1.5rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px 25px;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 600;
        }

        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.1);
            color: white;
            border-left: 4px solid var(--uitm-gold);
        }

        .sidebar-menu a i {
            font-size: 1.2rem;
            width: 25px;
        }

        .sidebar-toggle {
            position: fixed;
            left: var(--sidebar-width);
            top: 20px;
            background: var(--uitm-purple);
            color: white;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            z-index: 999;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .sidebar-toggle:hover {
            background: var(--uitm-purple-light);
        }

        .sidebar.collapsed + .sidebar-toggle {
            left: 0;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: var(--transition);
            min-height: 100vh;
        }

        .sidebar.collapsed ~ .main-content {
            margin-left: 0;
        }

        /* NAVBAR */
        .nav-bar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            height: 85px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 4%;
            border-bottom: 4px solid var(--uitm-purple);
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .nav-logo img { 
            height: 55px; 
            width: auto; 
            object-fit: contain;
        }
        
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        
        .admin-badge {
            background: linear-gradient(135deg, var(--uitm-gold), #d4a574);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        
        .user-profile {
            text-align: right;
            border-right: 2px solid var(--border-color);
            padding-right: 20px;
        }
        
        .user-profile .welcome-text {
            font-size: 0.7rem;
            font-weight: 800;
            color: var(--uitm-gold);
            text-transform: uppercase;
            display: block;
            margin-bottom: -2px;
        }
        
        .user-profile .user-name {
            font-weight: 700;
            color: var(--uitm-purple);
            font-size: 0.95rem;
        }
        
        .btn-logout {
            border: 2px solid #dc2626;
            color: #dc2626;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 10px 22px;
            border-radius: 8px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-logout:hover {
            background: #dc2626;
            color: white;
        }

        /* ALERTS */
        .alert {
            margin: 20px 4%;
            padding: 15px 20px;
            border-radius: 10px;
            font-weight: 600;
        }
        
        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border-left: 4px solid #10b981;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        /* STATS SECTION */
        .stats-section {
            padding: 60px 4%;
            background: var(--white);
            text-align: center;
        }
        
        .stats-section h2 {
            color: var(--uitm-purple);
            font-weight: 800;
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        
        .stats-section > p {
            color: #64748b;
            margin-bottom: 40px;
        }
        
        .stats-grid {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
        }
        
        .stat-card {
            background: var(--light-bg);
            padding: 40px;
            border-radius: 15px;
            min-width: 280px;
            border-bottom: 6px solid var(--uitm-gold);
            transition: var(--transition);
        }
        
        .stat-card:hover { 
            transform: translateY(-10px); 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .stat-card h3 { 
            font-size: 3.5rem; 
            color: var(--uitm-purple); 
            font-weight: 800; 
            margin-bottom: 5px; 
        }
        
        .stat-card p { 
            font-weight: 700; 
            color: var(--uitm-gold); 
            text-transform: uppercase; 
            letter-spacing: 1px; 
            font-size: 0.8rem; 
        }
        
        .stat-card.purple-border {
            border-bottom-color: var(--uitm-purple);
        }

        /* DASHBOARD SECTIONS */
        .dashboard-section {
            padding: 60px 4%;
            background: var(--white);
            margin-top: 20px;
        }
        
        .dashboard-section:nth-child(even) {
            background: var(--light-bg);
        }
        
        .section-title {
            font-size: 1.8rem;
            color: var(--uitm-purple);
            font-weight: 800;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        /* METRIC CARDS GRID */
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .metric-card {
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            border-left: 5px solid;
            transition: var(--transition);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .metric-card.blue { border-left-color: #3b82f6; }
        .metric-card.orange { border-left-color: #f59e0b; }
        .metric-card.red { border-left-color: #ef4444; }
        .metric-card.green { border-left-color: #10b981; }
        
        .metric-card h3 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 5px;
        }
        
        .metric-card.blue h3 { color: #3b82f6; }
        .metric-card.orange h3 { color: #f59e0b; }
        .metric-card.red h3 { color: #ef4444; }
        .metric-card.green h3 { color: #10b981; }
        
        .metric-card p {
            color: #64748b;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .chart-container {
            background: var(--white);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            margin-top: 30px;
        }

        .chart-container h3 {
            color: var(--uitm-purple);
            font-weight: 700;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        /* FILTERS & BUTTONS */
        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        
        .filters select {
            padding: 12px 20px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            font-weight: 600;
            background: var(--white);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .filters select:focus {
            outline: none;
            border-color: var(--uitm-purple);
        }
        
        .btn-filter, .btn-reset {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }
        
        .btn-filter {
            background: var(--uitm-purple);
            color: white;
        }
        
        .btn-filter:hover {
            background: var(--uitm-purple-light);
            transform: translateY(-2px);
        }
        
        .btn-reset {
            background: #64748b;
            color: white;
        }
        
        .btn-reset:hover {
            background: #475569;
        }

        /* ACTION BUTTONS (Print Only) */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn-print,
        .btn-print-dept {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-print {
            background: #10b981;
            color: white;
        }

        .btn-print:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        .btn-print-dept {
            background: #14b8a6;
            color: white;
        }

        .btn-print-dept:hover {
            background: #0d9488;
            transform: translateY(-2px);
        }

        /* TABLE */
        .table-container {
            background: var(--white);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        thead {
            background: var(--uitm-purple);
            color: white;
        }
        
        th {
            padding: 18px 15px;
            text-align: left;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 18px 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        tbody tr:hover {
            background: var(--light-bg);
        }

        /* STATUS DROPDOWN IN TABLE */
        .status-dropdown {
            padding: 8px 14px;
            border: 2px solid transparent;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.75rem;
            cursor: pointer;
            transition: var(--transition);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-dropdown:focus {
            outline: none;
        }

        /* Status Colors */
        .status-dropdown option[value="1"],
        .status-dropdown[value="1"] {
            background: #fef3c7;
            color: #92400e;
        }

        .status-dropdown option[value="2"],
        .status-dropdown[value="2"] {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-dropdown option[value="3"],
        .status-dropdown[value="3"] {
            background: #d1fae5;
            color: #065f46;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 2px solid #fbbf24;
        }
        
        .status-progress {
            background: #dbeafe;
            color: #1e40af;
            border: 2px solid #3b82f6;
        }
        
        .status-resolved {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #10b981;
        }
        
        .btn-view {
            background: var(--uitm-purple);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: var(--transition);
        }
        
        .btn-view:hover {
            background: var(--uitm-purple-light);
            transform: translateY(-2px);
        }

        /* MODAL */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: var(--white);
            margin: 3% auto;
            padding: 40px;
            border-radius: 15px;
            width: 90%;
            max-width: 700px;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        
        .modal-content h2 {
            color: var(--uitm-purple);
            margin-bottom: 25px;
            font-size: 1.8rem;
        }
        
        .modal-content p {
            margin: 12px 0;
            line-height: 1.8;
        }
        
        .modal-content strong {
            color: var(--uitm-purple);
            font-weight: 700;
        }
        
        .close {
            color: #94a3b8;
            float: right;
            font-size: 32px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        
        .close:hover {
            color: var(--formal-gray);
        }
        
        .modal-content form {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid var(--border-color);
        }
        
        .modal-content label {
            display: block;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--uitm-purple);
        }
        
        .modal-content select {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-family: inherit;
            margin-bottom: 15px;
        }
        
        .btn-update {
            background: var(--uitm-purple);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            transition: var(--transition);
        }
        
        .btn-update:hover {
            background: var(--uitm-purple-light);
        }
        
        .btn-delete {
            background: #ef4444;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: var(--transition);
        }
        
        .btn-delete:hover {
            background: #dc2626;
        }

        /* EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 80px 40px;
            color: #94a3b8;
        }
        
        .empty-state i {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #64748b;
        }

        /* PRINT STYLES */
        @media print {
            @page {
                size: landscape;
                margin: 1cm;
            }

            body * {
                visibility: hidden;
            }
            
            .sidebar,
            .sidebar-toggle,
            .nav-bar,
            .filters,
            .action-buttons,
            .btn-view,
            .status-dropdown,
            .modal,
            .alert {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
            }
            
            #printableArea,
            #printableArea * {
                visibility: visible;
            }
            
            #printableArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            
            .table-container {
                box-shadow: none;
                border: 1px solid #ddd;
                page-break-inside: avoid;
            }
            
            table {
                page-break-inside: auto;
                width: 100%;
                font-size: 11px;
            }
            
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            
            thead {
                display: table-header-group;
            }

            th {
                padding: 12px 8px;
                font-size: 10px;
            }

            td {
                padding: 10px 8px;
                font-size: 10px;
                word-wrap: break-word;
            }
            
            .print-header {
                display: block !important;
                text-align: center;
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 3px solid var(--uitm-purple);
            }
            
            .print-header h1 {
                color: var(--uitm-purple);
                font-size: 24px;
                margin-bottom: 8px;
            }
            
            .print-header p {
                color: #64748b;
                font-size: 12px;
                margin: 3px 0;
            }
            
            .print-footer {
                display: block !important;
                margin-top: 20px;
                padding-top: 15px;
                border-top: 2px solid #e2e8f0;
                text-align: center;
                font-size: 11px;
                color: #94a3b8;
            }

            .no-print {
                display: none !important;
            }

            .status-badge {
                display: inline-block !important;
                padding: 6px 12px;
                border-radius: 20px;
            }

            /* Show full text in print for admin */
            .issue-text-full {
                display: block !important;
            }

            .issue-text-short {
                display: none !important;
            }

            /* Department print - hide status column */
            .print-dept .status-col {
                display: none !important;
            }
        }

        @media (max-width: 992px) {
            .sidebar {
                left: calc(-1 * var(--sidebar-width));
            }
            .sidebar-toggle {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .nav-bar { 
                padding: 0 4%; 
                height: auto; 
                flex-wrap: wrap; 
                padding-top: 15px; 
                padding-bottom: 15px; 
            }
            .stats-grid { flex-direction: column; }
            .metrics-grid { grid-template-columns: 1fr; }
            .dashboard-section { padding: 40px 4%; }
            .table-container { overflow-x: auto; }
        }
    </style>
</head>
<body>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
          <h2><i class="fas fa-user-shield"></i> Admin Panel</h2>
        </div>
        <div class="sidebar-menu">
            <a href="#statistics" class="active" onclick="scrollToSection('statistics')">
                <i class="fas fa-chart-line"></i>
                <span>Live Statistics</span>
            </a>
            <a href="#pelajar" onclick="scrollToSection('pelajar')">
                <i class="fas fa-graduation-cap"></i>
                <span>Aduan Pelajar</span>
            </a>
            <a href="#staff" onclick="scrollToSection('staff')">
                <i class="fas fa-id-badge"></i>
                <span>Aduan Staff</span>
            </a>
            <a href="#all-complaints" onclick="scrollToSection('all-complaints')">
                <i class="fas fa-list"></i>
                <span>Semua Aduan</span>
            </a>
            <a href="index.php">
                <i class="fas fa-home"></i>
                <span>Back to Main Site</span>
            </a>
        </div>
    </div>

    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="main-content">
       
        <nav class="nav-bar">
            <a href="index.php" class="nav-logo">
                <img src="images/logo.png" alt="UiTM Logo">
            </a>

            <div class="nav-actions">
                <span class="admin-badge">
                    <i class="fas fa-crown"></i> ADMIN ACCESS
                </span>
                
                <div class="user-profile">
                    <span class="welcome-text">Welcome back,</span>
                    <span class="user-name"><?php echo htmlspecialchars(strtoupper($adminName)); ?></span>
                </div>
                
                <a href="logout.php" class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> LOGOUT
                </a>
            </div>
        </nav>

        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <section class="stats-section" id="statistics">
            <h2>Live Statistics</h2>
            <p>Transparency in our commitment to excellence</p>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $total_complaints; ?></h3>
                    <p>Total Complaints</p>
                </div>
                <div class="stat-card purple-border">
                    <h3><?php echo $resolved_cases; ?></h3>
                    <p>Resolved Cases</p>
                </div>
            </div>
        </section>

        <div class="dashboard-section" id="pelajar">
            <h2 class="section-title">
                <i class="fas fa-graduation-cap"></i>
                Dashboard Aduan Pelajar
            </h2>
            
            <div class="metrics-grid">
                <div class="metric-card blue">
                    <h3><?php echo $pelajar_total; ?></h3>
                    <p>Total Complaint</p>
                </div>
                <div class="metric-card orange">
                    <h3><?php echo $pelajar_progress; ?></h3>
                    <p>In Progress</p>
                </div>
                <div class="metric-card red">
                    <h3><?php echo $pelajar_pending; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="metric-card green">
                    <h3><?php echo $pelajar_resolved; ?></h3>
                    <p>Resolved</p>
                </div>
            </div>


            <div class="chart-container">
                <h3><i class="fas fa-chart-bar"></i> Aduan Pelajar Mengikut Jabatan</h3>
                <canvas id="studentDeptChart"></canvas>
            </div>
        </div>

        <div class="dashboard-section" id="staff">
            <h2 class="section-title">
                <i class="fas fa-id-badge"></i>
                Dashboard Aduan Staff
            </h2>
            
            <div class="metrics-grid">
                <div class="metric-card blue">
                    <h3><?php echo $staff_total_count; ?></h3>
                    <p>Total Complaint</p>
                </div>
                <div class="metric-card orange">
                    <h3><?php echo $staff_progress_count; ?></h3>
                    <p>In Progress</p>
                </div>
                <div class="metric-card red">
                    <h3><?php echo $staff_pending_count; ?></h3>
                    <p>Pending</p>
                </div>
                <div class="metric-card green">
                    <h3><?php echo $staff_resolved_count; ?></h3>
                    <p>Resolved</p>
                </div>
            </div>

            <div class="chart-container">
                <h3><i class="fas fa-chart-bar"></i> Aduan Staff Mengikut Jabatan</h3>
                <canvas id="staffDeptChart"></canvas>
            </div>
        </div>

        <div class="dashboard-section" id="all-complaints">
            <h2 class="section-title">
                <i class="fas fa-list-alt"></i>
                Manage All Complaints
            </h2>

            <form method="GET" action="" class="filters">
                <select name="status">
                    <option value="">All Status</option>
                    <option value="1" <?php echo $filter_status == '1' ? 'selected' : ''; ?>>Pending</option>
                    <option value="2" <?php echo $filter_status == '2' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="3" <?php echo $filter_status == '3' ? 'selected' : ''; ?>>Resolved</option>
                </select>

                <select name="role">
                    <option value="">All Roles</option>
                    <option value="student" <?php echo $filter_role == 'student' ? 'selected' : ''; ?>>Student</option>
                    <option value="staff" <?php echo $filter_role == 'staff' ? 'selected' : ''; ?>>Staff</option>
                </select>

                <select name="campus">
                    <option value="">All Campus</option>
                    <option value="Machang" <?php echo $filter_campus == 'Machang' ? 'selected' : ''; ?>>Machang</option>
                    <option value="Kota Bharu" <?php echo $filter_campus == 'Kota Bharu' ? 'selected' : ''; ?>>Kota Bharu</option>
                </select>

                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> APPLY FILTER</button>
                <a href="admin_dashboard.php" class="btn-reset"><i class="fas fa-redo"></i> RESET</a>
            </form>

            <div class="action-buttons">
                <button onclick="printComplaintsAdmin()" class="btn-print">
                    <i class="fas fa-print"></i> PRINT (Admin)
                </button>
                <button onclick="printComplaintsDepartment()" class="btn-print-dept">
                    <i class="fas fa-print"></i> PRINT (Department)
                </button>
            </div>

            <div class="table-container" id="printableArea">
                <div class="print-header" style="display: none;">
                    <h1>UniVoice - Complaints Report</h1>
                    <p>UiTM Cawangan Kelantan</p>
                    <p>Generated: <?php echo date('d F Y, h:i A'); ?></p>
                    <?php if ($filter_status || $filter_role || $filter_campus): ?>
                    <p style="margin-top: 10px; font-weight: 600;">
                        Filters: 
                        <?php 
                        $filters = [];
                        if ($filter_status) {
                            $status_names = ['', 'Pending', 'In Progress', 'Resolved'];
                            $filters[] = 'Status: ' . $status_names[$filter_status];
                        }
                        if ($filter_role) $filters[] = 'Role: ' . ucfirst($filter_role);
                        if ($filter_campus) $filters[] = 'Campus: ' . $filter_campus;
                        echo implode(' | ', $filters);
                        ?>
                    </p>
                    <?php endif; ?>
                </div>

                <?php if (mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th class="name-col">Name</th>
                            <th class="role-col">Role</th>
                            <th>Issue</th>
                            <th>Department</th>
                            <th class="campus-col">Campus</th>
                            <th>Date</th>
                            <th class="status-col">Status</th>
                            <th class="no-print">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                    mysqli_data_seek($result, 0);
                    while($row = mysqli_fetch_assoc($result)): 
                        // Logic for Anonymity in Display
                        $is_anon = ($row['is_anonymous'] == 1);
                        $displayName = $is_anon ? '<i style="color:#666;">Anonymous</i>' : htmlspecialchars($row['user_name']);
                        $displayRole = $is_anon ? 'N/A' : ($row['student_id'] ? 'Student' : 'Staff');

                        $status_class = '';
                        if ($row['status_id'] == 1) $status_class = 'status-pending';
                        if ($row['status_id'] == 2) $status_class = 'status-progress';
                        if ($row['status_id'] == 3) $status_class = 'status-resolved';
                    ?>
                    <tr>
                        <td><?php echo $row['complaint_id']; ?></td>
                        <td class="name-col"><?php echo $displayName; ?></td>
                        <td class="role-col"><?php echo $displayRole; ?></td>
                        <td>
                            <span class="issue-text-short"><?php echo htmlspecialchars(substr($row['issue'], 0, 50)) . '...'; ?></span>
                            <span class="issue-text-full" style="display: none;"><?php echo htmlspecialchars($row['issue']); ?></span>
                        </td>
                        <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                        <td class="campus-col"><?php echo htmlspecialchars($row['campus']); ?></td>
                        <td><?php echo date('d M Y', strtotime($row['submit_at'])); ?></td>
                        <td class="status-col">
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="complaint_id" value="<?php echo $row['complaint_id']; ?>">
                                <select name="status_id" class="status-dropdown <?php echo $status_class; ?>" onchange="this.form.submit()" value="<?php echo $row['status_id']; ?>">
                                    <option value="1" <?php echo $row['status_id'] == 1 ? 'selected' : ''; ?>>Pending</option>
                                    <option value="2" <?php echo $row['status_id'] == 2 ? 'selected' : ''; ?>>In Progress</option>
                                    <option value="3" <?php echo $row['status_id'] == 3 ? 'selected' : ''; ?>>Resolved</option>
                                </select>
                                <input type="hidden" name="update_status_inline" value="1">
                            </form>
                            <span class="status-badge <?php echo $status_class; ?>" style="display: none;">
                                <?php echo $row['status_name']; ?>
                            </span>
                        </td>
                        <td class="no-print">
                            <button class="btn-view" onclick="viewComplaint(<?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>

                <div class="print-footer" style="display: none;">
                    <p>This report is generated by UniVoice Complaint Management System</p>
                    <p>UiTM Cawangan Kelantan | Confidential Document</p>
                </div>

                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>No complaints found</h3>
                    <p>Try adjusting your filters</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2><i class="fas fa-file-alt"></i> Complaint Details</h2>
        <div id="modalBody"></div>
        
        <form method="POST">
            <input type="hidden" name="complaint_id" id="modal_complaint_id">
            <label>Update Status:</label>
            <select name="new_status" required>
                <option value="1">Pending</option>
                <option value="2">In Progress</option>
                <option value="3">Resolved</option>
            </select>
            <button type="submit" name="update_status" class="btn-update">
                <i class="fas fa-sync-alt"></i> Update Status
            </button>
        </form>

        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this complaint?');">
            <input type="hidden" name="complaint_id" id="modal_complaint_id_delete">
            <button type="submit" name="delete_complaint" class="btn-delete">
                <i class="fas fa-trash"></i> Delete Complaint
            </button>
        </form>
    </div>
</div>


<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
}

function scrollToSection(sectionId) {
    event.preventDefault();
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.classList.remove('active');
    });
    event.target.closest('a').classList.add('active');
}

function printComplaintsAdmin() {
    // Show print header and footer
    document.querySelectorAll('.print-header, .print-footer').forEach(el => {
        el.style.display = 'block';
    });
    
    // Hide dropdown, show badge
    const table = document.querySelector('#printableArea table');
    if (table) {
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const dropdown = row.querySelector('.status-dropdown');
            const badge = row.querySelector('.status-badge');
            if (dropdown && badge) {
                dropdown.style.display = 'none';
                badge.style.display = 'inline-block';
            }

            // Show full issue text
            const issueShort = row.querySelector('.issue-text-short');
            const issueFull = row.querySelector('.issue-text-full');
            if (issueShort && issueFull) {
                issueShort.style.display = 'none';
                issueFull.style.display = 'block';
            }
        });
    }
    
    // Print
    window.print();
    
    // Restore after print
    setTimeout(() => {
        document.querySelectorAll('.print-header, .print-footer').forEach(el => {
            el.style.display = 'none';
        });
        
        if (table) {
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const dropdown = row.querySelector('.status-dropdown');
                const badge = row.querySelector('.status-badge');
                if (dropdown && badge) {
                    dropdown.style.display = '';
                    badge.style.display = 'none';
                }

                // Show short issue text again
                const issueShort = row.querySelector('.issue-text-short');
                const issueFull = row.querySelector('.issue-text-full');
                if (issueShort && issueFull) {
                    issueShort.style.display = '';
                    issueFull.style.display = 'none';
                }
            });
        }
    }, 1000);
}

function printComplaintsDepartment() {
    // Show print header and footer
    document.querySelectorAll('.print-header, .print-footer').forEach(el => {
        el.style.display = 'block';
    });
    
    // Hide columns: ID (1), Name (2), Campus (6), Status (8)
    // Show only: Role (3), Issue (4), Department (5), Date (7)
    const table = document.querySelector('#printableArea table');
    if (table) {
        // Add class to body for print styling
        document.body.classList.add('print-dept');
        
        // Hide headers: ID, Name, Campus, Status
        const headers = table.querySelectorAll('thead th');
        if (headers[0]) headers[0].style.display = 'none'; // ID
        if (headers[1]) headers[1].style.display = 'none'; // Name
        if (headers[5]) headers[5].style.display = 'none'; // Campus
        if (headers[7]) headers[7].style.display = 'none'; // Status
        
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells[0]) cells[0].style.display = 'none'; // ID
            if (cells[1]) cells[1].style.display = 'none'; // Name
            if (cells[5]) cells[5].style.display = 'none'; // Campus
            if (cells[7]) cells[7].style.display = 'none'; // Status

            // Show full issue text
            const issueShort = row.querySelector('.issue-text-short');
            const issueFull = row.querySelector('.issue-text-full');
            if (issueShort && issueFull) {
                issueShort.style.display = 'none';
                issueFull.style.display = 'block';
            }
        });
    }
    
    // Print
    window.print();
    
    // Restore after print
    setTimeout(() => {
        document.querySelectorAll('.print-header, .print-footer').forEach(el => {
            el.style.display = 'none';
        });
        
        // Remove print class
        document.body.classList.remove('print-dept');
        
        // Restore hidden columns
        if (table) {
            const headers = table.querySelectorAll('thead th');
            const rows = table.querySelectorAll('tbody tr');
            headers.forEach(th => th.style.display = '');
            rows.forEach(row => {
                row.querySelectorAll('td').forEach(td => td.style.display = '');

                // Show short issue text again
                const issueShort = row.querySelector('.issue-text-short');
                const issueFull = row.querySelector('.issue-text-full');
                if (issueShort && issueFull) {
                    issueShort.style.display = '';
                    issueFull.style.display = 'none';
                }
            });
        }
    }, 1000);
}

function viewComplaint(data) {
    document.getElementById('viewModal').style.display = 'block';
    document.getElementById('modal_complaint_id').value = data.complaint_id;
    document.getElementById('modal_complaint_id_delete').value = data.complaint_id;
    
    // Logic for Anonymity in Modal View
    let is_anon = (data.is_anonymous == 1);
    let displayName = is_anon ? '<i style="color:#666;">Anonymous</i>' : data.user_name;
    let displayRole = is_anon ? 'N/A' : (data.student_id ? 'Student' : 'Staff');

    let attachmentHTML = data.attachment 
        ? `<p><strong>Attachment:</strong> <a href="uploads/${data.attachment}" target="_blank" style="color: var(--uitm-purple); font-weight: 600;">View File</a></p>`
        : '<p><strong>Attachment:</strong> None</p>';
    
    document.getElementById('modalBody').innerHTML = `
        <p><strong>Complaint ID:</strong> ${data.complaint_id}</p>
        <p><strong>Name:</strong> ${displayName}</p>
        <p><strong>Role:</strong> ${displayRole}</p>
        <p><strong>Issue:</strong> ${data.issue}</p>
        <p><strong>Description:</strong> ${data.description}</p>
        <p><strong>Department:</strong> ${data.department_name}</p>
        <p><strong>Campus:</strong> ${data.campus}</p>
        <p><strong>Status:</strong> ${data.status_name}</p>
        <p><strong>Submitted:</strong> ${data.submit_at}</p>
        ${attachmentHTML}
    `;
}

function closeModal() {
    document.getElementById('viewModal').style.display = 'none';
}

window.onclick = function(event) {
    let modal = document.getElementById('viewModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// CARTA JABATAN PELAJAR
<?php if (!empty($student_dept_data)): ?>
const studentDeptCtx = document.getElementById('studentDeptChart').getContext('2d');
const studentDeptChart = new Chart(studentDeptCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($student_dept_data, 'department_name')); ?>,
        datasets: [{
            label: 'Jumlah Aduan',
            data: <?php echo json_encode(array_column($student_dept_data, 'total')); ?>,
            backgroundColor: [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#f43f5e'
            ],
            borderColor: '#ffffff',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { font: { size: 12, weight: '600' }, color: '#64748b' },
                grid: { color: '#e2e8f0' }
            },
            x: {
                ticks: { font: { size: 12, weight: '600' }, color: '#64748b' },
                grid: { display: false }
            }
        }
    }
});
<?php endif; ?>

// CARTA JABATAN STAFF
<?php if (!empty($staff_dept_data)): ?>
const staffDeptCtx = document.getElementById('staffDeptChart').getContext('2d');
const staffDeptChart = new Chart(staffDeptCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column($staff_dept_data, 'department_name')); ?>,
        datasets: [{
            label: 'Jumlah Aduan',
            data: <?php echo json_encode(array_column($staff_dept_data, 'total')); ?>,
            backgroundColor: [
                '#f43f5e', '#06b6d4', '#8b5cf6', '#10b981', '#f59e0b', '#3b82f6', '#fbbf24'
            ],
            borderColor: '#ffffff',
            borderWidth: 2,
            borderRadius: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: '#1e293b',
                padding: 12,
                titleFont: { size: 14, weight: 'bold' },
                bodyFont: { size: 13 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { font: { size: 12, weight: '600' }, color: '#64748b' },
                grid: { color: '#e2e8f0' }
            },
            x: {
                ticks: { font: { size: 12, weight: '600' }, color: '#64748b' },
                grid: { display: false }
            }
        }
    }
});
<?php endif; ?>
</script>

</body>
</html>