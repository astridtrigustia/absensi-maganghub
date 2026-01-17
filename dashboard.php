<?php 
session_start(); 
if(!isset($_SESSION['admin'])) die("Login dulu"); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="navbar">
    ABSENSI MAGANGHUB – LAPAS IIB PEREMPUAN PADANG 
</div>

<div class="container">

    <div class="card">
        <h2 class="page-title">Dashboard Admin</h2>

        <div class="dashboard-menu">
            <a href="settings.php" class="menu-btn">Pengaturan Radius</a>
            <a href="password.php" class="menu-btn">Ganti Password</a>
            <a href="tabel.php" class="menu-btn">Lihat Tabel Absensi</a>
        </div>
    </div>

</div>

</body>
</html>
