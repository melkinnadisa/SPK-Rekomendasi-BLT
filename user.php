<?php include 'config.php'; 

// Cek jika user adalah admin (opsional, tambahkan jika ada role check)
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Add User
if (isset($_POST['add'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $stmt = $pdo->prepare("INSERT INTO users (username, password, full_name, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$username, $password, $full_name, $role]);
}

// Edit User
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $role = $_POST['role'];
    $stmt = $pdo->prepare("UPDATE users SET username=?, full_name=?, role=? WHERE id=?");
    $stmt->execute([$username, $full_name, $role, $id]);
}

// Reset Password (opsional, jika ingin ubah password terpisah)
if (isset($_POST['reset_pass'])) {
    $id = $_POST['id'];
    $new_pass = md5($_POST['new_password']);
    $stmt = $pdo->prepare("UPDATE users SET password=? WHERE id=?");
    $stmt->execute([$new_pass, $id]);
}

// Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    if ($id != $_SESSION['user_id']) { // Jangan hapus diri sendiri
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
    }
}

$users = $pdo->query("SELECT * FROM users")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manajemen User</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <h2>Daftar User</h2>
        <form method="POST">
            <input type="hidden" name="add" value="1">
            Username: <input type="text" name="username" required><br>
            Password: <input type="password" name="password" required><br>
            Nama Lengkap: <input type="text" name="full_name" required><br>
            Role: <select name="role"><option value="admin">Admin</option><option value="user">User</option></select><br>
            <input type="submit" value="Tambah User">
        </form>
        <table>
            <tr><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Aksi</th></tr>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?= $u['username'] ?></td>
                <td><?= $u['full_name'] ?></td>
                <td><?= $u['role'] ?></td>
                <td>
                    <!-- Form Edit User -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <input type="text" name="username" value="<?= $u['username'] ?>" size="10">
                        <input type="text" name="full_name" value="<?= $u['full_name'] ?>" size="15">
                        <select name="role"><option <?= $u['role']=='admin'?'selected':'' ?> value="admin">Admin</option><option <?= $u['role']=='user'?'selected':'' ?> value="user">User</option></select>
                        <input type="hidden" name="edit" value="1">
                        <button type="submit">Edit</button>
                    </form>
                    <!-- Form Reset Password -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $u['id'] ?>">
                        <input type="password" name="new_password" placeholder="New Password" size="10" required>
                        <input type="hidden" name="reset_pass" value="1">
                        <button type="submit">Reset Pass</button>
                    </form>
                    <a href="?delete=<?= $u['id'] ?>" onclick="return confirm('Hapus user ini?')"><button>Hapus</button></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>