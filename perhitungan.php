<?php include 'config.php'; 

// Hitung SAW (normalisasi & ranking)
if (isset($_POST['hitung'])) {
    // Hapus hasil lama
    $pdo->exec("TRUNCATE TABLE hasil_saw");
    
    $kr = $pdo->query("SELECT * FROM kriteria")->fetchAll();
    $alt = $pdo->query("SELECT * FROM alternatif")->fetchAll();
    
    $nilai_mat = []; // Matriks nilai
    foreach ($alt as $a) {
        $row = ['id' => $a['id'], 'nama' => $a['nama']];
        foreach ($kr as $k) {
            $val = $pdo->prepare("SELECT nilai FROM nilai WHERE alternatif_id=? AND kriteria_id=?")->execute([$a['id'], $k['id']])->fetchColumn();
            $row[$k['id']] = (float)$val;
        }
        $nilai_mat[] = $row;
    }
    
    // Normalisasi
    $normal_mat = [];
    foreach ($kr as $k) {
        $col = array_column($nilai_mat, $k['id']);
        $max_val = max($col);
        $min_val = min($col);
        foreach ($nilai_mat as $i => $row) {
            if ($k['tipe'] == 'benefit') {
                $normal_mat[$i][$k['id']] = $row[$k['id']] / $max_val;
            } else {
                $normal_mat[$i][$k['id']] = $min_val / $row[$k['id']];
            }
        }
    }
    
    // Bobot & Total
    $totals = [];
    foreach ($normal_mat as $i => $row) {
        $total = 0;
        foreach ($kr as $k) {
            $total += $row[$k['id']] * $k['bobot'];
        }
        $totals[$i] = $total;
    }
    
    // Ranking
    arsort($totals);
    $rank = 1;
    foreach ($totals as $i => $total) {
        $alt_id = $normal_mat[$i]['id'];
        $stmt = $pdo->prepare("INSERT INTO hasil_saw (alternatif_id, nilai_total, ranking) VALUES (?, ?, ?)");
        $stmt->execute([$alt_id, $total, $rank++]);
    }
    $success = "Perhitungan selesai!";
}

$hasil = $pdo->query("
    SELECT h.*, a.nama FROM hasil_saw h 
    JOIN alternatif a ON h.alternatif_id = a.id 
    ORDER BY h.ranking
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Perhitungan SAW</title><link rel="stylesheet" href="style.css"></head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <h2>Perhitungan SAW</h2>
        <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
        <form method="POST">
            <input type="submit" name="hitung" value="Hitung Ulang SAW">
        </form>
        <table>
            <tr><th>Ranking</th><th>Nama Alternatif</th><th>Nilai Total</th></tr>
            <?php foreach ($hasil as $h): ?>
            <tr><td><?= $h['ranking'] ?></td><td><?= $h['nama'] ?></td><td><?= number_format($h['nilai_total'], 4) ?></td></tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>