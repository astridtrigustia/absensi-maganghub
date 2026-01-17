<?php
date_default_timezone_set("Asia/Jakarta");
include "koneksi.php";

/* ================== AMBIL POST ================== */
$nama   = trim($_POST['nama'] ?? '');
$posisi = trim($_POST['posisi'] ?? '');
$jenis  = trim($_POST['jenis'] ?? '');
$lat    = trim($_POST['latitude'] ?? '');
$lon    = trim($_POST['longitude'] ?? '');
$device = trim($_POST['device_id'] ?? '');

if ($nama==='' || $posisi==='' || $jenis==='' || $device==='') {
    header("Location: index.php?err=" . urlencode("Data tidak lengkap."));
    exit;
}

/* ================== WAJIB AKTIFKAN LOKASI ================== */
if ($lat === '' || $lon === '') {
    header("Location: index.php?err=" . urlencode(
        "Lokasi tidak terdeteksi. Aktifkan GPS dan izinkan lokasi."
    ));
    exit;
}

/* ================== BATAS LOKASI KANTOR ================== */
/* 📍 GANTI sesuai koordinat kantor */
$kantor_lat = -0.8109;
$kantor_lon = 100.3399;
$radius_max = 0.0015; // ±150 meter

$jarak = sqrt(
    pow(floatval($lat) - $kantor_lat, 2) +
    pow(floatval($lon) - $kantor_lon, 2)
);

if ($jarak > $radius_max) {
    header("Location: index.php?err=" . urlencode(
        "Absensi hanya bisa dilakukan di area kantor."
    ));
    exit;
}

/* ================== FITUR 1 HP = 1 ORANG ================== */
$cekDev = mysqli_query($conn,
    "SELECT nama FROM device_map WHERE device_id='$device'"
);

if (mysqli_num_rows($cekDev) > 0) {
    $d = mysqli_fetch_assoc($cekDev);
    if ($d['nama'] !== $nama) {
        header("Location: index.php?err=" . urlencode(
            "HP ini sudah terdaftar untuk: " . $d['nama']
        ));
        exit;
    }
} else {
    mysqli_query($conn,
        "INSERT INTO device_map (device_id, nama)
         VALUES ('$device', '$nama')"
    );
}

/* ================== WAKTU ================== */
$hari   = date("Y-m-d");
$waktu  = date("H:i:s");
$jamNow = date("H:i");

/* ================== ATUR TERLAMBAT ================== */
$batasMasuk  = "08:00";
$statusMasuk = "Hadir";

if ($jenis === 'masuk' && $jamNow > $batasMasuk) {
    $statusMasuk = "Terlambat";
}

/* ================== REVERSE GEOCODE ================== */
function getAddress($lat, $lon) {
    $url = "https://nominatim.openstreetmap.org/reverse?lat=$lat&lon=$lon&format=jsonv2";
    $opts = [
        "http" => [
            "header" => "User-Agent: AbsensiApp/1.0\r\n",
            "timeout" => 5
        ]
    ];
    $ctx = stream_context_create($opts);
    $res = @file_get_contents($url, false, $ctx);
    if (!$res) return "Lokasi kantor";
    $data = json_decode($res, true);
    return $data['display_name'] ?? "Lokasi kantor";
}

/* ================== CEK ABSENSI HARI INI ================== */
$q = mysqli_query($conn,
    "SELECT * FROM absensi WHERE nama='$nama' AND tanggal='$hari'"
);

if (mysqli_num_rows($q) > 0) {

    $r = mysqli_fetch_assoc($q);

    switch ($jenis) {
        case 'masuk':
            if ($r['masuk'] !== null) {
                header("Location: index.php?err=" . urlencode(
                    "Kamu sudah absen masuk hari ini."
                ));
                exit;
            }
            mysqli_query($conn,
                "UPDATE absensi
                 SET masuk='$waktu', status='$statusMasuk'
                 WHERE id=".$r['id']
            );
            break;

        case 'istirahat':
            if ($r['masuk'] === null) {
                header("Location: index.php?err=" . urlencode(
                    "Harus absen masuk dulu."
                ));
                exit;
            }
            if ($r['istirahat'] !== null) {
                header("Location: index.php?err=" . urlencode(
                    "Sudah absen setelah istirahat."
                ));
                exit;
            }
            mysqli_query($conn,
                "UPDATE absensi SET istirahat='$waktu'
                 WHERE id=".$r['id']
            );
            break;

        case 'pulang':
            if ($r['istirahat'] === null) {
                header("Location: index.php?err=" . urlencode(
                    "Harus absen setelah istirahat dulu."
                ));
                exit;
            }
            if ($r['pulang'] !== null) {
                header("Location: index.php?err=" . urlencode(
                    "Sudah absen pulang."
                ));
                exit;
            }
            mysqli_query($conn,
                "UPDATE absensi SET pulang='$waktu'
                 WHERE id=".$r['id']
            );
            break;
    }

    if ($r['alamat'] === null) {
        $addr = getAddress($lat, $lon);
        mysqli_query($conn,
            "UPDATE absensi
             SET lat='$lat', lon='$lon', alamat='$addr'
             WHERE id=".$r['id']
        );
    }

} else {

    if ($jenis !== 'masuk') {
        header("Location: index.php?err=" . urlencode(
            "Kamu harus absen masuk dulu!"
        ));
        exit;
    }

    $addr = getAddress($lat, $lon);

    mysqli_query($conn,
        "INSERT INTO absensi
        (nama,posisi,tanggal,masuk,lat,lon,alamat,status)
        VALUES
        ('$nama','$posisi','$hari','$waktu','$lat','$lon','$addr','$statusMasuk')"
    );
}

header("Location: index.php?msg=" . urlencode("Absen $jenis berhasil!"));
exit;
