```php
<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

$pesan = "";

// Menambah kategori
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama_kategori']);

    if ($nama != "") {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO kategori (nama_kategori) VALUES (?)"
        );

        mysqli_stmt_bind_param($stmt, "s", $nama);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: kategori.php");
    exit;
}

// Mengedit kategori
if (isset($_POST['edit'])) {
    $id = (int) $_POST['id'];
    $nama = trim($_POST['nama_kategori']);

    if ($nama != "") {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE kategori SET nama_kategori = ? WHERE id = ?"
        );

        mysqli_stmt_bind_param($stmt, "si", $nama, $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: kategori.php");
    exit;
}

// Menghapus kategori
if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];

    $stmt = mysqli_prepare(
        $koneksi,
        "DELETE FROM kategori WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: kategori.php");
    exit;
}

// Mengambil data kategori
$data = mysqli_query(
    $koneksi,
    "SELECT * FROM kategori ORDER BY id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Kategori</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .aksi {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-edit {
            background: #eab308;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-hapus {
            background: #dc2626;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-edit {
            display: none;
            margin-top: 8px;
        }

        .form-edit input {
            padding: 8px;
            max-width: 180px;
        }
    </style>
</head>
<body>

<nav class="navbar">
    <h2>InventarisKu</h2>
    <a href="logout.php" class="logout">Logout</a>
</nav>

<main class="container">
    <a href="dashboard.php" class="back">
        ← Kembali ke Dashboard
    </a>

    <h1>Data Kategori</h1>
    <p>Tambahkan, edit, atau hapus kategori sesuai kebutuhan.</p>

    <div class="panel">
        <h3>Tambah Kategori</h3>

        <form method="POST" class="form-row">
            <input
                type="text"
                name="nama_kategori"
                placeholder="Contoh: Elektronik"
                required
            >
            <button type="submit" name="tambah">Tambah</button>
        </form>
    </div>

    <div class="panel">
        <h3>Daftar Kategori</h3>

        <table>
            <tr>
                <th>No</th>
                <th>Nama Kategori</th>
                <th>Aksi</th>
            </tr>

            <?php $no = 1; ?>

            <?php while ($row = mysqli_fetch_assoc($data)): ?>
            <tr>
                <td><?= $no++ ?></td>

                <td>
                    <?= htmlspecialchars($row['nama_kategori']) ?>

                    <form
                        method="POST"
                        class="form-edit"
                        id="edit-<?= $row['id'] ?>"
                    >
                        <input
                            type="hidden"
                            name="id"
                            value="<?= $row['id'] ?>"
                        >

                        <input
                            type="text"
                            name="nama_kategori"
                            value="<?= htmlspecialchars($row['nama_kategori']) ?>"
                            required
                        >

                        <button type="submit" name="edit" class="btn-edit">
                            Simpan
                        </button>

                        <button
                            type="button"
                            class="btn-hapus"
                            onclick="tutupEdit(<?= $row['id'] ?>)"
                        >
                            Batal
                        </button>
                    </form>
                </td>

                <td>
                    <div class="aksi">
                        <button
                            type="button"
                            class="btn-edit"
                            onclick="bukaEdit(<?= $row['id'] ?>)"
                        >
                            Edit
                        </button>

                        <form
                            method="POST"
                            onsubmit="return confirm('Yakin hapus kategori ini?')"
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
                    </div>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</main>

<script>
function bukaEdit(id) {
    document.getElementById("edit-" + id).style.display = "block";
}

function tutupEdit(id) {
    document.getElementById("edit-" + id).style.display = "none";
}
</script>

</body>
</html>
```