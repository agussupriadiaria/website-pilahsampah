<?php
    session_start(); // Memulai session

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

    // Cek apakah user sudah login
    $is_logged_in = isset($_SESSION["is_login"]) && $_SESSION["is_login"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="contactStyle.css">
</head>
<body>
<header>
    <div class="logo">
        <h1>PilahSampah Indonesia</h1>
    </div>
    <!-- Menu Hamburger Icon -->
    <div class="menu-toggle" id="menu-toggle">
        &#9776; <!-- Icon hamburger -->
    </div>
    <nav>
        <ul id="menu">
            <li><a href="index" class="menu-link">Home</a></li>
            <li><a href="about" class="menu-link">About</a></li>
            <li><a href="contact" class="menu-link active">Contact</a></li>
            <?php if ($is_logged_in): ?>
                <!-- Jika user login, tampilkan Profile dan Logout -->
                <li><a href="profile" class="menu-link">Profile</a></li>
                <li><a href="logout" class="menu-link">Logout</a></li>
            <?php else: ?>
                <!-- Jika user belum login, tampilkan Login dan Register -->
                <li><a href="login" class="menu-link">Login</a></li>
                <li><a href="register" class="menu-link">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<section class="contact-section">
    <div class="container">
        <h2>Hubungi Kami</h2>
        <p>Jika Anda memiliki pertanyaan atau membutuhkan informasi lebih lanjut, jangan ragu untuk menghubungi kami melalui WhatsApp.</p>
        
        <!-- Link WhatsApp -->
        <div class="whatsapp-contact">
            <!-- <h3>Atau hubungi kami melalui WhatsApp:</h3> -->
            <a href="https://wa.me/6282329095164?text=Halo,%20saya%20ingin%20bertanya%20tentang%20PilahSampah" target="_blank" class="btn-whatsapp">Chat via WhatsApp</a>
        </div>
    </div>
</section>

<footer>
    <p>&copy; 2024 PilahSampah. All rights reserved.</p>
</footer>

<script>
    // Dropdown Menu Toggle
    document.getElementById('menu-toggle').addEventListener('click', function() {
        var menu = document.getElementById('menu');
        menu.classList.toggle('show');
    });

    // Aktifkan link yang dipilih
    document.querySelectorAll('.menu-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.menu-link').forEach(item => {
                item.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
</script>

</body>
</html>