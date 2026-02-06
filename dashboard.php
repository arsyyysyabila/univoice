<?php
session_start();
include 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$userName = $_SESSION['name'] ?? 'User';
$role = $_SESSION['role'] ?? 'student';

if ($role === 'student') {
    $where_clause = "student_id = '$user_id'";
} else if ($role === 'staff') {
    $where_clause = "staff_id = '$user_id'";
} else {
    $where_clause = "1=0";
}

$total_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE $where_clause");
$pending_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE $where_clause AND status_id = 1");
$resolved_q = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE $where_clause AND status_id = 3");

$stats = [
    'total' => mysqli_fetch_assoc($total_q)['total'] ?? 0,
    'pending' => mysqli_fetch_assoc($pending_q)['total'] ?? 0,
    'resolved' => mysqli_fetch_assoc($resolved_q)['total'] ?? 0
];

$sql = "SELECT c.*, d.department_name, s.status_name 
        FROM complaints c 
        LEFT JOIN department d ON c.department_id = d.department_id 
        LEFT JOIN complaints_status s ON c.status_id = s.status_id 
        WHERE $where_clause 
        ORDER BY c.submit_at DESC"; 

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | UniVoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --uitm-purple: #3d0a91; 
            --uitm-gold: #b28e2c; 
            --white: #ffffff; 
            --bg: #f8fafc; 
            --text-main: #1e293b;
        }
        
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); margin: 0; color: var(--text-main); }
        

        .main-header { 
            background: var(--white); 
            padding: 0 5%; 
            height: 75px; 
            display: flex; 
            align-items: center; 
            justify-content: space-between; 
            box-shadow: 0 2px 15px rgba(0,0,0,0.05); 
            border-bottom: 3px solid var(--uitm-purple); 
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .brand { color: var(--uitm-purple); font-weight: 800; font-size: 1.6rem; text-decoration: none; }
        .nav-links { display: flex; gap: 25px; align-items: center; }
        .nav-links a { text-decoration: none; color: #64748b; font-weight: 600; font-size: 0.9rem; transition: 0.3s; }
        .nav-links a.active { color: var(--uitm-purple); }
        .nav-links a:hover { color: var(--uitm-purple); }


        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }


        .stats-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { 
            background: var(--white); 
            padding: 25px; 
            border-radius: 20px; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.02); 
            border-left: 5px solid var(--uitm-purple); 
            display: flex;
            flex-direction: column;
        }
        .stat-card h3 { margin: 0; font-size: 0.8rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-card p { margin: 10px 0 0; font-size: 2.2rem; font-weight: 800; color: var(--uitm-purple); }


        .table-card { 
            background: var(--white); 
            border-radius: 20px; 
            overflow: hidden; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fcfdfe; padding: 18px; text-align: left; color: #64748b; font-size: 0.75rem; text-transform: uppercase; border-bottom: 1px solid #f1f5f9; }
        td { padding: 18px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; vertical-align: middle; }
        

        .badge { padding: 6px 12px; border-radius: 8px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; display: inline-block; }
        .status-1 { background: #fffbeb; color: #b45309; } /* Pending */
        .status-2 { background: #eff6ff; color: #1e40af; } /* In Progress */
        .status-3 { background: #ecfdf5; color: #065f46; } /* Resolved */

        /* Buttons & Icons */
        .btn-new { background: var(--uitm-purple); color: white; padding: 12px 24px; border-radius: 12px; text-decoration: none; font-weight: 700; font-size: 0.85rem; transition: 0.3s; box-shadow: 0 4px 12px rgba(61, 10, 145, 0.2); }
        .btn-new:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(61, 10, 145, 0.3); }
        .attach-link { color: var(--uitm-purple); text-decoration: none; font-size: 1.1rem; }
        
        .empty-state { text-align: center; padding: 60px; color: #94a3b8; }
        .empty-state i { font-size: 3rem; margin-bottom: 20px; }
    </style>
</head>
<body>

<header class="main-header">
    <a href="index.php" class="brand">UniVoice</a>
    <nav class="nav-links">
        <a href="index.php">Home</a>
        <a href="complaint_form.php">Submit</a>
        <a href="dashboard.php" class="active">Dashboard</a>
        <div style="margin-left: 15px; padding-left: 15px; border-left: 1px solid #eee;">
            <span style="font-size: 0.7rem; color: var(--uitm-gold); font-weight: 800; display: block; line-height: 1;">LOGGED IN AS</span>
            <span style="font-weight: 700; color: var(--uitm-purple); font-size: 0.85rem;"><?php echo strtoupper($userName); ?></span>
        </div>
    </nav>
</header>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
        <div>
            <h1 style="font-weight: 800; color: var(--uitm-purple); margin: 0; font-size: 1.8rem;">My Complaints</h1>
            <p style="color: #64748b; margin-top: 5px;">Track the status of your reported issues.</p>
        </div>
        <a href="complaint_form.php" class="btn-new"><i class="fas fa-plus"></i> NEW REPORT</a>
    </div>

    <div class="stats-row">
        <div class="stat-card">
            <h3>Total Submitted</h3>
            <p><?php echo $stats['total']; ?></p>
        </div>
        <div class="stat-card" style="border-left-color: var(--uitm-gold);">
            <h3>Pending Action</h3>
            <p><?php echo $stats['pending']; ?></p>
        </div>
        <div class="stat-card" style="border-left-color: #10b981;">
            <h3>Resolved</h3>
            <p><?php echo $stats['resolved']; ?></p>
        </div>
    </div>

    <div class="table-card">
        <?php if(mysqli_num_rows($result) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Date Submitted</th>
                    <th>Campus</th>
                    <th>Issue & Details</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Evidence</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td style="white-space: nowrap; color: #64748b;">
                        <i class="far fa-calendar-alt" style="margin-right: 5px;"></i>
                        <?php echo date('d M Y', strtotime($row['submit_at'])); ?>
                    </td>
                    <td>
                        <span style="font-weight: 600;"><i class="fas fa-map-marker-alt" style="color: var(--uitm-gold); margin-right: 5px;"></i> <?php echo htmlspecialchars($row['campus'] ?? 'N/A'); ?></span>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--uitm-purple);"><?php echo htmlspecialchars($row['issue']); ?></div>
                        <div style="font-size: 0.8rem; color: #64748b; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            <?php echo htmlspecialchars($row['description']); ?>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($row['department_name'] ?? 'General'); ?></td>
                    <td>
                        <span class="badge status-<?php echo $row['status_id']; ?>">
                            <?php echo htmlspecialchars($row['status_name']); ?>
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <?php if(!empty($row['attachment'])): ?>
                            <a href="uploads/<?php echo $row['attachment']; ?>" target="_blank" class="attach-link" title="View Evidence">
                                <i class="fas fa-paperclip"></i>
                            </a>
                        <?php else: ?>
                            <span style="color: #cbd5e1;">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-list"></i>
                <h2>No Complaints Found</h2>
                <p>You haven't submitted any reports yet. Click "New Report" to start.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>