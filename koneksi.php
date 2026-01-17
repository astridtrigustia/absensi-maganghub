<?php
$conn = mysqli_connect(
    $_ENV['MYSQL_HOST'],
    $_ENV['MYSQL_USER'],
    $_ENV['MYSQL_PASSWORD'],
    $_ENV['MYSQL_DATABASE'],
    $_ENV['MYSQL_PORT']
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>


