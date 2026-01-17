<?php
include 'koneksi.php';
session_start();

if (!isset($_SESSION['admin'])) {
    die("Akses ditolak");
}

$id = intval($_POST['id']);
$status = $_POST['status'];

$allowed = ['Hadir','Terlambat','Alpa','Izin','Sakit'];
if (!in_array($status, $allowed)) {
    die("Status tidak valid");
}

$stmt = $conn->prepare("UPDATE absensi SET status=? WHERE id=?");
$stmt->bind_param("si", $status, $id);
$stmt->execute();

header("Location: tabel.php");
exit;
