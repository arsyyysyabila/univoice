<?php
session_start();
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_id = mysqli_real_escape_string($conn, $_POST['admin_id']);
    $password = $_POST['password'];


    $sql = "SELECT * FROM admin WHERE admin_id = '$admin_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);


    if (!$result) {
        die("Database error: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        
        
        if ($password === $row['password']) {
            session_unset();
            
          
            $_SESSION['user_id'] = $row['admin_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = 'admin'; 
            $_SESSION['admin_role'] = $row['role']; 
            
         
            $update_login = "UPDATE admin SET last_login = NOW() WHERE admin_id = '$admin_id'";
            mysqli_query($conn, $update_login);
         

            header("Location: admin_dashboard.php");
            exit();
        } else {
            header("Location: admin_login.php?error=Invalid Password");
            exit();
        }
    } else {
        header("Location: admin_login.php?error=Admin ID Not Found - Please check table admin exists");
        exit();
    }
} else {
    header("Location: admin_login.php");
    exit();
}
?>