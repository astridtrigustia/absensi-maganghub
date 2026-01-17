
<?php
session_start(); if(!isset($_SESSION['admin'])) die("Login dulu");
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=absensi.csv");
readfile("data/absensi.csv");
?>
