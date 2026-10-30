<?php
$koneksi = mysqli_connect(
    "localhost",
    "root",
    "",
    "latihan_pelpel"
);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>