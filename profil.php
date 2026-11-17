<?php 
include 'config.php'; 
$id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if ($_POST) {
    $full_name = $_POST['full_name'];
    $stmt = $pdo->prepare("UPDATE users SET full_name=? WHERE id=?");
    $stmt->execute([$full_name, $id]);
    header('Location: profil.php?success=1');
}
?>
<!DOCTYPE html>
<html lang="id">
<head><title>Profil</title><link rel="stylesheet" href="style.css"></head>
<body>
    <?php include 'nav.php'; // Buat file nav.php terpisah untuk menu umum ?>
    <div class="container">
        <h2>Profil User</h2>
        <?php if (isset($_GET['success'])) echo "<p style='color:green;'>Berhasil diupdate!</p>"; ?>
        <form method="POST">
            <label>Nama Lengkap:</label>
            <input type="text" name="full_name" value="<?= $user['full_name'] ?>" required>
            <input type="submit" value="Update">
        </form>
    </div>
</body>
</html>