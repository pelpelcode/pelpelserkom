```php
<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

// Menambah admin baru
if (isset($_POST['tambah'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username == "" || $password == "") {
        $pesan = "Username dan password wajib diisi!";
    } elseif (strlen($password) < 8) {
        $pesan = "Password minimal 8 karakter!";
    } else {
        // Mengecek username
        $cek = mysqli_prepare(
            $koneksi,
            "SELECT id FROM admin WHERE username = ?"
        );

        mysqli_stmt_bind_param($cek, "s", $username);
        mysqli_stmt_execute($cek);
        $hasil = mysqli_stmt_get_result($cek);

        if (mysqli_num_rows($hasil) > 0) {
            $pesan = "Username sudah digunakan!";
        } else {
            // Mengamankan password dengan hash
            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = mysqli_prepare(
                $koneksi,
                "INSERT INTO admin (username, password)
                 VALUES (?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmt,
                "ss",
                $username,
                $passwordHash
            );

            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_stmt_close($cek);

                header("Location: admin.php?status=berhasil");
                exit;
            } else {
                $pesan = "Admin gagal ditambahkan!";
            }

            mysqli_stmt_close($stmt);
        }

        mysqli_stmt_close($cek);
    }
}

// Menghapus admin
if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];

    if ($id == $_SESSION['admin_id']) {
        $pesan = "Kamu tidak bisa menghapus akun sendiri!";
    } else {
        $stmt = mysqli_prepare(
            $koneksi,
            "DELETE FROM admin WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);

            header("Location: admin.php?status=dihapus");
            exit;
        } else {
            $pesan = "Admin gagal dihapus!";
        }

        mysqli_stmt_close($stmt);
    }
}

// Pesan status
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'berhasil') {
        $pesan = "Admin berhasil ditambahkan!";
    } elseif ($_GET['status'] == 'dihapus') {
        $pesan = "Admin berhasil dihapus!";
    }
}

// Mengambil daftar admin
$data = mysqli_query(
    $koneksi,
    "SELECT id, username FROM admin ORDER BY id ASC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>MARVEL</h1>

<nav class="navbar">
    <h2>InventarisKu</h2>
    <a href="logout.php" class="logout">Logout</a>
</nav>

<main class="container">
    <a href="dashboard.php" class="back">
        ← Kembali ke Dashboard
    </a>

    <h1>Kelola Admin</h1>
    <p>Tambah atau hapus akun admin melalui website.</p>

    <?php if ($pesan != ""): ?>
        <div class="alert success">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <h3>Tambah Admin Baru</h3>

        <form method="POST" class="form-barang">
            <label>Username</label>
            <input
                type="text"
                name="username"
                placeholder="Contoh: admin2"
                maxlength="50"
                required
            >

            <label>Password</label>
            <input
                type="password"
                name="password"
                placeholder="Minimal 8 karakter"
                minlength="8"
                required
            >

            <button type="submit" name="tambah">
                Tambah Admin
            </button>
        </form>
    </div>

    <div class="panel">
        <h3>Daftar Admin</h3>

        <table>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Aksi</th>
            </tr>

            <?php $no = 1; ?>

            <?php while ($row = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?= $no++ ?></td>

                <td>
                    <?= htmlspecialchars($row['username']) ?>

                    <?php if ($row['id'] == $_SESSION['admin_id']): ?>
                        <strong>(Kamu)</strong>
                    <?php endif; ?>
                </td>

                <td>
                    <?php if ($row['id'] != $_SESSION['admin_id']): ?>
                        <form
                            method="POST"
                            onsubmit="return confirm('Yakin hapus admin ini?')"
                        >
                            <input
                                type="hidden"
                                name="id"
                                value="<?= $row['id'] ?>"
                            >

                            <button
                                type="submit"
                                name="hapus"
                                class="btn-hapus"
                            >
                                Hapus
                            </button>
                        </form>
                    <?php else: ?>
                        Akun aktif
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

</body>
</html>
```