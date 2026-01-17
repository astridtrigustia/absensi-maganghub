<?php
// ===== AMBIL DATA NAMA DARI pegawai.csv =====
$pegawai = [];
$file = __DIR__ . "/data/data_pegawai.csv";

if (file_exists($file)) {
    if (($f = fopen($file, "r")) !== false) {
        $i = 0;
        while (($row = fgetcsv($f)) !== false) {
            $i++;
            if ($i == 1) continue; // skip header
            if (!empty($row[0])) {
                $pegawai[] = trim($row[0]);
            }
        }
        fclose($f);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/style.css">
    <title>Form Absensi</title>
</head>
<body>

<?php include "header.php"; ?>

<div class="absen-card">
    <div class="page-title">Form Absensi</div>

    <!-- DEVICE ID GENERATOR -->
    <script>
        if (!localStorage.getItem('device_id')) {
            const newId = "dev_" + Math.random().toString(36).substring(2) + Date.now();
            localStorage.setItem('device_id', newId);
        }
    </script>

    <form id="form" class="absen-form" action="submit.php" method="POST" autocomplete="off">

        <!-- NAMA (DROPDOWN) -->
        <select name="nama" required>
            <option value="" disabled selected>Pilih nama</option>
            <?php foreach ($pegawai as $p): ?>
                <option value="<?= htmlspecialchars($p) ?>">
                    <?= htmlspecialchars($p) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Posisi -->
        <input name="posisi" placeholder="Posisi" required>

        <!-- Jenis Absen -->
        <select name="jenis" required>
            <option value="" disabled selected>Pilih jenis absensi</option>
            <option value="masuk">Absen Masuk</option>
            <option value="istirahat">Absen Setelah Istirahat</option>
            <option value="pulang">Absen Pulang</option>
        </select>

        <!-- Hidden geolocation -->
        <input type="hidden" id="lat" name="latitude">
        <input type="hidden" id="lon" name="longitude">

        <!-- Hidden Device ID -->
        <input type="hidden" id="device_id" name="device_id">

        <button type="button" onclick="absen()" class="btn masuk">
            Kirim Absensi
        </button>
    </form>
</div>

<?php include "footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_GET['err'])): ?>
<script>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: <?= json_encode($_GET['err']) ?>,
    confirmButtonText: 'OK'
});
</script>
<?php endif; ?>

<?php if (isset($_GET['msg'])): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Berhasil',
    text: <?= json_encode($_GET['msg']) ?>,
    confirmButtonText: 'OK'
});
</script>
<?php endif; ?>

<script src="js/geo.js"></script>

<!-- ================= ANTI DOUBLE / TRIPLE SUBMIT ================= -->
<script>
let sudahSubmit = false;

function absen() {
    if (sudahSubmit) return; // ⛔ STOP kalau sudah submit
    sudahSubmit = true;

    document.getElementById('device_id').value =
        localStorage.getItem('device_id');

    document.getElementById('form').submit();
}
</script>

</body>
</html>
