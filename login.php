<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | UniVoice UiTM</title>
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
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; height: 100vh; overflow: hidden; }

        .login-container { display: flex; height: 100%; }


        .form-side { 
            flex: 1; 
            display: flex; 
            flex-direction: column; 
            justify-content: center; 
            padding: 0 10%; 
            background: #fff;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }

        .brand-header { margin-bottom: 2rem; }
        .brand-header img { 
            height: 90px; 
            width: auto; 
            filter: drop-shadow(0px 4px 8px rgba(0,0,0,0.1));
        }

        .form-header h1 { font-size: 2.2rem; color: var(--uitm-purple); font-weight: 800; margin-bottom: 0.5rem; }
        .form-header p { color: #64748b; margin-bottom: 1.5rem; }

        /* MODERN TOGGLE SWITCH */
        .role-toggle {
            display: flex;
            background: #f1f5f9;
            padding: 5px;
            border-radius: 12px;
            margin-bottom: 2rem;
            position: relative;
            width: 100%;
        }

        .role-toggle label {
            flex: 1;
            text-align: center;
            padding: 12px;
            cursor: pointer;
            font-weight: 700;
            font-size: 0.9rem;
            color: #64748b;
            z-index: 2;
            transition: var(--transition);
        }

        .role-toggle input { display: none; }

        .role-toggle .slider {
            position: absolute;
            top: 5px;
            left: 5px;
            width: calc(50% - 5px);
            height: calc(100% - 10px);
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: var(--transition);
            z-index: 1;
        }

        /* Toggle Logic */
        #student-radio:checked ~ .slider { left: 5px; }
        #staff-radio:checked ~ .slider { left: calc(50%); }
        #student-radio:checked ~ label[for="student-radio"] { color: var(--uitm-purple); }
        #staff-radio:checked ~ label[for="staff-radio"] { color: var(--uitm-purple); }

        .input-group { margin-bottom: 1.2rem; position: relative; }
        .input-group label { display: block; font-weight: 700; font-size: 0.85rem; margin-bottom: 8px; color: var(--text-dark); }
        .input-group i { position: absolute; left: 15px; top: 42px; color: #94a3b8; z-index: 2; }
        .input-group input { 
            width: 100%; padding: 14px 12px 14px 45px; 
            border: 1.5px solid #e2e8f0; border-radius: 12px; 
            font-family: inherit; transition: var(--transition);
            background: #fcfdfe;
        }
        .input-group input:focus { 
            border-color: var(--uitm-purple); 
            background: #fff;
            outline: none; 
            box-shadow: 0 0 0 4px rgba(61, 10, 145, 0.1); 
        }

        .btn-submit { 
            width: 100%; padding: 16px; background: var(--uitm-purple); 
            color: white; border: none; border-radius: 12px; 
            font-weight: 700; cursor: pointer; transition: var(--transition);
            font-size: 1rem; letter-spacing: 0.5px; margin-top: 10px;
        }
        .btn-submit:hover { background: #2a0766; transform: translateY(-2px); box-shadow: 0 8px 15px rgba(61, 10, 145, 0.2); }

        .footer-link { text-align: center; margin-top: 2rem; font-size: 0.9rem; color: #64748b; }
        .footer-link a { color: var(--uitm-purple); text-decoration: none; font-weight: 700; }

        /* Right Side: Aesthetics */
        .image-side { 
            flex: 1.3; 
            background: linear-gradient(rgba(61, 10, 145, 0.8), rgba(61, 10, 145, 0.6)), url('images/uitm 1.png');
            background-size: cover; background-position: center;
            display: flex; align-items: center; justify-content: center; color: #fff;
        }
        .overlay-content { text-align: center; max-width: 80%; }
        .overlay-content h2 { font-size: 3.5rem; font-weight: 800; margin-bottom: 1rem; }
        .line { width: 80px; height: 6px; background: var(--uitm-gold); margin: 0 auto 2rem; border-radius: 10px; }

        @media (max-width: 900px) { .image-side { display: none; } .form-side { padding: 0 8%; } }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="form-side">
            <div class="brand-header">
                <img src="images/logo.png" alt="UiTM Logo">
            </div>

            <div class="form-header">
                <h1>Welcome Back</h1>
                <p>Please select your role and enter your details.</p>
            </div>

            <form action="login_process.php" method="POST">
                <div class="role-toggle">
                    <input type="radio" name="role" id="student-radio" value="student" checked>
                    <label for="student-radio">Student</label>
                    
                    <input type="radio" name="role" id="staff-radio" value="staff">
                    <label for="staff-radio">Staff</label>
                    
                    <div class="slider"></div>
                </div>

                <div class="input-group">
                    <label id="id-label">Student ID</label>
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="user_id" placeholder="e.g. 2024123456" required>
                </div>

                <div class="input-group">
                    <label>Password</label>
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">SIGN IN</button>
            </form>

            <div class="footer-link" style="margin-top: 1.5rem;">
                <a href="admin_login.php" style="display: inline-block; padding: 12px 30px; background: var(--uitm-gold); color: white; text-decoration: none; border-radius: 10px; font-weight: 700; transition: var(--transition); box-shadow: 0 4px 10px rgba(178, 142, 44, 0.3);" onmouseover="this.style.background='#9a7424'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 15px rgba(178, 142, 44, 0.4)';" onmouseout="this.style.background='var(--uitm-gold)'; this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 10px rgba(178, 142, 44, 0.3)';">
                    <i class="fas fa-user-shield"></i> Admin Login
                </a>
            </div>

            <div class="footer-link">
                Don't have an account? <a href="register.php">Register here</a><br><br>
                <a href="index.php"><i class="fas fa-arrow-left"></i> Back to Homepage</a>
            </div>
        </div>

        <div class="image-side">
            <div class="overlay-content">
                <h2>UniVoice</h2>
                <div class="line"></div>
                <p style="font-size: 1.4rem; font-weight: 400; line-height: 1.6;">
                    Providing a formal channel for <br>
                    <strong>UiTM Cawangan Kelantan</strong> students to be heard.
                </p>
            </div>
        </div>
    </div>

    <script>

        const studentRadio = document.getElementById('student-radio');
        const staffRadio = document.getElementById('staff-radio');
        const idLabel = document.getElementById('id-label');
        const idInput = document.querySelector('input[name="user_id"]');

        studentRadio.addEventListener('change', () => {
            idLabel.innerText = "Student ID";
            idInput.placeholder = "e.g. 2024123456";
        });

        staffRadio.addEventListener('change', () => {
            idLabel.innerText = "Staff ID";
            idInput.placeholder = "e.g. STAFF9988";
        });
    </script>
</body>
</html>