<?php include 'config.php'; 
$hasil = $pdo->query("
    SELECT h.*, a.nama, a.kode FROM hasil_saw h 
    JOIN alternatif a ON h.alternatif_id = a.id 
    ORDER BY h.ranking
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Laporan</title><link rel="stylesheet" href="style.css"></head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <h2>Laporan SPK Metode SAW</h2>
        <button onclick="window.print()">Print Laporan</button>
        <table>
            <tr><th>No.</th><th>Kode</th><th>Nama</th><th>Ranking</th><th>Nilai Total</th></tr>
            <?php $no=1; foreach ($hasil as $h): ?>
            <tr><td><?= $no++ ?></td><td><?= $h['kode'] ?></td><td><?= $h['nama'] ?></td><td><?= $h['ranking'] ?></td><td><?= number_format($h['nilai_total'], 4) ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>
    <script> // CSS untuk print
    @media print { nav { display: none; } button { display: none; } }
    </script>
</body>
</html>