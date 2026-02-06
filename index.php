<?php 
session_start(); 

$host = "localhost";
$user = "root";
$pass = "";
$db   = "univoice";

$conn = mysqli_connect($host, $user, $pass, $db);

$total_complaints = 0;
$total_resolved = 0;

if ($conn) {
    $res_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints");
    if ($res_total) {
        $total_complaints = mysqli_fetch_assoc($res_total)['total'];
    }

    $res_resolved = mysqli_query($conn, "SELECT COUNT(*) as total FROM complaints WHERE status_id = 3");
    if ($res_resolved) {
        $total_resolved = mysqli_fetch_assoc($res_resolved)['total'];
    }
}

// Check if user is admin/staff
$is_admin = (isset($_SESSION['role']) && $_SESSION['role'] === 'admin');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UniVoice | UiTM Cawangan Kelantan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --uitm-purple: #3d0a91;
            --uitm-purple-light: #5216b5;
            --uitm-gold: #b28e2c;
            --formal-gray: #1a1a1b;
            --light-bg: #f8f9fa;
            --border-color: #e2e8f0;
            --white: #ffffff;
            --footer-bg: #2c3e50; 
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background-color: var(--white); 
            color: var(--formal-gray);
            line-height: 1.6;
        }

        /* NAVBAR */
        .nav-bar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            height: 85px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 8%;
            border-bottom: 4px solid var(--uitm-purple);
            position: sticky;
            top: 0;
            z-index: 1000;
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
        
        /* Admin Badge */
        .admin-section {
            display: flex;
            align-items: center;
            gap: 15px;
            padding-right: 20px;
            border-right: 2px solid var(--border-color);
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
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .btn-admin-panel {
            background: var(--uitm-purple);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-admin-panel:hover {
            background: #2a0766;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(61, 10, 145, 0.3);
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
        .btn-login {
            color: var(--uitm-purple);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 10px 22px;
            border: 2px solid var(--uitm-purple);
            border-radius: 8px;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-login:hover { 
            background: var(--uitm-purple); 
            color: white; 
        }
        .btn-logout {
            border-color: #dc2626;
            color: #dc2626;
        }
        .btn-logout:hover {
            background: #dc2626;
            border-color: #dc2626;
            color: white;
        }

        /* HERO */
        .hero {
            padding: 120px 8%;
            background: linear-gradient(rgba(0, 0, 0, 0.65), rgba(0, 0, 0, 0.45)), 
                        url('images/uitm 1.png'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed; 
            display: flex;
            align-items: center;
            min-height: 85vh;
        }
        .hero-content { max-width: 850px; }
        .hero-tag {
            color: var(--uitm-gold);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 20px;
            padding-bottom: 5px;
            border-bottom: 2px solid var(--uitm-gold);
        }
        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            font-weight: 800;
            line-height: 1.1;
            color: #ffffff;
            margin-bottom: 25px;
        }
        .hero p {
            font-size: 1.2rem;
            color: #ffffff; 
            margin-bottom: 40px;
            max-width: 700px;
            opacity: 0.95;
        }
        .btn-gold {
            background: var(--uitm-gold);
            color: white;
            padding: 18px 45px;
            text-decoration: none;
            font-weight: 700;
            border-radius: 6px;
            display: inline-block;
            transition: var(--transition);
        }

        .dashboard-strip {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            background: var(--white);
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
        }
        .dashboard-item { 
            padding: 50px 8%; 
            border-right: 1px solid var(--border-color); 
        }
        .dashboard-item:last-child { border-right: none; }
        .dashboard-item h4 { 
            font-size: 0.8rem; 
            text-transform: uppercase; 
            color: var(--uitm-gold); 
            margin-bottom: 15px; font-weight: 800; 
        }

        .about-section { 
            padding: 100px 8%; 
            background: var(--white); 
        }
        .about-flex { 
            display: flex; 
            gap: 80px; align-items: center; 
        }
        .about-text h2 { 
            font-size: 2.8rem; 
            color: var(--uitm-purple); 
            margin-bottom: 25px; 
            font-weight: 800; 
        }
        .about-img img { 
            width: 100%; 
            max-width: 600px; 
            border-radius: 12px; 
            box-shadow: 30px 30px 0px var(--light-bg); 
            border: 1px solid var(--border-color); 
        }

        .features-section { 
            background: var(--light-bg); 
            padding: 100px 8%; 
        }
        .features-grid {
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
            gap: 30px; 
        }
        .feature-card { 
            background: var(--white); 
            padding: 45px; 
            border-radius: 10px; 
            transition: var(--transition); 
        }
        .feature-card i { 
            color: var(--uitm-purple); 
            font-size: 2rem; 
            margin-bottom: 25px; 
        }

        .cta-banner {
            margin: 100px 8%;
            background: var(--uitm-purple);
            color: white;
            padding: 80px;
            border-radius: 15px;
            text-align: center;
        }

        .stats-section {
            padding: 80px 8%;
            background: var(--white);
            text-align: center;
            border-top: 1px solid var(--border-color);
        }
        .stats-grid {
            display: flex;
            justify-content: center;
            gap: 40px;
            flex-wrap: wrap;
            margin-top: 40px;
        }
        .stat-card {
            background: var(--light-bg);
            padding: 40px;
            border-radius: 15px;
            min-width: 280px;
            border-bottom: 6px solid var(--uitm-gold);
            transition: var(--transition);
        }
        .stat-card:hover { transform: translateY(-10px); }
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

        .main-footer { 
            background: var(--footer-bg); 
            color: white; 
            padding: 80px 8% 40px; 
        }
        .footer-container { 
            display: grid; 
            grid-template-columns: 1.5fr 1fr 1.5fr; 
            gap: 60px; 
            padding-bottom: 50px; 
            border-bottom: 1px solid rgba(255,255,255,0.1); 
        }
        .footer-bottom { 
            padding-top: 30px; 
            display: flex; 
            justify-content: space-between; 
            font-size: 0.85rem; 
            opacity: 0.7; 
        }
        .social-icons { 
            display: flex; 
            gap: 15px; 
            margin-top: 20px; 
        }
        .social-icons a { 
            width: 38px; 
            height: 38px; 
            background: white; 
            color: var(--footer-bg); 
            border-radius: 50%; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            text-decoration: none; 
            transition: var(--transition); 
        }
        .social-icons a:hover { background: var(--uitm-gold); color: white; }

        @media (max-width: 992px) {
            .footer-container { grid-template-columns: 1fr; }
            .nav-bar { padding: 0 4%; }
            .dashboard-strip { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <nav class="nav-bar">
        <a href="index.php" class="nav-logo">
            <img src="images/logo.png" alt="UiTM Logo">
        </a>

        <div class="nav-actions">
            <?php if(isset($_SESSION['user_id'])): ?>
                
                <?php if($is_admin): ?>
                    <div class="admin-section">
                        <span class="admin-badge">
                            <i class="fas fa-crown"></i> ADMIN ACCESS
                        </span>
                        <a href="admin_dashboard.php" class="btn-admin-panel">
                            <i class="fas fa-tachometer-alt"></i> ADMIN PANEL
                        </a>
                    </div>
                <?php endif; ?>
                
                <div class="user-profile">
                    <span class="welcome-text">Welcome back,</span>
                    <span class="user-name"><?php echo htmlspecialchars(strtoupper($_SESSION['name'])); ?></span>
                </div>
                <a href="logout.php" class="btn-login btn-logout">
                    <i class="fas fa-sign-out-alt"></i> LOGOUT
                </a>
            <?php else: ?>
                <a href="login.php" class="btn-login">
                    <i class="fas fa-user-circle"></i> LOGIN
                </a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="hero">
        <div class="hero-content">
            <span class="hero-tag">Official Student Portal</span>
            <h1>Integrity in Service,<br>Excellence in Action.</h1>
            <p>UniVoice is the formalized complaint management system for UiTM Cawangan Kelantan. We provide a professional channel for students to ensure their concerns reach administrative leadership.</p>
            <a href="complaint_form.php" class="btn-gold">SUBMIT A COMPLAINT</a>
        </div>
    </header>

    <div class="dashboard-strip">
        <div class="dashboard-item">
            <h4>Institutional Vision</h4>
            <p>To be the leading digital hub for student voice and campus excellence in UiTM Kelantan.</p>
        </div>
        <div class="dashboard-item">
            <h4>Our Mission</h4>
            <p>Providing a transparent, efficient, and responsive complaint management system for all students.</p>
        </div>
        <div class="dashboard-item">
            <h4>Key Objective</h4>
            <p>To resolve campus issues swiftly while maintaining the highest level of student confidentiality.</p>
        </div>
    </div>

    <section class="about-section" id="about">
        <div class="about-flex">
            <div class="about-text">
                <h2>About UniVoice</h2>
                <p>UniVoice bridges the communication gap between students and the management of UiTM Kelantan Branch, ensuring every concern regarding facilities or welfare is prioritized.</p>
                <div style="margin-top: 30px; padding: 25px; background: var(--light-bg); border-left: 5px solid var(--uitm-purple);">
                    <p style="font-weight: 700; color: var(--uitm-purple); font-style: italic;">
                        "Ensuring the voices of Machang & Kota Bharu students are always heard through structured digital innovation."
                    </p>
                </div>
            </div>
            <div class="about-img">
                <img src="images/org.jpeg" alt="Organization Structure">
            </div>
        </div>
    </section>

    <section class="features-section">
        <div style="text-align: center; margin-bottom: 60px;">
            <h2 style="color: var(--uitm-purple); font-size: 2.5rem; font-weight: 800;">System Features</h2>
            <p>Advanced tools for effective campus management</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-clock-rotate-left"></i>
                <h3>Real-time Tracking</h3>
                <p>Monitor the progress of your submission from initial review to final administrative resolution.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-user-shield"></i>
                <h3>Privacy Guaranteed</h3>
                <p>Strict security protocols ensure that your personal data and identities are protected at all times.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-network-wired"></i>
                <h3>Direct Integration</h3>
                <p>Your reports are automatically channeled to the responsible departments for immediate action.</p>
            </div>
        </div>
    </section>

    <section class="cta-banner">
        <h2>Ready to make an impact?</h2>
        <p>Login to your account to file a formal complaint or send as <strong>anonymous</strong> and track your existing complaints.</p>
        <p style="font-size: 0.9rem; margin-top: 10px; opacity: 0.8;">Note: Registration is required to use your name, but not for anonymous reports.</p>
        <a href="complaint_form.php" class="btn-gold" style="background: white; color: var(--uitm-purple); margin-top: 20px;">SUBMIT A COMPLAINT</a>
    </section>

    <section class="stats-section">
        <h2 style="color: var(--uitm-purple); font-weight: 800; font-size: 2.2rem;">Live Statistics</h2>
        <p>Transparency in our commitment to excellence</p>
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $total_complaints; ?></h3>
                <p>Total Complaints</p>
            </div>
            <div class="stat-card" style="border-bottom-color: var(--uitm-purple);">
                <h3><?php echo $total_resolved; ?></h3>
                <p>Resolved Cases</p>
            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-col">
                <h3><i class="fas fa-map-marker-alt"></i> Mailing Address</h3>
                <p>UiTM Cawangan Kelantan, Bukit Ilmu, 18500 Machang, Kelantan, Malaysia</p>
                <h3 style="margin-top: 20px;"><i class="fas fa-clock"></i> Operating Hours</h3>
                <p>Sun – Wed: 8:00 AM – 5:00 PM<br>Thu: 8:00 AM – 3:30 PM</p>
            </div>
            <div class="footer-col">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="https://facebook.com/uitmcaw.kelantan"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://youtube.com/@uitmcawangankelantan3571"><i class="fab fa-youtube"></i></a>
                    <a href="https://instagram.com/uitmcawangankelantan"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Stay Updated</h3>
                <p>Subscribe for campus announcements.</p>
                <div style="display: flex; margin-top: 15px;">
                    <input type="email" placeholder="Email address" style="padding: 12px; border: none; border-radius: 4px 0 0 4px; flex: 1;">
                    <button style="padding: 12px 20px; background: var(--uitm-gold); border: none; color: white; border-radius: 0 4px 4px 0; cursor: pointer;">SUBMIT</button>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 UniVoice UiTM Kelantan. All rights reserved.</p>
            <div>
                <a href="#" style="color: white; margin-left: 15px; text-decoration: none;">Privacy Policy</a>
                <a href="#" style="color: white; margin-left: 15px; text-decoration: none;">Terms of Use</a>
            </div>
        </div>
    </footer>

</body>
</html>