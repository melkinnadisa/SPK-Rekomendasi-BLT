<?php include 'config.php'; 
if (isset($_POST['add'])) {
    $pdo->prepare("INSERT INTO kriteria (kode, nama, tipe, bobot) VALUES (?, ?, ?, ?)")
        ->execute([$_POST['kode'], $_POST['nama'], $_POST['tipe'], $_POST['bobot']]);
}
if (isset($_POST['edit'])) {
    $pdo->prepare("UPDATE kriteria SET kode=?, nama=?, tipe=?, bobot=? WHERE id=?")
        ->execute([$_POST['kode'], $_POST['nama'], $_POST['tipe'], $_POST['bobot'], $_POST['id']]);
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM kriteria WHERE id=?")->execute([$_GET['delete']]);
}
$kriteria = $pdo->query("SELECT * FROM kriteria")->fetchAll();
?>
<!DOCTYPE html><html><head><title>Kriteria</title><link rel="stylesheet" href="style.css"></head>
<body><?php include 'nav.php'; ?>
<div class="container">
    <h2>Daftar Kriteria</h2>
    <form method="POST">
        <input type="hidden" name="add" value="1">
        Kode: <input name="kode" size="5" required>
        Nama: <input name="nama" required>
        Tipe: <select name="tipe"><option>benefit</option><option>cost</option></select>
        Bobot: <input name="bobot" type="number" step="0.001" required>
        <button>Tambah</button>
    </form>
    <table>
        <tr><th>Kode</th><th>Nama</th><th>Tipe</th><th>Bobot</th><th>Aksi</th></tr>
        <?php foreach ($kriteria as $k): ?>
        <tr>
            <form method="POST">
            <td><input name="kode" value="<?= $k['kode'] ?>" size="3"></td>
            <td><input name="nama" value="<?= $k['nama'] ?>"></td>
            <td><select name="tipe"><option <?= $k['tipe']=='benefit'?'selected':'' ?>>benefit</option><option <?= $k['tipe']=='cost'?'selected':'' ?>>cost</option></select></td>
            <td><input name="bobot" value="<?= $k['bobot'] ?>" size="5"></td>
            <td>
                <input type="hidden" name="id" value="<?= $k['id'] ?>">
                <input type="hidden" name="edit" value="1">
                <button>Edit</button>
            </form>
            <a href="?delete=<?= $k['id'] ?>" onclick="return confirm('Hapus?')"><button style="background:red;">Hapus</button></a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>
</body></html>