<?php
/**
 * CREATE ADMIN ACCOUNT
 * Run this file ONCE to create your first admin account
 * After creating, DELETE this file for security!
 */

include 'config.php';

// =====================================================
// ADMIN CREDENTIALS - CHANGE THESE!
// =====================================================
$admin_id = "ADMIN001";  // Change this
$admin_name = "Admin UniVoice";  // Change this
$admin_email = "admin@univoice.uitm.edu.my";  // Change this
$admin_password = "Admin@2026";  // Change this (use strong password!)
$admin_campus = "Machang";  // Machang or Kota Bharu

// =====================================================
// CREATE ADMIN
// =====================================================
echo "<style>
    body { font-family: Arial, sans-serif; padding: 40px; background: #f5f5f5; }
    .container { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
    h2 { color: #3d0a91; margin-bottom: 20px; }
    .success { color: #10b981; padding: 15px; background: #d1fae5; border-radius: 8px; margin: 15px 0; }
    .error { color: #ef4444; padding: 15px; background: #fee2e2; border-radius: 8px; margin: 15px 0; }
    .info { color: #3b82f6; padding: 15px; background: #dbeafe; border-radius: 8px; margin: 15px 0; }
    code { background: #f1f5f9; padding: 3px 8px; border-radius: 4px; font-family: 'Courier New', monospace; }
    .credentials { background: #fef3c7; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #d97706; }
    .warning { background: #fef2f2; padding: 15px; border-radius: 8px; margin: 15px 0; color: #dc2626; font-weight: bold; }
</style>";

echo "<div class='container'>";
echo "<h2>🔐 Create Admin Account</h2>";

// Check if staff table exists
$check_table = mysqli_query($conn, "SHOW TABLES LIKE 'staff'");
if (mysqli_num_rows($check_table) == 0) {
    echo "<div class='error'>❌ Error: 'staff' table does not exist. Please import univoice.sql first!</div>";
    echo "</div>";
    exit();
}

// Check if admin already exists
$check_admin = mysqli_query($conn, "SELECT * FROM staff WHERE staff_id = '$admin_id'");
if (mysqli_num_rows($check_admin) > 0) {
    echo "<div class='error'>❌ Admin with ID <code>$admin_id</code> already exists!</div>";
    echo "<div class='info'>If you want to reset password, delete the existing admin from database first.</div>";
    echo "</div>";
    exit();
}

// Insert admin account
$insert_sql = "INSERT INTO staff (staff_id, name, email, campus, password, confirm_password, attachment) 
               VALUES ('$admin_id', '$admin_name', '$admin_email', '$admin_campus', '$admin_password', '$admin_password', '')";

if (mysqli_query($conn, $insert_sql)) {
    echo "<div class='success'>✅ Admin account created successfully!</div>";
    
    echo "<div class='credentials'>";
    echo "<h3>📋 Login Credentials</h3>";
    echo "<p><strong>Staff ID:</strong> <code>$admin_id</code></p>";
    echo "<p><strong>Password:</strong> <code>$admin_password</code></p>";
    echo "<p><strong>Name:</strong> $admin_name</p>";
    echo "<p><strong>Email:</strong> $admin_email</p>";
    echo "<p><strong>Campus:</strong> $admin_campus</p>";
    echo "</div>";
    
    echo "<div class='info'>";
    echo "<h3>📝 Next Steps:</h3>";
    echo "<ol>";
    echo "<li>Go to <code>login.php</code></li>";
    echo "<li>Select <strong>Staff</strong> role</li>";
    echo "<li>Enter your Staff ID and Password</li>";
    echo "<li>After login, you'll be redirected to homepage</li>";
    echo "<li>Navigate to <code>admin_dashboard.php</code> to access admin panel</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<div class='warning'>";
    echo "⚠️ IMPORTANT: DELETE THIS FILE (create_admin.php) NOW FOR SECURITY!";
    echo "</div>";
    
} else {
    echo "<div class='error'>❌ Error creating admin: " . mysqli_error($conn) . "</div>";
}

echo "</div>";
?>