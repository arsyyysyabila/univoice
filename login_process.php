<?php
session_start();
include "config.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role']; 
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $password = $_POST['password'];

    $table = ($role == 'student') ? 'student' : 'staff';
    $id_col = ($role == 'student') ? 'student_id' : 'staff_id';

    $sql = "SELECT * FROM $table WHERE $id_col = '$user_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);
        if ($password === $row['password']) {
            $_SESSION['user_id'] = $row[$id_col];
            $_SESSION['name'] = $row['name'];
            $_SESSION['role'] = $role;
            header("Location: index.php");
            exit();
        } else {
            header("Location: login.php?error=Wrong Password");
            exit();
        }
    } else {
        header("Location: login.php?error=User Not Found");
        exit();
    }
}
?>