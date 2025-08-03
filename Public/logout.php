<?php
session_start(); // Mulai sesi

// Hapus semua data session
$_SESSION = array();

// Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Mulai sesi baru untuk login ulang
session_start();
$_SESSION['message'] = 'Silakan login kembali untuk melanjutkan.';

// Arahkan pengguna ke halaman login
header("Location: login.php"); // Ubah menjadi login.php jika Anda menggunakan ekstensi .php
exit();
?>
