<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account | UniVoice</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --uitm-purple: #3d0a91; 
            --uitm-gold: #b28e2c; 
            --bg-body: #f3f4f6;
            --error-red: #e11d48;
            --success-green: #10b981;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: var(--bg-body); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; padding: 20px; }

        .register-card {
            background: #fff; width: 100%; max-width: 550px;
            padding: 2.5rem; border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .reg-header { 
            text-align: center;
            margin-bottom: 2rem; }

        .reg-header img { 
            height: 75px;
            margin-bottom: 12px; 
            filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.08)); }

        .reg-header h1 { color: var(--uitm-purple); font-size: 1.8rem; font-weight: 800; }

        .role-selector {
            display: flex; background: #f1f5f9; padding: 6px; border-radius: 14px;
            margin-bottom: 2rem; position: relative;
        }
        .role-selector label {
            flex: 1; text-align: center; padding: 12px; cursor: pointer;
            font-weight: 700; font-size: 0.9rem; color: #64748b; z-index: 2; transition: var(--transition);
        }
        .role-selector input { display: none; }

        .role-selector .slider {
            position: absolute; top: 6px; left: 6px; width: calc(50% - 6px); height: calc(100% - 12px);
            background: white; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); transition: var(--transition); z-index: 1;
        }
        #student:checked ~ .slider { left: 6px; }
        #staff:checked ~ .slider { left: 50%; }
        #student:checked ~ label[for="student"], #staff:checked ~ label[for="staff"] { color: var(--uitm-purple); }

        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .field { margin-bottom: 1.25rem; position: relative; }
        .field label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; color: #334155; }
        
        .input-wrapper { position: relative; }
        .input-wrapper i.prefix-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 0.9rem; }
        
        .field input, .field select {
            width: 100%; padding: 12px 15px 12px 40px; border: 1.5px solid #e2e8f0;
            border-radius: 12px; font-family: inherit; font-size: 0.95rem; transition: var(--transition);
            background: #f8fafc;
        }
        
        .field input:focus { border-color: var(--uitm-purple); outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(61, 10, 145, 0.08); }
        
        .input-wrapper.error input { border-color: var(--error-red); background: #fff1f2; }
        .input-wrapper.success input { border-color: var(--success-green); }
        .match-error { color: var(--error-red); font-size: 0.75rem; font-weight: 700; margin-top: 5px; display: none; }

        .btn-reg {
            width: 100%; padding: 16px; background: var(--uitm-purple);
            color: white; border: none; border-radius: 14px;
            font-weight: 700; font-size: 1rem; cursor: pointer; transition: var(--transition); margin-top: 1rem;
            display: flex; justify-content: center; align-items: center; gap: 10px;
        }
        .btn-reg:hover { background: #2a0766; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(61, 10, 145, 0.2); }
        .btn-reg:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .back-login { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; color: #64748b; }
        .back-login a { color: var(--uitm-purple); text-decoration: none; font-weight: 700; }

        @media (max-width: 480px) { .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="reg-header">
            <img src="images/logo.png" alt="UiTM Logo">
            <h1>Create Account</h1>
            <p style="color: #64748b; font-size: 0.9rem;">Join the official UniVoice portal</p>
        </div>

        <form action="register_process.php" method="POST" id="regForm">
            <div class="role-selector">
                <input type="radio" name="role" id="student" value="student" checked>
                <label for="student">Student</label>
                <input type="radio" name="role" id="staff" value="staff">
                <label for="staff">Staff</label>
                <div class="slider"></div>
            </div>

            <div class="field">
                <label>Full Name</label>
                <div class="input-wrapper">
                    <i class="fas fa-user prefix-icon"></i>
                    <input type="text" name="name" placeholder="Name as per MyKad" required>
                </div>
            </div>
            
            <div class="form-grid">
                <div class="field">
                    <label id="id-label">Student ID</label>
                    <div class="input-wrapper">
                        <i class="fas fa-id-badge prefix-icon"></i>
                        <input type="text" name="user_id" id="user_id" placeholder="202XXXXXX" required>
                    </div>
                </div>
                <div class="field">
                    <label>Campus</label>
                    <div class="input-wrapper">
                        <i class="fas fa-map-marker-alt prefix-icon"></i>
                        <select name="campus">
                            <option value="Machang">Machang</option>
                            <option value="Kota Bharu">Kota Bharu</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="field">
                <label>University Email</label>
                <div class="input-wrapper">
                    <i class="fas fa-envelope prefix-icon"></i>
                    <input type="email" name="email" placeholder="example@uitm.edu.my" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="field">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock prefix-icon"></i>
                        <input type="password" name="password" id="password" placeholder="8+ chars" required>
                    </div>
                </div>
                <div class="field">
                    <label>Confirm Password</label>
                    <div class="input-wrapper" id="retype-wrapper">
                        <i class="fas fa-key prefix-icon"></i>
                        <input type="password" name="retype_password" id="retype_password" placeholder="Repeat it" required>
                    </div>
                    <span id="error-text" class="match-error">Passwords do not match!</span>
                </div>
            </div>

            <button type="submit" class="btn-reg" id="submit-btn">
                <span>REGISTER ACCOUNT</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="back-login">
            Already have an account? <a href="login.php">Log in here</a>
        </div>
    </div>

    <script>

        const studentRadio = document.getElementById('student');
        const staffRadio = document.getElementById('staff');
        const idLabel = document.getElementById('id-label');
        const idInput = document.getElementById('user_id');

        studentRadio.addEventListener('change', () => {
            idLabel.innerText = "Student ID";
            idInput.placeholder = "202XXXXXX";
        });

        staffRadio.addEventListener('change', () => {
            idLabel.innerText = "Staff ID";
            idInput.placeholder = "STAFFXXXX";
        });

        const pass = document.getElementById('password');
        const retype = document.getElementById('retype_password');
        const errorText = document.getElementById('error-text');
        const wrapper = document.getElementById('retype-wrapper');
        const btn = document.getElementById('submit-btn');

        function validatePassword() {
            const val1 = pass.value;
            const val2 = retype.value;

            if (val2.length > 0) {
                if (val1 !== val2) {
                    errorText.style.display = 'block';
                    wrapper.classList.add('error');
                    wrapper.classList.remove('success');
                    btn.disabled = true;
                } else {
                    errorText.style.display = 'none';
                    wrapper.classList.remove('error');
                    wrapper.classList.add('success');
                    btn.disabled = false;
                }
            } else {
                errorText.style.display = 'none';
                wrapper.classList.remove('error', 'success');
                btn.disabled = false;
            }
        }

        retype.addEventListener('input', validatePassword);
        pass.addEventListener('input', validatePassword);
    </script>
</body>
</html>