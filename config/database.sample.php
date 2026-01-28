<?php
// config/database.sample.php
// Copy file ini menjadi database.php dan sesuaikan kredensialnya

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'kinerja_pengawas';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
