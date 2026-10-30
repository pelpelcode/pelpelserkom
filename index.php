
<?php
include "koneksi.php";

// Menghitung jumlah kategori
$kategori = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kategori");
$totalKategori = mysqli_fetch_assoc($kategori)['total'];

// Menghitung jumlah barang
$barang = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang");
$totalBarang = mysqli_fetch_assoc($barang)['total'];

// Mengambil data barang dan kategorinya
$dataBarang = mysqli_query($koneksi, "
    SELECT barang.nama_barang, barang.jumlah, kategori.nama_kategori
    FROM barang
    LEFT JOIN kategori ON barang.kategori_id = kategori.id
    ORDER BY barang.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InventarisKu</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f0fdf4;
            margin: 0;
            color: #333;
        }

        header {
            background: #166534;
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h2 {
            margin: 0;
        }

        header a {
            color: white;
            text-decoration: none;
        }

        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 0 15px;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .welcome h1 {
            color: #166534;
        }

        .jumlah {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            flex: 1;
            border-left: 5px solid #22c55e;
        }

        .card h3 {
            margin-top: 0;
        }

        .angka {
            color: #15803d;
            font-size: 30px;
            font-weight: bold;
        }

        .tabel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            overflow-x: auto;
        }

        h2 {
            color: #166534;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        th {
            background: #166534;
            color: white;
        }

        footer {
            text-align: center;
            padding: 20px;
            color: #166534;
        }

        @media (max-width: 500px) {
            .jumlah {
                flex-direction: column;
            }

            header {
                gap: 10px;
            }
        }
    </style>
</head>

<body>

<header>
    <h2>InventarisKu</h2>
    <a href="login.php">login admin🔆</a>
</header>

<div class="container">

    <section class="welcome">
        <h1>Selamat Datang di InventarisKu</h1>
        <p>
            Website untuk melihat data inventaris barang,
            jumlah barang, dan kategori barang.
        </p>
    </section>

    <section class="jumlah">

        <div class="card">
            <h3>Total Kategori</h3>
            <div class="angka"><?= $totalKategori ?></div>
        </div>

        <div class="card">
            <h3>Total Barang</h3>
            <div class="angka"><?= $totalBarang ?></div>
        </div>

    </section>

    <section class="tabel">
        <h2>Daftar Barang</h2>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Kategori</th>
                </tr>
            </thead>

            <tbody>
                <?php $no = 1; ?>

                <?php if (mysqli_num_rows($dataBarang) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($dataBarang)): ?>
                        <tr>
                            <td><?= $no++ ?></td>

                            <td>
                                <?= htmlspecialchars($row['nama_barang']) ?>
                            </td>

                            <td><?= (int) $row['jumlah'] ?></td>

                            <td>
                                <?= htmlspecialchars(
                                    $row['nama_kategori'] ?? 'Tanpa kategori'
                                ) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">Belum ada data barang.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>

</div>

<footer>
    &copy; <?= date('Y') ?> InventarisKu
</footer>

</body>
</html>
