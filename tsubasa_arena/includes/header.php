<?php
// includes/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Tsubasa Arena' ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f8f9fa;
            color: #1a2a3a;
        }
        
        /* ========== TEMA BLUE LOCK - TSUBASA ARENA ========== */
        :root {
            --blue-dark: #0a2b4e;
            --blue-medium: #1e4d7c;
            --blue-light: #3a6b92;
            --red-accent: #e63946;
            --white: #ffffff;
            --gray-bg: #f8f9fa;
            --gray-border: #e0e0e0;
            --text-dark: #1a2a3a;
        }
        
        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
        }
        
        /* ========== NAVBAR ========== */
        .navbar {
            background: var(--blue-dark);
            padding: 16px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .logo h1 {
            font-size: 28px;
            color: var(--white);
            letter-spacing: 1px;
        }
        
        .logo span {
            color: var(--red-accent);
            font-size: 14px;
            background: rgba(255,255,255,0.1);
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: 8px;
        }
        
        .logo p {
            font-size: 10px;
            color: rgba(255,255,255,0.7);
            letter-spacing: 1px;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            gap: 28px;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .nav-links a:hover {
            color: var(--red-accent);
        }
        
        .btn-login {
            background: var(--red-accent);
            color: white;
            padding: 8px 20px;
            border-radius: 30px;
            font-weight: 600;
        }
        
        .btn-login:hover {
            background: #c1121f;
            color: white;
        }
        
        /* ========== BUTTON ========== */
        .btn-primary {
            background: var(--blue-medium);
            color: white;
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 30px;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-primary:hover {
            background: var(--blue-dark);
            transform: translateY(-2px);
        }
        
        .btn-accent {
            background: var(--red-accent);
            color: white;
            border: none;
            padding: 12px 24px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border-radius: 30px;
        }
        
        .btn-accent:hover {
            background: #c1121f;
            transform: translateY(-2px);
        }
        
        /* ========== FOOTER ========== */
        footer {
            background: var(--blue-dark);
            color: white;
            padding: 40px 0 24px;
            margin-top: 60px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 32px;
            margin-bottom: 32px;
        }
        
        .footer-col h4 {
            color: var(--red-accent);
            margin-bottom: 16px;
        }
        
        .footer-col p, .footer-col a {
            font-size: 13px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            line-height: 1.6;
        }
        
        .footer-col a:hover {
            color: var(--red-accent);
        }
        
        .copyright {
            text-align: center;
            padding-top: 24px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .nav-container {
                flex-direction: column;
            }
            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <!-- NAVBAR TSUBASA ARENA -->
    <div class="navbar">
        <div class="nav-container">
            <div class="logo">
                <h1>⚡ Tsubasa <span>Arena</span></h1>
                <p>BOOKING LAPANGAN FUTSAL</p>
            </div>
            <ul class="nav-links">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="lapangan.php">Lapangan</a></li>
                <li><a href="riwayat.php">Riwayat Booking</a></li>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <li><span style="color: var(--red-accent);">👤 <?= $_SESSION['user_nama'] ?? 'User' ?></span></li>
                    <li><a href="logout.php" class="btn-login">Logout</a></li>
                <?php else: ?>
                    <li><a href="login.php" class="btn-login">Login / Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>