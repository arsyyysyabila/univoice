<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | UniVoice UiTM</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --uitm-purple: #3d0a91;
            --uitm-gold: #b28e2c;
            --text-dark: #1e293b;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: linear-gradient(135deg, var(--uitm-purple) 0%, #2a0766 100%);
            height: 100vh; 
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .login-box {
            background: white;
            border-radius: 20px;
            padding: 50px;
            width: 450px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .admin-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .admin-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--uitm-gold) 0%, #9a7424 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(178, 142, 44, 0.4);
        }

        .admin-icon i {
            font-size: 35px;
            color: white;
        }

        .admin-header h1 {
            font-size: 1.8rem;
            color: var(--uitm-purple);
            font-weight: 800;
            margin-bottom: 5px;
        }

        .admin-header p {
            color: #64748b;
            font-size: 0.95rem;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-weight: 700;
            font-size: 0.85rem;
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 42px;
            color: #94a3b8;
            z-index: 2;
        }

        .input-group input {
            width: 100%;
            padding: 14px 12px 14px 45px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-family: inherit;
            transition: var(--transition);
            background: #fcfdfe;
            font-size: 0.95rem;
        }

        .input-group input:focus {
            border-color: var(--uitm-gold);
            background: #fff;
            outline: none;
            box-shadow: 0 0 0 4px rgba(178, 142, 44, 0.1);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, var(--uitm-gold) 0%, #9a7424 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: var(--transition);
            font-size: 1rem;
            letter-spacing: 0.5px;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(178, 142, 44, 0.3);
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(178, 142, 44, 0.4);
        }

        .footer-link {
            text-align: center;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #64748b;
        }

        .footer-link a {
            color: var(--uitm-purple);
            text-decoration: none;
            font-weight: 700;
            transition: var(--transition);
        }

        .footer-link a:hover {
            color: var(--uitm-gold);
        }

        .security-note {
            background: #fffbeb;
            border-left: 4px solid var(--uitm-gold);
            padding: 12px 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #92400e;
        }

        .security-note i {
            color: var(--uitm-gold);
        }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="admin-header">
            <div class="admin-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1>Admin Access</h1>
            <p>Authorized Personnel Only</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($_GET['error']); ?></span>
            </div>
        <?php endif; ?>

        <form action="admin_login_process.php" method="POST">
            <div class="input-group">
                <label>Admin ID</label>
                <i class="fas fa-user-shield"></i>
                <input type="text" name="admin_id" placeholder="Enter your Admin ID" required autofocus>
            </div>

            <div class="input-group">
                <label>Password</label>
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-sign-in-alt"></i> LOGIN AS ADMIN
            </button>
        </form>

        <div class="security-note">
            <i class="fas fa-info-circle"></i>
            <strong>Security Notice:</strong> This area is restricted to authorized administrators only. All access attempts are logged.
        </div>

        <div class="footer-link">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Back to User Login</a>
        </div>
    </div>
</body>
</html>