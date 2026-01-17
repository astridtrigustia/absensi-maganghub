<?php
include 'koneksi.php';

$week = $_GET['week'] ?? date('o-\WW');
list($year, $weekNum) = explode('-W', $week);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=rekap_mingguan_$week.xls");

echo "Nama\tHadir\tTerlambat\tIzin\tSakit\tAlpa\n";

$q = mysqli_query($conn, "
    SELECT 
        nama,
        SUM(status='Hadir') AS hadir,
        SUM(status='Terlambat') AS terlambat,
        SUM(status='Izin') AS izin,
        SUM(status='Sakit') AS sakit,
        SUM(status='Alpa') AS alpa
    FROM absensi
    WHERE YEARWEEK(tanggal, 1) = YEARWEEK(STR_TO_DATE('$year $weekNum 1','%X %V %w'),1)
    GROUP BY nama
    ORDER BY nama
");

while ($r = mysqli_fetch_assoc($q)) {
    echo "{$r['nama']}\t{$r['hadir']}\t{$r['terlambat']}\t{$r['izin']}\t{$r['sakit']}\t{$r['alpa']}\n";
}
