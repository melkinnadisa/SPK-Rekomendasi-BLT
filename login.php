<?php
include 'config.php';
if (isset($_SESSION['user_id'])) header('Location: index.php');

$error = '';
if ($_POST) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->execute([$username, $password]);
    if ($user = $stmt->fetch()) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SPK BTL</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($error): ?><p class="error"><?= $error ?></p><?php endif; ?>
        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" value="admin" required><br>
            <label>Password:</label>
            <input type="password" name="password" value="admin" required><br>
            <button type="submit">Login</button>
        </form>
        <p><small>Default: admin / admin</small></p>
    </div>
</body>
</html>