<?php
session_start();
include 'config.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Identification Logic
    $user_id = $_SESSION['user_id'] ?? null;
    $role = $_SESSION['role'] ?? 'student'; 
    
    /** * Anonymity Logic:
     * Set flag to 1 if:
     * - The "is_anonymous" checkbox was ticked
     * - OR the user is a guest (not logged in)
     */
    $is_anonymous_flag = (isset($_POST['is_anonymous']) || !$user_id) ? 1 : 0;
    
    // For database integrity, we use NULL. 
    // The Dashboard will check the is_anonymous_flag to display "Anonymous" as the Name and Role.
    $student_id_val = "NULL";
    $staff_id_val = "NULL";

    // Store actual IDs only if the user is NOT choosing to be anonymous
    if ($user_id && !$is_anonymous_flag) {
        if ($role === 'student') {
            $student_id_val = "'$user_id'";
        } else if ($role === 'staff') {
            $staff_id_val = "'$user_id'";
        }
    } 
    
    // 2. Data Sanitization
    $issue = mysqli_real_escape_string($conn, $_POST['issue']);
    $dept_id = mysqli_real_escape_string($conn, $_POST['department_id']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $campus = mysqli_real_escape_string($conn, $_POST['campus']); 
    $status_id = 1; // Default to Pending
    $date = date('Y-m-d H:i:s'); 

    // 3. Handle File Upload 
    $attachment_db = "NULL";
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === 0) {
        $upload_dir = "uploads/";
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
        $new_name = "UNI_" . time() . "_" . rand(1000, 9999) . "." . $file_ext;
        
        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $upload_dir . $new_name)) {
            $attachment_db = "'$new_name'";
        }
    }

    // 4. Insert Query
    // This query uses the is_anonymous flag to tell the dashboard to show "Anonymous" for Name and Role.
    $query = "INSERT INTO complaints (student_id, staff_id, issue, department_id, description, campus, attachment, status_id, is_anonymous, submit_at) 
              VALUES ($student_id_val, $staff_id_val, '$issue', '$dept_id', '$desc', '$campus', $attachment_db, $status_id, $is_anonymous_flag, '$date')";

    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Processing...</title>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f8fafc; }
        </style>
    </head>
    <body>";

    if (mysqli_query($conn, $query)) {
        // Redirection based on session status
        $redirect_page = $user_id ? 'dashboard.php' : 'index.php';
        $button_text = $user_id ? 'View Dashboard' : 'Back to Home';

        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Submission Successful!',
                text: 'Your complaint has been logged successfully.',
                confirmButtonColor: '#3d0a91',
                confirmButtonText: '$button_text'
            }).then((result) => {
                window.location.href = '$redirect_page';
            });
        </script>";
    } else {
        $db_error = addslashes(mysqli_error($conn));
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Submission Failed',
                text: 'Database Error: $db_error',
                confirmButtonColor: '#ef4444'
            }).then(() => {
                window.history.back();
            });
        </script>";
    }
    echo "</body></html>";
} else {
    header("Location: complaint_form.php");
    exit();
}
?>