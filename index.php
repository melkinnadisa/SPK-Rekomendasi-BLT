<?php include 'config.php'; ?>
<!DOCTYPE html>
<html><head><title>Dashboard</title><link rel="stylesheet" href="style.css"></head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <h2>Selamat Datang, <?= $_SESSION['username'] ?>!</h2>
        <p>Sistem Pendukung Keputusan Rekomendasi Penerima Bantuan Langsung Tunai (BTL) Desa Jatirogo menggunakan Metode <strong>SAW</strong>.</p>
        <p><a href="perhitungan.php"><button>Mulai Perhitungan</button></a></p>
    </div>
</body></html>