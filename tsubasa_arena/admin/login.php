<?php
// admin/login.php - Halaman login ADMIN dengan CAPTCHA AJAX
session_start();

// Jika sudah login, redirect ke dashboard admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit();
}

include '../config/database.php';

// Generate CAPTCHA matematika
function generateCaptcha() {
    $angka1 = rand(1, 20);
    $angka2 = rand(1, 20);
    $_SESSION['captcha_angka1'] = $angka1;
    $_SESSION['captcha_angka2'] = $angka2;
    $_SESSION['captcha_hasil'] = $angka1 + $angka2;
    return [$angka1, $angka2];
}

// Cek apakah CAPTCHA sudah ada di session
if (!isset($_SESSION['captcha_angka1'])) {
    generateCaptcha();
}

$angka1 = $_SESSION['captcha_angka1'];
$angka2 = $_SESSION['captcha_angka2'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);
    $captcha = (int)$_POST['captcha'];
    
    // Validasi CAPTCHA
    if ($captcha != $_SESSION['captcha_hasil']) {
        $error = "❌ Kode keamanan salah!";
        generateCaptcha();
        $angka1 = $_SESSION['captcha_angka1'];
        $angka2 = $_SESSION['captcha_angka2'];
    } else {
        // Cek admin berdasarkan username
        $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND role = 'admin'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $admin = mysqli_fetch_assoc($result);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nama'] = $admin['nama'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['login_success'] = true;
            
            // Reset CAPTCHA
            generateCaptcha();
            
            header("Location: index.php");
            exit();
        } else {
            $error = "❌ Username atau password salah!";
            generateCaptcha();
            $angka1 = $_SESSION['captcha_angka1'];
            $angka2 = $_SESSION['captcha_angka2'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Tsubasa Arena</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('https://images.unsplash.com/photo-1522778119026-d647f0594c0a?w=1920&h=1080&fit=crop');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(10,43,78,0.85) 0%, rgba(30,77,124,0.85) 100%);
            z-index: 0;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 24px;
            box-shadow: 0 25px 40px -12px rgba(0,0,0,0.4);
            overflow: hidden;
            width: 420px;
            max-width: 90%;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255,255,255,0.2);
            transition: transform 0.3s;
        }
        
        .login-container:hover {
            transform: translateY(-5px);
        }
        
        .login-header {
            background: linear-gradient(135deg, #0a2b4e 0%, #1e4d7c 100%);
            color: white;
            text-align: center;
            padding: 30px 25px;
            position: relative;
        }
        
        .admin-icon {
            font-size: 45px;
            margin-bottom: 8px;
        }
        
        .login-header h1 {
            font-size: 26px;
            margin-bottom: 4px;
            letter-spacing: 2px;
        }
        
        .login-header p {
            font-size: 12px;
            opacity: 0.85;
        }
        
        .login-body {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 6px;
            color: #333;
            font-weight: 600;
            font-size: 12px;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon input {
            width: 100%;
            padding: 12px 15px 12px 42px;
            border: 2px solid #e1e1e1;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s;
            background: white;
        }
        
        .input-icon .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
        }
        
        .input-icon input:focus {
            outline: none;
            border-color: #e63946;
            box-shadow: 0 0 0 3px rgba(230,57,70,0.1);
        }
        
        .captcha-group {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid #e1e1e1;
        }
        
        .captcha-question {
            font-size: 22px;
            font-weight: bold;
            background: #0a2b4e;
            color: white;
            display: inline-block;
            padding: 6px 18px;
            border-radius: 35px;
            margin-bottom: 10px;
        }
        
        .captcha-group input {
            text-align: center;
            padding: 10px;
            font-size: 15px;
            width: 100%;
            border: 2px solid #ddd;
            border-radius: 10px;
        }
        
        .login-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #e63946 0%, #c1121f 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 8px;
            letter-spacing: 1px;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(230,57,70,0.4);
        }
        
        .refresh-captcha {
            background: #0a2b4e;
            color: white;
            width: auto;
            padding: 6px 18px;
            margin-top: 10px;
            font-size: 12px;
            cursor: pointer;
            border-radius: 25px;
            border: none;
            transition: all 0.3s;
        }
        
        .refresh-captcha:hover {
            background: #1e4d7c;
        }
        
        .refresh-captcha:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .login-footer {
            text-align: center;
            padding: 18px;
            background: rgba(0,0,0,0.02);
            font-size: 11px;
            color: #888;
            border-top: 1px solid #eee;
        }
        
        .alert-error {
            background: #fee;
            color: #e63946;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 18px;
            text-align: center;
            border-left: 3px solid #e63946;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="admin-icon">⚡⚽🏟️</div>
            <h1>TSUBASA ARENA</h1>
            <p>ADMIN PANEL</p>
        </div>
        
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert-error">
                    <?= $error ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="loginForm">
                <div class="form-group">
                    <label>👤 USERNAME</label>
                    <div class="input-icon">
                        <span class="icon">👤</span>
                        <input type="text" name="username" id="username" required placeholder="Masukkan username" autocomplete="off">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🔒 PASSWORD</label>
                    <div class="input-icon">
                        <span class="icon">🔑</span>
                        <input type="password" name="password" id="password" required placeholder="Masukkan password">
                    </div>
                </div>
                
                <div class="form-group">
                    <label>🔐 CAPTCHA</label>
                    <div class="captcha-group">
                        <div class="captcha-question" id="captchaQuestion">
                            <?= $angka1 ?> + <?= $angka2 ?> = ?
                        </div>
                        <input type="number" name="captcha" id="captcha" required placeholder="Hasil penjumlahan">
                        <button type="button" class="refresh-captcha" id="refreshCaptcha">
                            🔄 Refresh Captcha
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="login-btn" id="submitBtn">⚡ LOGIN ⚡</button>
            </form>
        </div>
        
        <div class="login-footer">
            <p>⚡ Tsubasa Arena – Admin Dashboard ⚡</p>
        </div>
    </div>
    
    <script>
        // Refresh CAPTCHA dengan AJAX
        const refreshBtn = document.getElementById('refreshCaptcha');
        const captchaQuestion = document.getElementById('captchaQuestion');
        const captchaInput = document.getElementById('captcha');
        
        refreshBtn.addEventListener('click', async function() {
            const originalText = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '🔄 Loading...';
            refreshBtn.disabled = true;
            
            try {
                const response = await fetch('refresh_captcha.php');
                const data = await response.json();
                
                if (data.success) {
                    captchaQuestion.innerHTML = data.angka1 + ' + ' + data.angka2 + ' = ?';
                    captchaInput.value = '';
                    captchaQuestion.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        captchaQuestion.style.transform = 'scale(1)';
                    }, 200);
                } else {
                    alert('Gagal mengganti soal, silakan reload halaman.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Terjadi kesalahan, silakan reload halaman.');
            } finally {
                refreshBtn.innerHTML = originalText;
                refreshBtn.disabled = false;
            }
        });
    </script>
</body>
</html>