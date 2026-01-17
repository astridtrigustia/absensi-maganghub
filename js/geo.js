// =====================
// SETTING LOKASI KANTOR
// =====================
const kantor_lat = -0.810906; 
const kantor_lon = 100.339584; 
const max_distance = 150; // meter


// =====================
// FUNGSI JARAK
// =====================
function hitungJarak(lat1, lon1, lat2, lon2) {
    const R = 6371000;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;

    const a =
        Math.sin(dLat / 2) ** 2 +
        Math.cos(lat1 * Math.PI / 180) *
        Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) ** 2;

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}


// =====================
// PROSES ABSEN
// =====================
function prosesAbsen(lat, lon) {

    const jarak = hitungJarak(lat, lon, kantor_lat, kantor_lon);

    if (jarak > max_distance) {
        Swal.fire({
            icon: "error",
            title: "Kamu di luar kantor!",
            html: `
                <p>Absen tidak bisa dilakukan.</p>
                <br>
                <p>📍 Lokasi kamu sekarang:<br>${lat}, ${lon}</p>
                <p>📐 Jarak dari kantor: <b>${jarak.toFixed(2)} meter</b></p>
                <p>⛔ Maksimal: <b>${max_distance} meter</b></p>
                <br>
                Silakan berada di area kantor untuk absen.
            `,
            confirmButtonText: "OK"
        });
        return;
    }

    document.getElementById("lat").value = lat;
    document.getElementById("lon").value = lon;

    document.getElementById("form").submit();
}


// =====================
// FUNGSI ABSEN UTAMA
// =====================
function absen() {

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            prosesAbsen(pos.coords.latitude, pos.coords.longitude);
        }, () => {
            alert("Izin lokasi ditolak.");
        });
    } else {
        alert("Browser tidak mendukung GPS.");
    }
}
