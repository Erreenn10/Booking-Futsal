<?php
// admin/hapus_user.php - Hapus User
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Cek apakah user ini admin
$check = mysqli_query($conn, "SELECT role FROM users WHERE id = $id");
$user = mysqli_fetch_assoc($check);

if ($user && $user['role'] == 'admin') {
    // Jangan hapus admin
    header("Location: user.php?error=Tidak bisa menghapus admin");
    exit();
}

// Hapus user
$delete = "DELETE FROM users WHERE id = $id AND role = 'user'";
mysqli_query($conn, $delete);

header("Location: user.php");
exit();
?>