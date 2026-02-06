<?php
session_start();
include "config.php"; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $campus = mysqli_real_escape_string($conn, $_POST['campus']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['retype_password'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!'); window.history.back();</script>";
        exit();
    }

    $table = ($role == 'student') ? "student" : "staff";
    $id_col = ($role == 'student') ? "student_id" : "staff_id";

    $check = mysqli_query($conn, "SELECT $id_col FROM $table WHERE $id_col = '$user_id'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Error: This ID is already registered!'); window.history.back();</script>";
        exit();
    }

    $sql = "INSERT INTO $table ($id_col, name, email, password, campus) 
            VALUES ('$user_id', '$name', '$email', '$password', '$campus')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Registration Successful!'); window.location.href='login.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>