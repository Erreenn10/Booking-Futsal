<?php
// admin/konfirmasi_booking.php - Proses Konfirmasi Booking
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';

$status = '';
switch ($action) {
    case 'confirm':
        $status = 'confirmed';
        break;
    case 'cancel':
        $status = 'canceled';
        break;
    case 'complete':
        $status = 'completed';
        break;
}

if ($id > 0 && $status) {
    $update = "UPDATE booking SET status = '$status' WHERE id = $id";
    mysqli_query($conn, $update);
    
    // Jika status confirmed, update jadwal
    if ($status == 'confirmed') {
        $booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM booking WHERE id = $id"));
        $tanggal = $booking['tanggal'];
        $jam = $booking['jam_mulai'];
        $id_lapangan = $booking['id_lapangan'];
        
        // Cek apakah sudah ada jadwal
        $cek = mysqli_query($conn, "SELECT id FROM jadwal WHERE id_lapangan = $id_lapangan AND tanggal = '$tanggal' AND jam = '$jam'");
        if (mysqli_num_rows($cek) == 0) {
            mysqli_query($conn, "INSERT INTO jadwal (id_lapangan, tanggal, jam, status, booking_id) 
                                 VALUES ($id_lapangan, '$tanggal', '$jam', 'booked', $id)");
        } else {
            mysqli_query($conn, "UPDATE jadwal SET status = 'booked', booking_id = $id 
                                 WHERE id_lapangan = $id_lapangan AND tanggal = '$tanggal' AND jam = '$jam'");
        }
    }
    
    // Jika status canceled, hapus dari jadwal atau set available
    if ($status == 'canceled') {
        $booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM booking WHERE id = $id"));
        mysqli_query($conn, "DELETE FROM jadwal WHERE booking_id = $id");
    }
}

header("Location: booking.php");
exit();
?>