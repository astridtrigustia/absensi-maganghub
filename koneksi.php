<?php
$conn = mysqli_connect(
    "sql206.infinityfree.com",
    "ifo_40922998",
    "xK098RhBh2",
    "ifo_40922998_db_absensi"
);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
