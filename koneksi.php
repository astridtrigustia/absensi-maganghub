<?php
$conn = mysqli_connect(
    $_ENV['MYSQLHOST'],
    $_ENV['MYSQLUSER'],
    $_ENV['MYSQLPASSWORD'],
    $_ENV['MYSQLDATABASE'],
    $_ENV['MYSQLPORT']
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
