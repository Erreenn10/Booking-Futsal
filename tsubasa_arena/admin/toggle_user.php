<?php
// admin/toggle_user.php - Aktifkan / Nonaktifkan User
session_start();
include '../config/database.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'activate') {
    mysqli_query($conn, "UPDATE users SET is_active = 1 WHERE id = $id");
} elseif ($action == 'deactivate') {
    mysqli_query($conn, "UPDATE users SET is_active = 0 WHERE id = $id");
}

header("Location: user.php");
exit();
?>