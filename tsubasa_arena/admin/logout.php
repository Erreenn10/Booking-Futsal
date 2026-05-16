<?php
// admin/logout.php - Logout Admin
session_start();

// Hapus semua session
session_destroy();

// Redirect ke halaman login admin
header("Location: login.php");
exit();
?>