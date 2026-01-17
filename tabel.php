<?php
include 'header.php';
include 'koneksi.php';
?>

<div class="container">

    <h2 class="page-title">Data Absensi</h2>

    <a href="export_excel.php" class="btn-gold">Download Excel</a>

    <form method="GET" class="filter-box">
        <input type="date" name="tanggal"
               value="<?= htmlspecialchars($_GET['tanggal'] ?? '') ?>">

        <input type="text" name="nama" placeholder="Cari nama..."
               value="<?= htmlspecialchars($_GET['nama'] ?? '') ?>">

        <button class="btn-primary">Terapkan</button>
    </form>

    <!-- ================= TABEL ABSENSI HARIAN ================= -->
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Posisi</th>
                <th>Tanggal</th>
                <th>Masuk</th>
                <th>Istirahat</th>
                <th>Pulang</th>
                <th>Status</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>

<?php
$no = 1;
$hadirHariIni = [];

/* ================= FILTER ================= */
$where = [];
if (!empty($_GET['nama'])) {
    $namaCari = mysqli_real_escape_string($conn, $_GET['nama']);
    $where[] = "nama LIKE '%$namaCari%'";
}
if (!empty($_GET['tanggal'])) {
    $tgl = mysqli_real_escape_string($conn, $_GET['tanggal']);
    $where[] = "tanggal = '$tgl'";
}

$whereSql = $where ? "WHERE " . implode(" AND ", $where) : "";

/* ================= QUERY UTAMA ================= */
$q = mysqli_query($conn,
    "SELECT * FROM absensi
     $whereSql
     ORDER BY tanggal DESC, masuk DESC"
);

while ($row = mysqli_fetch_assoc($q)) {

    if ($row['tanggal'] == date("Y-m-d")) {
        $hadirHariIni[] = $row['nama'];
    }

    $classRow = ($row['status'] === 'Terlambat') ? 'telat' : 'tepat';

    echo "<tr class='$classRow'>";
    echo "<td>$no</td>";
    echo "<td>" . htmlspecialchars($row['nama']) . "</td>";
    echo "<td>" . htmlspecialchars($row['posisi']) . "</td>";
    echo "<td>" . htmlspecialchars($row['tanggal']) . "</td>";
    echo "<td>" . htmlspecialchars($row['masuk']) . "</td>";
    echo "<td>" . htmlspecialchars($row['istirahat']) . "</td>";
    echo "<td>" . htmlspecialchars($row['pulang']) . "</td>";

    /* ===== STATUS (EDIT ADMIN) ===== */
    echo "<td>
        <form method='POST' action='update_status.php'>
            <input type='hidden' name='id' value='{$row['id']}'>
            <select name='status' onchange='this.form.submit()'>
    ";
    $opsi = ['Hadir','Terlambat','Alpa','Izin','Sakit'];
    foreach ($opsi as $o) {
        $sel = ($row['status'] == $o) ? 'selected' : '';
        echo "<option value='$o' $sel>$o</option>";
    }
    echo "</select></form></td>";

    echo "<td>" . htmlspecialchars($row['lat']) . "</td>";
    echo "<td>" . htmlspecialchars($row['lon']) . "</td>";
    echo "<td>" . htmlspecialchars($row['alamat']) . "</td>";
    echo "</tr>";

    $no++;
}
?>

        </tbody>
    </table>

<?php
/* ================= DAFTAR TIDAK HADIR (SETELAH JAM 18.00) ================= */
$jamSekarang = date("H:i:s");

if ($jamSekarang >= "18:00:00") {
    $filePegawai = __DIR__ . "/data/pegawai.csv";

    if (file_exists($filePegawai)) {
        echo "<h3 style='margin-top:30px;color:#b71c1c;'>Daftar Tidak Hadir</h3>";

        if (($fp = fopen($filePegawai, "r")) !== false) {
            $i = 0;
            while (($p = fgetcsv($fp)) !== false) {
                $i++;
                if ($i == 1) continue;

                if (!in_array($p[0], $hadirHariIni)) {
                    echo "<p>- " . htmlspecialchars($p[0]) . " <strong>(Alpa)</strong></p>";
                }
            }
            fclose($fp);
        }
    }
}
?>

<!-- ================= REKAP MINGGUAN ================= -->
<?php
$week = $_GET['week'] ?? date('o-\WW');
list($year, $weekNum) = explode('-W', $week);
?>

<h3 style="margin-top:40px;">Rekap Mingguan (Senin - Sabtu)</h3>

<form method="GET" style="margin-bottom:15px;">
    <input type="week" name="week" value="<?= htmlspecialchars($week) ?>">
    <button class="btn-primary">Tampilkan</button>
</form>

<a href="export_rekap_mingguan.php?week=<?= urlencode($week) ?>"
   class="btn-gold"
   style="margin-bottom:15px;display:inline-block;">
   Export Rekap Mingguan (Excel)
</a>

<?php
$rekap = mysqli_query($conn, "
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

echo "<table>
<tr>
    <th>Nama</th>
    <th>Hadir</th>
    <th>Terlambat</th>
    <th>Izin</th>
    <th>Sakit</th>
    <th>Alpa</th>
</tr>";

while ($r = mysqli_fetch_assoc($rekap)) {
    $alpaStyle = ($r['alpa'] > 0)
        ? "style='background:#fdecea;color:#b71c1c;font-weight:bold;'"
        : "";

    echo "<tr>
        <td>{$r['nama']}</td>
        <td>{$r['hadir']}</td>
        <td>{$r['terlambat']}</td>
        <td>{$r['izin']}</td>
        <td>{$r['sakit']}</td>
        <td $alpaStyle>{$r['alpa']}</td>
    </tr>";
}
echo "</table>";
?>

</div>

<?php include 'footer.php'; ?>
