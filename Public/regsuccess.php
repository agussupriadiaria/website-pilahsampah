<?php
// Mulai sesi jika belum dimulai
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Berhasil</title>
    <link rel="stylesheet" href="regsuccessStyle.css">
</head>
<body>
    <main>
        <div class="notification-container">
            <h2>Registrasi Anda Berhasil!</h2>
            <p>Klik tombol di bawah ini untuk masuk ke halaman dashboard user.</p>
            <a href="login.php" class="btn-login">Login</a>
        </div>
    </main>
    <footer>
        <p>&copy; <?php echo date("Y"); ?> PilahSampah Indonesia. All rights reserved.</p>
    </footer>
</body>
</html>
