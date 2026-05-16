<?php
// admin/hapus_lapangan.php - Hapus Lapangan
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Ambil foto untuk dihapus
$query = "SELECT foto FROM lapangan WHERE id = $id";
$result = mysqli_query($conn, $query);
$lapangan = mysqli_fetch_assoc($result);

if ($lapangan) {
    // Hapus file foto
    if ($lapangan['foto'] && file_exists("../assets/uploads/lapangan/" . $lapangan['foto'])) {
        unlink("../assets/uploads/lapangan/" . $lapangan['foto']);
    }
    
    // Hapus dari database
    $delete = "DELETE FROM lapangan WHERE id = $id";
    mysqli_query($conn, $delete);
}

header("Location: lapangan.php");
exit();
?>