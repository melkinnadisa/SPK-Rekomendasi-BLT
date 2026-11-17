<?php
$host = 'localhost';
$db = 'spk_saw';
$user = 'root';
$pass = '';  // Kosong untuk XAMPP default

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

session_start();

// Cek login
if (basename($_SERVER['PHP_SELF']) !== 'login.php' && !isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
?>