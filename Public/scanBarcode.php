<?php
// Include koneksi ke database
include "php/connectphp.php";
// Memulai session
session_start();

// =====================
// Atur waktu sesi menjadi 10 menit (600 detik)
$inactive = 600;
if (isset($_SESSION['timeout'])) {
    $session_life = time() - $_SESSION['timeout'];
    if ($session_life > $inactive) {
        session_unset();
        session_destroy();
        header("Location: logout"); // Arahkan ke halaman logout
        exit();
    }
}
$_SESSION['timeout'] = time();
// =====================

// Cek apakah user sudah login
if (!isset($_SESSION["is_login"]) || !$_SESSION["is_login"]) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Kamera</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Scan Barcode</h1>
        <p>Halo, <?= htmlspecialchars($_SESSION["username"]); ?>! Akses kamera Anda di sini.</p>
    </header>

    <main>
        <div>
            <label for="camera-select">Pilih Kamera:</label>
            <select id="camera-select"></select>
        </div>
        <video id="video" autoplay playsinline></video>
        <canvas id="canvas" style="display: none;"></canvas>
    </main>

    <footer>
        <p>&copy; 2024 PilahSampah. All rights reserved.</p>
    </footer>

    <script>
        // Elemen DOM
        const video = document.getElementById('video');
        const cameraSelect = document.getElementById('camera-select');

        // Cek apakah browser mendukung MediaDevices
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            alert("Browser Anda tidak mendukung akses kamera.");
        } else {
            // Enumerasi perangkat untuk mendapatkan kamera yang tersedia
            navigator.mediaDevices.enumerateDevices()
                .then(devices => {
                    const videoDevices = devices.filter(device => device.kind === 'videoinput');
                    videoDevices.forEach((device, index) => {
                        const option = document.createElement('option');
                        option.value = device.deviceId;
                        option.text = device.label || `Kamera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });

                    // Set kamera default jika ada
                    if (videoDevices.length > 0) {
                        startCamera(videoDevices[0].deviceId);
                    }
                })
                .catch(error => {
                    console.error("Error mendapatkan perangkat kamera:", error);
                    alert("Gagal mendeteksi kamera. Pastikan perangkat Anda memiliki kamera yang aktif.");
                });

            // Mulai kamera berdasarkan ID perangkat
            function startCamera(deviceId) {
                navigator.mediaDevices.getUserMedia({
                    video: { deviceId: { exact: deviceId } }
                }).then(stream => {
                    video.srcObject = stream;
                }).catch(error => {
                    console.error("Error mengakses kamera:", error);
                    alert("Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.");
                });
            }

            // Ganti kamera berdasarkan pilihan
            cameraSelect.addEventListener('change', () => {
                const selectedDeviceId = cameraSelect.value;
                if (selectedDeviceId) {
                    startCamera(selectedDeviceId);
                }
            });
        }
    </script>
</body>
</html>
