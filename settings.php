<?php
session_start();
if (!isset($_SESSION['admin'])) die("Login dulu");

// Lokasi file JSON
$dataFile = "settings.json";

// --- BACA DATA ---
if (file_exists($dataFile)) {
    $json = file_get_contents($dataFile);
    $data = json_decode($json, true);

    // Jika gagal decode → buat default
    if (!is_array($data)) {
        $data = ["latitude" => "", "longitude" => "", "radius" => ""];
    }

} else {
    // Jika file tidak ada → buat default
    $data = ["latitude" => "", "longitude" => "", "radius" => ""];
}

// --- SIMPAN DATA ---
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $data['latitude']  = trim($_POST['latitude']);
    $data['longitude'] = trim($_POST['longitude']);
    $data['radius']    = trim($_POST['radius']);

    file_put_contents($dataFile, json_encode($data, JSON_PRETTY_PRINT));

    header("Location: settings.php");
    exit;
}
?>

<link rel='stylesheet' href='css/style.css'>

<div class="container">
    <div class="settings-card">

        <h2 class="settings-title">Pengaturan Kantor</h2>

        <form method="POST" class="settings-form">

            <div>
                <label>Latitude</label>
                <input name="latitude" value="<?= htmlspecialchars($data['latitude']) ?>">
            </div>

            <div>
                <label>Longitude</label>
                <input name="longitude" value="<?= htmlspecialchars($data['longitude']) ?>">
            </div>

            <div>
                <label>Radius (meter)</label>
                <input name="radius" value="<?= htmlspecialchars($data['radius']) ?>">
            </div>
<div style="display:flex; gap:10px; margin-top:20px;">
    <a href="dashboard.php" class="btn btn-secondary" 
       style="background:#6c757d; color:white; padding:10px 20px; border-radius:6px; text-decoration:none;">
        Kembali
    </a>

    <button type="submit" class="btn" 
            style="background:#08224C; color:white; padding:10px 30px; border-radius:6px;">
        Simpan
    </button>
</div>


        </form>

    </div>
</div>
