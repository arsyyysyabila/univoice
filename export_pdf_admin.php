<?php
// Start output buffering to prevent "Headers already sent" errors
ob_start();
session_start();
include 'config.php';

// 1. ACCESS CONTROL
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// 2. INITIALIZE TCPDF
// Double check this path matches your folder structure!
if (!file_exists('tcpdf/tcpdf.php')) {
    die("Error: TCPDF library not found. Please check the path.");
}
require_once('tcpdf/tcpdf.php');

// 3. RETRIEVE AND SANITIZE FILTERS
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$filter_role = isset($_GET['role']) ? mysqli_real_escape_string($conn, $_GET['role']) : '';
$filter_campus = isset($_GET['campus']) ? mysqli_real_escape_string($conn, $_GET['campus']) : '';
$filter_department = isset($_GET['department']) ? mysqli_real_escape_string($conn, $_GET['department']) : '';

// 4. SQL QUERY
$sql = "SELECT 
    c.complaint_id,
    c.issue,
    c.description,
    c.campus,
    c.submit_at,
    c.status_id,
    c.is_anonymous,
    d.department_name,
    s.status_name,
    COALESCE(st.name, sf.name) as user_name,
    CASE WHEN c.student_id IS NOT NULL THEN 'Student' ELSE 'Staff' END as actual_role
FROM complaints c
LEFT JOIN department d ON c.department_id = d.department_id
LEFT JOIN complaints_status s ON c.status_id = s.status_id
LEFT JOIN student st ON c.student_id = st.student_id
LEFT JOIN staff sf ON c.staff_id = sf.staff_id
WHERE 1=1";

if (!empty($filter_status))     $sql .= " AND c.status_id = '$filter_status'";
if (!empty($filter_campus))     $sql .= " AND c.campus = '$filter_campus'";
if (!empty($filter_department)) $sql .= " AND c.department_id = '$filter_department'";

if (!empty($filter_role)) {
    if ($filter_role === 'student') $sql .= " AND c.student_id IS NOT NULL";
    elseif ($filter_role === 'staff') $sql .= " AND c.staff_id IS NOT NULL";
}

$sql .= " ORDER BY c.submit_at DESC";
$result = mysqli_query($conn, $sql);

// 5. PDF GENERATION SETTINGS
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetCreator('UniVoice System');
$pdf->SetAuthor('UiTM Kelantan Admin');
$pdf->SetTitle('Complaints Admin Report');

// Header and Footer
$pdf->SetHeaderData('', 0, 'UniVoice - Complaints Report (ADMIN)', 'UiTM Cawangan Kelantan - Generated: ' . date('d M Y, h:i A'));
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();

// Heading
$pdf->SetFont('helvetica', 'B', 16);
$pdf->SetTextColor(64, 22, 133); // UiTM Purple
$pdf->Cell(0, 10, 'COMPLAINTS SUMMARY REPORT - ADMIN COPY', 0, 1, 'C');
$pdf->Ln(5);

// Filter Metadata display
$pdf->SetFont('helvetica', '', 9);
$pdf->SetTextColor(0, 0, 0);
$total_records = mysqli_num_rows($result);
$pdf->Cell(0, 5, 'Total Records Found: ' . $total_records, 0, 1, 'L');
$pdf->Ln(5);

// 6. HTML TABLE
$html = '
<style>
    table { border-collapse: collapse; width: 100%; }
    th { background-color: #401685; color: white; font-weight: bold; padding: 10px; border: 1px solid #444; }
    td { padding: 8px; border: 1px solid #ccc; font-size: 8pt; }
</style>
<table>
<thead>
    <tr>
        <th width="7%">ID</th>
        <th width="18%">User Name</th>
        <th width="10%">Role</th>
        <th width="20%">Issue Title</th>
        <th width="15%">Dept</th>
        <th width="15%">Date</th>
        <th width="15%">Status</th>
    </tr>
</thead>
<tbody>';

if ($total_records > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $is_anon = ($row['is_anonymous'] == 1);
        $displayName = $is_anon ? '<i style="color:#666;">Anonymous</i>' : '<strong>' . htmlspecialchars($row['user_name']) . '</strong>';
        $displayRole = $is_anon ? 'N/A' : $row['actual_role'];

        $statusColor = '#333333';
        if ($row['status_id'] == 1) $statusColor = '#92400e'; // Pending
        if ($row['status_id'] == 2) $statusColor = '#1e40af'; // Progress
        if ($row['status_id'] == 3) $statusColor = '#065f46'; // Resolved

        $html .= '<tr>
            <td>' . $row['complaint_id'] . '</td>
            <td>' . $displayName . '</td>
            <td>' . $displayRole . '</td>
            <td>' . htmlspecialchars(substr($row['issue'], 0, 35)) . '...</td>
            <td>' . htmlspecialchars($row['department_name']) . '</td>
            <td>' . date('d/m/Y', strtotime($row['submit_at'])) . '</td>
            <td style="color: ' . $statusColor . '; font-weight: bold;">' . strtoupper($row['status_name']) . '</td>
        </tr>';
    }
} else {
    $html .= '<tr><td colspan="7" style="text-align:center;">No records found matching criteria.</td></tr>';
}
$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');

// 7. FOOTER
$pdf->Ln(10);
$pdf->SetFont('helvetica', 'I', 8);
$pdf->SetTextColor(220, 38, 38);
$pdf->Cell(0, 5, '*** CONFIDENTIAL - FOR OFFICIAL USE ONLY ***', 0, 1, 'C');

// 8. FINAL OUTPUT
// Clear the buffer before outputting PDF
ob_end_clean();
$filename = 'UniVoice_Report_' . date('Ymd_His') . '.pdf';
$pdf->Output($filename, 'D'); 
?>