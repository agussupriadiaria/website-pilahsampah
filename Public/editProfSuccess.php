<?php
    // Mulai sesi jika belum dimulai
    session_start();
    // =====================
    // Atur waktu sesi menjadi 3 menit (180 detik)
    $inactive = 600;

    // Cek apakah sesi 'timeout' sudah diatur
    if (isset($_SESSION['timeout'])) {
        $session_life = time() - $_SESSION['timeout'];
        if ($session_life > $inactive) {
            session_unset();
            session_destroy();
            header("Location: logout"); // Arahkan ke halaman logout atau login
            exit(); // Hentikan eksekusi skrip setelah redirect
        }
    }

    $_SESSION['timeout'] = time();
    //===================== 
    // Pastikan ada sesi login aktif
    if (!isset($_SESSION["is_login"]) || !$_SESSION["is_login"]) {
        header("Location: login.php");
        exit();
    }

    // Logika setelah edit profil sukses
    $edit_success = true; // Ganti sesuai dengan kondisi sukses dari proses edit profil
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Berhasil</title>
    <link rel="stylesheet" href="regsuccessStyle.css"> <!-- Sesuaikan file CSS -->
</head>
<body>
    <main>
        <div class="notification-container">
            <?php if ($edit_success): ?>
                <h2>Edit Profil Berhasil!</h2>
                <p>Profil Anda telah berhasil diperbarui. Klik tombol di bawah untuk melihat profil Anda.</p>
                <a href="profile" class="btn-profile">Lihat Profil</a>
            <?php else: ?>
                <h2>Edit Profil Gagal!</h2>
                <p>Terjadi kesalahan saat memperbarui profil Anda. Silakan coba lagi.</p>
                <a href="editProfile" class="btn-try-again">Coba Lagi</a>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> PilahSampah Indonesia. All rights reserved.</p>
    </footer>
</body>
</html>
