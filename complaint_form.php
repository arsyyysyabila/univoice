<?php
session_start();
include 'config.php'; 

// REMOVED: Strict redirect. Now anyone can access this page.
$is_logged_in = isset($_SESSION['user_id']);

// Fetch departments from database
$dept_query = mysqli_query($conn, "SELECT * FROM department ORDER BY department_name ASC");

// User details from session or defaults for Guest
$userName = $_SESSION['name'] ?? 'Guest User';
$userRole = $_SESSION['role'] ?? 'Public'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint | UniVoice UiTM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --uitm-purple: #3d0a91;
            --uitm-purple-light: #5113b9;
            --uitm-gold: #b28e2c;
            --slate-50: #f8fafc;
            --white: #ffffff;
            --text-dark: #1e293b;
            --shadow: 0 15px 30px -5px rgba(61, 10, 145, 0.15);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--slate-50);
            background-image: radial-gradient(at 0% 0%, rgba(61, 10, 145, 0.05) 0px, transparent 50%);
            margin: 0; 
            min-height: 100vh;
            color: var(--text-dark);
        }

        /* --- Header Section --- */
        .main-header {
            background: var(--white);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-bottom: 3px solid var(--uitm-purple);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0.5rem 0;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 70px;
        }

        .brand { text-decoration: none; flex: 1; }
        .brand-text { color: var(--uitm-purple); font-weight: 800; font-size: 1.8rem; }

        .nav-links { display: flex; gap: 30px; justify-content: center; flex: 2; }
        .nav-links a {
            color: var(--text-dark);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            transition: 0.3s;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .nav-links a:hover, .nav-links a.active {
            color: var(--uitm-purple);
            background: rgba(61, 10, 145, 0.05);
        }

        .user-badge { display: flex; align-items: center; gap: 15px; justify-content: flex-end; flex: 1; }
        .user-info { text-align: right; line-height: 1.2; }
        .user-info .welcome { font-size: 0.65rem; font-weight: 800; color: var(--uitm-gold); text-transform: uppercase; display: block; }
        .user-info .name { font-size: 0.85rem; font-weight: 700; color: var(--uitm-purple); }

        .btn-auth {
            font-size: 1.1rem;
            padding: 10px;
            border-radius: 50%;
            transition: 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .btn-logout { background: #fff5f5; color: #ef4444; }
        .btn-logout:hover { background: #ef4444; color: white; }
        
        .btn-login-nav { background: #f0fdf4; color: #16a34a; }
        .btn-login-nav:hover { background: #16a34a; color: white; }

        /* --- Form Section --- */
        .form-wrapper { padding: 3rem 1rem; display: flex; justify-content: center; }
        .form-card {
            background: var(--white);
            width: 100%; max-width: 850px;
            border-radius: 20px; padding: 3rem;
            box-shadow: var(--shadow);
            border-top: 6px solid var(--uitm-gold);
        }

        h2 { font-size: 2.2rem; color: var(--uitm-purple); text-align: center; margin-bottom: 0.5rem; font-weight: 800; }
        p.subtitle { text-align: center; color: #64748b; margin-bottom: 2.5rem; font-weight: 500; }

        /* --- Identity Choice Box --- */
        .identity-selection {
            background: #f8fafc;
            border: 2px solid var(--border-color);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }

        .login-prompt {
            background: #fff9eb;
            border: 1px solid #fbd38d;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #744210;
            font-size: 0.9rem;
        }

        .anonymous-container {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 0.5rem;
        }

        .anonymous-container input[type="checkbox"] { width: 22px; height: 22px; cursor: pointer; }
        .anonymous-container label { margin-bottom: 0; text-transform: none; color: var(--text-dark); cursor: pointer; }

        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .input-group { margin-bottom: 1.5rem; }
        
        label { 
            display: block; 
            font-weight: 700; 
            margin-bottom: 0.6rem; 
            font-size: 0.8rem; 
            color: var(--uitm-purple); 
            text-transform: uppercase; 
            letter-spacing: 0.5px;
        }

        input, select, textarea {
            width: 100%; padding: 0.9rem 1.1rem;
            border: 1.5px solid #e2e8f0; border-radius: 12px;
            font-family: inherit; font-size: 1rem; transition: 0.3s;
            background: #fcfdfe;
            box-sizing: border-box;
        }

        input:focus, select:focus, textarea:focus { 
            outline: none; 
            border-color: var(--uitm-purple); 
            background: white;
            box-shadow: 0 0 0 4px rgba(61, 10, 145, 0.05); 
        }

        .btn-submit {
            background: var(--uitm-purple); color: white;
            width: 100%; padding: 1.1rem; border: none; border-radius: 12px;
            font-size: 1.1rem; font-weight: 700; cursor: pointer;
            display: flex; justify-content: center; align-items: center; gap: 12px;
            transition: 0.4s; margin-top: 1rem;
            box-shadow: 0 4px 15px rgba(61, 10, 145, 0.2);
        }

        .btn-submit:hover { 
            background: var(--uitm-purple-light); 
            transform: translateY(-2px); 
            box-shadow: 0 8px 20px rgba(61, 10, 145, 0.3);
        }

        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
            .nav-links { display: none; }
        }
    </style>
</head>
<body>

<header class="main-header">
    <div class="nav-container">
        <a href="index.php" class="brand">
            <span class="brand-text">UniVoice</span>
        </a>

        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="complaint_form.php" class="active">Submit</a>
            <a href="dashboard.php">Dashboard</a>
        </nav>

        <div class="user-badge">
            <div class="user-info">
                <span class="welcome"><?php echo $is_logged_in ? "Logged in as ($userRole)" : "Current Session"; ?></span>
                <span class="name"><?php echo htmlspecialchars(strtoupper($userName)); ?></span>
            </div>
            <?php if($is_logged_in): ?>
                <a href="logout.php" class="btn-auth btn-logout" title="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-auth btn-login-nav" title="Login">
                    <i class="fas fa-sign-in-alt"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main class="form-wrapper">
    <div class="form-card">
        <h2>Submit a Complaint</h2>
        <p class="subtitle">Improve UiTM Kelantan by sharing your concerns with us.</p>

        <form action="submit_complaint.php" method="POST" enctype="multipart/form-data">
            
            <div class="identity-selection">
                <label><i class="fas fa-user-secret"></i> Filing Identity</label>
                
                <?php if(!$is_logged_in): ?>
                    <div class="login-prompt">
                        <i class="fas fa-info-circle"></i>
                        <span>You are not logged in. This report will be sent <strong>Anonymously</strong>. <a href="login.php" style="color: var(--uitm-purple); font-weight: 700;">Login here</a> to use your name and track progress.</span>
                    </div>
                    <div class="anonymous-container">
                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1" checked onclick="return false;">
                        <label for="is_anonymous">
                            <strong>Submit as Anonymous</strong> (Required for Guests)
                        </label>
                    </div>
                <?php else: ?>
                    <div class="anonymous-container">
                        <input type="checkbox" name="is_anonymous" id="is_anonymous" value="1">
                        <label for="is_anonymous">
                            <strong>Submit as Anonymous?</strong><br>
                            <small>Tick this to hide your name from the department. Untick to use your profile name.</small>
                        </label>
                    </div>
                <?php endif; ?>
            </div>

            <div class="grid-2">
                <div class="input-group">
                    <label><i class="fas fa-map-marker-alt"></i> Campus Location</label>
                    <select name="campus" required>
                        <option value="">-- Select Campus --</option>
                        <option value="Machang">UiTM Machang</option>
                        <option value="Kota Bharu">UiTM Kota Bharu (KKB)</option>
                    </select>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-list-ul"></i> Type of Complaint</label>
                    <select name="complaint_type" required>
                        <option value="">-- Select Category --</option>
                        <option value="Facilities">Facilities Maintenance</option>
                        <option value="ICT">ICT & Campus WiFi</option>
                        <option value="Academic">Academic Affairs</option>
                        <option value="Student Welfare">Student Welfare</option>
                        <option value="Security">Campus Security</option>
                        <option value="Hostel">Residential/Hostel</option>
                        <option value="Finance">Bursary/Finance</option>
                        <option value="Cleanliness">Cleanliness</option>
                        <option value="Others">General/Others</option>
                    </select>
                </div>
            </div>

            <div class="grid-2">
                <div class="input-group">
                    <label><i class="fas fa-building"></i> Target Department</label>
                    <select name="department_id" required>
                        <option value="">-- Select Department --</option>
                        <?php while($row = mysqli_fetch_assoc($dept_query)): ?>
                            <option value="<?php echo $row['department_id']; ?>">
                                <?php echo htmlspecialchars($row['department_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="input-group">
                    <label><i class="fas fa-calendar-alt"></i> Date of Incident</label>
                    <input type="date" name="incident_date" required value="<?php echo date('Y-m-d'); ?>">
                </div>
            </div>

            <div class="input-group">
                <label><i class="fas fa-pen-nib"></i> Subject / Issue Title</label>
                <input type="text" name="issue" required placeholder="Short summary of the issue">
            </div>

            <div class="input-group">
                <label><i class="fas fa-align-left"></i> Detailed Description</label>
                <textarea name="description" rows="5" required placeholder="Explain the problem..."></textarea>
            </div>

            <div class="input-group">
                <label><i class="fas fa-paperclip"></i> Evidence (Optional)</label>
                <input type="file" name="attachment" accept="image/*">
            </div>

            <button type="submit" class="btn-submit">
                <span>SEND COMPLAINT</span>
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</main>

</body>
</html>