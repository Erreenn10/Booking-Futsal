<?php
// admin/refresh_captcha.php
session_start();
header('Content-Type: application/json');

$angka1 = rand(1, 20);
$angka2 = rand(1, 20);
$_SESSION['captcha_angka1'] = $angka1;
$_SESSION['captcha_angka2'] = $angka2;
$_SESSION['captcha_hasil'] = $angka1 + $angka2;

echo json_encode([
    'success' => true,
    'angka1' => $angka1,
    'angka2' => $angka2
]);
?>