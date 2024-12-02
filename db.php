<?php
$servername = "localhost";  // Biasanya localhost, bisa disesuaikan jika beda
$username = "root";  // Ganti dengan username database Anda
$password = "";  // Ganti dengan password database Anda
$dbname = "admin_db";  // Pastikan nama database sesuai dengan yang Anda sebutkan

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $dbname);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
