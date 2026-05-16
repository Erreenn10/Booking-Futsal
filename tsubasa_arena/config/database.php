<?php
// config/database.php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'tsubasa_arena_db';

$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");

function query($sql) {
    global $conn;
    return mysqli_query($conn, $sql);
}

function fetch($result) {
    return mysqli_fetch_assoc($result);
}

function fetchAll($result) {
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>