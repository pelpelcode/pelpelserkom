
<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// MENAMBAH BARANG
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama_barang']);
    $jumlah = (int) $_POST['jumlah'];
    $kategori_id = (int) $_POST['kategori_id'];

    if ($nama != "" && $jumlah >= 0 && $kategori_id > 0) {
        $stmt = mysqli_prepare(
            $koneksi,
            "INSERT INTO barang (nama_barang, jumlah, kategori_id)
             VALUES (?, ?, ?)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "sii",
            $nama,
            $jumlah,
            $kategori_id
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: barang.php");
    exit;
}

// MENGEDIT BARANG
if (isset($_POST['edit'])) {
    $id = (int) $_POST['id'];
    $nama = trim($_POST['nama_barang']);
    $jumlah = (int) $_POST['jumlah'];
    $kategori_id = (int) $_POST['kategori_id'];

    if ($nama != "" && $jumlah >= 0 && $kategori_id > 0) {
        $stmt = mysqli_prepare(
            $koneksi,
            "UPDATE barang
             SET nama_barang = ?, jumlah = ?, kategori_id = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "siii",
            $nama,
            $jumlah,
            $kategori_id,
            $id
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    header("Location: barang.php");
    exit;
}

// MENGHAPUS BARANG
if (isset($_POST['hapus'])) {
    $id = (int) $_POST['id'];

    $stmt = mysqli_prepare(
        $koneksi,
        "DELETE FROM barang WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: barang.php");
    exit;
}

// MENGAMBIL DATA KATEGORI
$kategori = mysqli_query(
    $koneksi,
    "SELECT * FROM kategori ORDER BY nama_kategori ASC"
);

// MENYIMPAN DATA KATEGORI AGAR BISA DIPAKAI BERULANG
$daftarKategori = [];

while ($kat = mysqli_fetch_assoc($kategori)) {
    $daftarKategori[] = $kat;
}

// MENGAMBIL BARANG BESERTA KATEGORINYA
$data = mysqli_query(
    $koneksi,
    "SELECT barang.*, kategori.nama_kategori
     FROM barang
     LEFT JOIN kategori
     ON barang.kategori_id = kategori.id
     ORDER BY barang.id DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Barang - InventarisKu</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5faf6;
            color: #1f2937;
        }

        .navbar {
            background: #166534;
            color: white;
            padding: 18px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .navbar h2 {
            font-size: 24px;
        }

        .navbar span {
            color: #bbf7d0;
        }

        .logout {
            background: #dc2626;
            color: white;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 7px;
        }

        .container {
            max-width: 1100px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .back {
            display: inline-block;
            margin-bottom: 22px;
            color: #15803d;
            text-decoration: none;
            font-weight: bold;
        }

        .container h1 {
            margin-bottom: 10px;
            font-size: 30px;
        }

        .description {
            color: #64748b;
            margin-bottom: 25px;
        }

        .panel {
            background: white;
            border: 1px solid #dcfce7;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 12px #1665340a;
        }

        .panel h3 {
            color: #166534;
            margin-bottom: 22px;
            font-size: 20px;
        }

        .form-barang {
            display: grid;
            gap: 10px;
        }

        .form-barang label {
            font-weight: bold;
            font-size: 14px;
            margin-top: 5px;
        }

        .form-barang input,
        .form-barang select {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
            background: white;
        }

        .btn-simpan {
            background: #16a34a;
            color: white;
            border: none;
            padding: 13px;
            border-radius: 7px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 12px;
        }

        .table-scroll {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th, td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #166534;
            color: white;
            font-size: 14px;
        }

        td {
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f0fdf4;
        }

        .badge-kategori {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-hapus {
            background: #dc2626;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-edit {
            background: #eab308;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
        }

        .aksi {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .form-edit {
            display: none;
            min-width: 230px;
            padding: 12px;
            background: #f0fdf4;
            border-radius: 8px;
        }

        .form-edit input,
        .form-edit select {
            width: 100%;
            padding: 9px;
            margin-bottom: 10px;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
        }

        .form-edit label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        .footer {
            text-align: center;
            color: #64748b;
            padding: 25px;
            font-size: 13px;
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 16px 20px;
            }

            .navbar h2 {
                font-size: 20px;
            }

            .container {
                margin-top: 25px;
                padding: 0 15px;
            }

            .container h1 {
                font-size: 25px;
            }

            .panel {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <h2>Inventaris<span>Ku</span></h2>
    <a href="logout.php" class="logout"
       onclick="return confirm('Yakin ingin logout?')">
        Logout
    </a>
</nav>

<main class="container">

    <a href="dashboard.php" class="back">
        ← Kembali ke Dashboard
    </a>

    <h1>Data Barang</h1>
    <p class="description">
        Tambah, edit, hapus, dan kelola inventaris barang.
    </p>

    <!-- FORM TAMBAH BARANG -->
    <div class="panel">
        <h3>+ Tambah Barang</h3>

        <form method="POST" class="form-barang">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" id="nama_barang"
                   name="nama_barang"
                   placeholder="Masukkan nama barang" required>

            <label for="jumlah">Jumlah Barang</label>
            <input type="number" id="jumlah" name="jumlah"
                   min="0" placeholder="Masukkan jumlah barang"
                   required>

            <label for="kategori_id">Kategori Barang</label>
            <select id="kategori_id" name="kategori_id" required>
                <option value="">-- Pilih Kategori --</option>

                <?php foreach ($daftarKategori as $kat): ?>
                    <option value="<?= (int) $kat['id'] ?>">
                        <?= htmlspecialchars(
                            $kat['nama_kategori'],
                            ENT_QUOTES,
                            'UTF-8'
                        ) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" name="tambah" class="btn-simpan">
                Simpan Barang
            </button>
        </form>
    </div>

    <!-- TABEL DAFTAR BARANG -->
    <div class="panel">
        <h3>Daftar Barang</h3>

        <div class="table-scroll">
            <table>
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (mysqli_num_rows($data) > 0): ?>
                    <?php $no = 1; ?>

                    <?php while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++ ?></td>

                        <td>
                            <?= htmlspecialchars(
                                $row['nama_barang'],
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>

                            <!-- FORM EDIT BARANG -->
                            <form method="POST"
                                  class="form-edit"
                                  id="edit-<?= (int) $row['id'] ?>">

                                <input type="hidden" name="id"
                                       value="<?= (int) $row['id'] ?>">

                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang"
                                       value="<?= htmlspecialchars(
                                           $row['nama_barang'],
                                           ENT_QUOTES,
                                           'UTF-8'
                                       ) ?>" required>

                                <label>Jumlah Barang</label>
                                <input type="number" name="jumlah"
                                       min="0"
                                       value="<?= (int) $row['jumlah'] ?>"
                                       required>

                                <label>Kategori</label>
                                <select name="kategori_id" required>
                                    <option value="">-- Pilih Kategori --</option>

                                    <?php foreach ($daftarKategori as $kat): ?>
                                        <option
                                            value="<?= (int) $kat['id'] ?>"
                                            <?= ((int) $row['kategori_id'] === (int) $kat['id'])
                                                ? 'selected' : '' ?>>
                                            <?= htmlspecialchars(
                                                $kat['nama_kategori'],
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <button type="submit" name="edit"
                                        class="btn-simpan">
                                    Simpan Perubahan
                                </button>

                                <button type="button" class="btn-hapus"
                                        onclick="tutupEdit(<?= (int) $row['id'] ?>)">
                                    Batal
                                </button>
                            </form>
                        </td>

                        <td><?= (int) $row['jumlah'] ?></td>

                        <td>
                            <span class="badge-kategori">
                                <?= htmlspecialchars(
                                    $row['nama_kategori'] ?? 'Tanpa kategori',
                                    ENT_QUOTES,
                                    'UTF-8'
                                ) ?>
                            </span>
                        </td>

                        <td>
                            <div class="aksi">
                                <button type="button" class="btn-edit"
                                        onclick="bukaEdit(<?= (int) $row['id'] ?>)">
                                    Edit
                                </button>

                                <form method="POST"
                                      onsubmit="return confirm('Yakin ingin menghapus barang ini?')">
                                    <input type="hidden" name="id"
                                           value="<?= (int) $row['id'] ?>">

                                    <button type="submit" name="hapus"
                                            class="btn-hapus">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty">
                            Belum ada data barang.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="footer">
    &copy; <?= date('Y') ?> InventarisKu
</footer>

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