<?php include 'config.php'; 

// Add Alternatif
if (isset($_POST['add_alt'])) {
    $nama = $_POST['nama'];
    $kode = $_POST['kode'];
    $detail = $_POST['detail'];
    $stmt = $pdo->prepare("INSERT INTO alternatif (nama, kode, detail) VALUES (?, ?, ?)");
    $stmt->execute([$nama, $kode, $detail]);
    $alt_id = $pdo->lastInsertId();
    // Auto input nilai kosong untuk kriteria existing
    $kr = $pdo->query("SELECT id FROM kriteria")->fetchAll();
    foreach ($kr as $k) {
        $pdo->prepare("INSERT INTO nilai (alternatif_id, kriteria_id, nilai) VALUES (?, ?, 0)")->execute([$alt_id, $k['id']]);
    }
}

// Edit Alternatif
if (isset($_POST['edit_alt'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $kode = $_POST['kode'];
    $detail = $_POST['detail'];
    $stmt = $pdo->prepare("UPDATE alternatif SET nama=?, kode=?, detail=? WHERE id=?");
    $stmt->execute([$nama, $kode, $detail, $id]);
}

// Input Nilai
if (isset($_POST['save_nilai'])) {
    $alt_id = $_POST['alt_id'];
    $kr_id = $_POST['kr_id'];
    $nilai = $_POST['nilai'];
    $stmt = $pdo->prepare("UPDATE nilai SET nilai=? WHERE alternatif_id=? AND kriteria_id=?");
    $stmt->execute([$nilai, $alt_id, $kr_id]);
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $pdo->prepare("DELETE FROM alternatif WHERE id=?")->execute([$id]);
    $pdo->prepare("DELETE FROM nilai WHERE alternatif_id=?")->execute([$id]);
}

$alternatif = $pdo->query("
    SELECT a.*, GROUP_CONCAT(CONCAT(k.nama, ': ', n.nilai) SEPARATOR ' | ') as nilai_str 
    FROM alternatif a 
    LEFT JOIN nilai n ON a.id = n.alternatif_id 
    LEFT JOIN kriteria k ON n.kriteria_id = k.id 
    GROUP BY a.id
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Alternatif</title><link rel="stylesheet" href="style.css"></head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <h2>Daftar Alternatif</h2>
        <form method="POST">
            <input type="hidden" name="add_alt" value="1">
            Nama: <input type="text" name="nama" required><br>
            Kode: <input type="text" name="kode" required><br>
            Detail: <textarea name="detail"></textarea><br>
            <input type="submit" value="Tambah Alternatif">
        </form>
        <table>
            <tr><th>Nama</th><th>Kode</th><th>Detail</th><th>Nilai (per Kriteria)</th><th>Aksi</th></tr>
            <?php foreach ($alternatif as $a): ?>
            <tr>
                <td><?= $a['nama'] ?></td>
                <td><?= $a['kode'] ?></td>
                <td><?= $a['detail'] ?></td>
                <td><?= $a['nilai_str'] ?: 'Belum diinput' ?></td>
                <td>
                    <!-- Form Edit Alternatif -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $a['id'] ?>">
                        <input type="text" name="nama" value="<?= $a['nama'] ?>" size="10">
                        <input type="text" name="kode" value="<?= $a['kode'] ?>" size="5">
                        <textarea name="detail" rows="2" cols="20"><?= $a['detail'] ?></textarea>
                        <input type="hidden" name="edit_alt" value="1">
                        <button type="submit">Edit</button>
                    </form>
                    <!-- Input Nilai (sederhana, expand untuk full) -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="alt_id" value="<?= $a['id'] ?>">
                        <?php $kr = $pdo->query("SELECT * FROM kriteria")->fetchAll(); foreach ($kr as $k): 
                            $nilai = $pdo->prepare("SELECT nilai FROM nilai WHERE alternatif_id=? AND kriteria_id=?")->execute([$a['id'], $k['id']])->fetchColumn();
                        ?>
                        <label><?= $k['nama'] ?>:</label><input type="number" name="nilai" value="<?= $nilai ?>" step="0.01" size="3">
                        <input type="hidden" name="kr_id" value="<?= $k['id'] ?>">
                        <?php endforeach; ?>
                        <input type="hidden" name="save_nilai" value="1">
                        <button type="submit">Simpan Nilai</button>
                    </form>
                    <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Hapus?')"><button>Hapus</button></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>