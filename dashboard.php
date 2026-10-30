
<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'koneksi.php';

$username = $_SESSION['username'];

// Menghitung jumlah kategori
$queryKategori = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM kategori");
$totalKategori = mysqli_fetch_assoc($queryKategori)['total'];

// Menghitung jumlah barang
$queryBarang = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM barang");
$totalBarang = mysqli_fetch_assoc($queryBarang)['total'];

// Mengambil data kategori
$daftarKategori = mysqli_query($koneksi, "SELECT * FROM kategori LIMIT 5");

// Mengambil data barang
$daftarBarang = mysqli_query($koneksi, "SELECT * FROM barang LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - InventarisKu</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7f5;
            color: #1f2937;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* SIDEBAR */
        .sidebar {
            width: 245px;
            min-height: 100vh;
            background: #166534;
            color: white;
            padding: 25px 18px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            padding: 0 10px;
            margin-bottom: 40px;
        }

        .logo span {
            color: #bbf7d0;
        }

        .menu-title {
            color: #bbf7d0;
            font-size: 12px;
            margin: 0 12px 15px;
        }

        .sidebar nav a {
            display: block;
            color: #f0fdf4;
            text-decoration: none;
            padding: 13px 15px;
            border-radius: 8px;
            margin-bottom: 8px;
            transition: 0.2s;
        }

        .sidebar nav a:hover,
        .sidebar nav a.active {
            background: #22c55e;
            color: white;
        }

        .logout {
            display: block;
            margin-top: auto;
            background: #14532d;
            color: white;
            text-align: center;
            padding: 13px;
            border-radius: 8px;
            text-decoration: none;
        }

        .logout:hover {
            background: #15803d;
        }

        /* KONTEN KANAN */
        .main-content {
            flex: 1;
            min-width: 0;
        }

        .topbar {
            background: white;
            padding: 22px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 8px #00000008;
        }

        .topbar h2 {
            font-size: 22px;
        }

        .admin-info {
            color: #64748b;
            font-size: 14px;
        }

        .content {
            padding: 30px;
        }

        .welcome {
            background: #dcfce7;
            border-left: 5px solid #22c55e;
            color: #166534;
            padding: 18px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .content h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }

        .description {
            color: #64748b;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        /* KARTU JUMLAH */
        .stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 1px solid #dcfce7;
            box-shadow: 0 4px 12px #1665340a;
            transition: 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-title {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .stat-number {
            color: #15803d;
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .stat-card a {
            color: #15803d;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .stat-card a:hover {
            color: #22c55e;
        }

        /* TABEL */
        .table-card {
            background: white;
            border: 1px solid #dcfce7;
            border-radius: 12px;
            padding: 22px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px #1665340a;
        }

        .table-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .table-heading h3 {
            font-size: 18px;
        }

        .table-heading a {
            color: #15803d;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .table-scroll {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 300px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #dcfce7;
            font-size: 14px;
        }

        th {
            background: #f0fdf4;
            color: #166534;
        }

        tbody tr:hover {
            background: #f8fdf9;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .empty {
            text-align: center;
            color: #94a3b8;
            padding: 25px;
        }

        .footer {
            padding: 20px 30px;
            color: #64748b;
            font-size: 13px;
        }

        /* RESPONSIF */
        @media (max-width: 800px) {
            .layout {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
                padding: 18px;
            }

            .logo {
                margin-bottom: 20px;
            }

            .sidebar nav {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 5px;
            }

            .sidebar nav a {
                margin-bottom: 0;
                font-size: 14px;
            }

            .logout {
                margin-top: 15px;
            }

            .content {
                padding: 20px;
            }

            .topbar {
                padding: 20px;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .stats {
                grid-template-columns: 1fr;
            }

            .content h1 {
                font-size: 23px;
            }

            .table-card {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR KIRI -->
    <aside class="sidebar">

        <div class="logo">
            Inventaris<span>Ku</span>
        </div>

        <p class="menu-title">MENU UTAMA</p>

        <nav>
            <a href="dashboard.php" class="active">🏠 Dashboard</a>
            <a href="kategori.php">📂 Kategori</a>
            <a href="barang.php">📦 Data Barang</a>
            <a href="admin.php">👤 Kelola Admin</a>
        </nav>

        <a href="logout.php" class="logout"
           onclick="return confirm('Yakin ingin logout?')">
            🚪 Logout
        </a>

    </aside>

    <!-- KONTEN KANAN -->
    <div class="main-content">

        <header class="topbar">
            <h2>Dashboard Admin</h2>

            <div class="admin-info">
                👤 <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
            </div>
        </header>

        <main class="content">

            <div class="welcome">
                <strong>
                    Selamat datang,
                    <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>!
                </strong>
                <br>
                Kamu berhasil login ke sistem InventarisKu.
            </div>

            <h1>Ringkasan Inventaris</h1>

            <p class="description">
                Pantau jumlah kategori dan barang yang tersimpan
                di dalam sistem inventaris.
            </p>

            <!-- JUMLAH KATEGORI DAN BARANG -->
            <div class="stats">

                <div class="stat-card">
                    <p class="stat-title">📂 Total Kategori</p>
                    <div class="stat-number"><?= $totalKategori ?></div>
                    <a href="kategori.php">Kelola Kategori →</a>
                </div>

                <div class="stat-card">
                    <p class="stat-title">📦 Total Barang</p>
                    <div class="stat-number"><?= $totalBarang ?></div>
                    <a href="barang.php">Kelola Barang →</a>
                </div>

            </div>

            <!-- TABEL KATEGORI -->
            <section class="table-card">

                <div class="table-heading">
                    <h3>Daftar Kategori</h3>
                    <a href="kategori.php">+ Kelola</a>
                </div>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Kategori</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (mysqli_num_rows($daftarKategori) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($kategori = mysqli_fetch_assoc($daftarKategori)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?= htmlspecialchars(
                                            $kategori['nama_kategori'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="empty">
                                    Belum ada kategori.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </section>

            <!-- TABEL BARANG -->
            <section class="table-card">

                <div class="table-heading">
                    <h3>Daftar Barang</h3>
                    <a href="barang.php">+ Kelola</a>
                </div>

                <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Barang</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php if (mysqli_num_rows($daftarBarang) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($barang = mysqli_fetch_assoc($daftarBarang)): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <?= htmlspecialchars(
                                            $barang['nama_barang'],
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="empty">
                                    Belum ada barang.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </section>

        </main>

        <footer class="footer">
            &copy; <?= date('Y') ?> InventarisKu. Sistem Pengelolaan Barang.
        </footer>

    </div>

</div>

</body>
</html>
