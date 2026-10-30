
<?php
session_start();
include "koneksi.php";

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare(
        $koneksi,
        "SELECT id, username, password FROM admin WHERE username = ?"
    );

    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);

    $hasil = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($hasil);

    // Memeriksa password hash atau password lama
    if (
        $admin &&
        (
            password_verify($password, $admin['password']) ||
            $password === $admin['password']
        )
    ) {
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['username'] = $admin['username'];

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .btn-kembali {
            display: block;
            text-align: center;
            margin-top: 15px;
            padding: 11px;
            border: 1px solid #16a34a;
            border-radius: 7px;
            color: #166534;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-kembali:hover {
            background: #dcfce7;
        }
    </style>
</head>

<body class="login-body">

    <div class="login-box">
        <h2>Login Admin</h2>
        <p>Silakan masuk ke sistem inventaris.</p>

        <?php if ($error != ""): ?>
            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input
                type="text"
                name="username"
                placeholder="Masukkan username"
                required
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Masukkan password"
                required
            >

            <button type="submit" name="login">Login</button>
        </form>

        <!-- Tombol kembali ke landing page -->
        <a href="index.php" class="btn-kembali">
            ← Kembali ke Landing Page
        </a>
    </div>

</body>
</html>
