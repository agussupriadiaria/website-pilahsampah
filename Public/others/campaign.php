<?php
// Memulai sesi jika belum dimulai
session_start(); 

// Mengecek apakah user sudah login
$is_logged_in = isset($_SESSION["is_login"]) && $_SESSION["is_login"];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign - Kegiatan Kami</title>
    <link rel="stylesheet" href="campaignStyle.css">
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
                <li><a href="../index" class="menu-link">Home</a></li>
                <li><a href="../about" class="menu-link">About</a></li>
                <li><a href="../contact" class="menu-link">Contact</a></li>
                <?php if ($is_logged_in): ?>
                    <!-- Jika user login, tampilkan Profile dan Logout -->
                    <li><a href="/profile" class="menu-link">Profile</a></li>
                    <li><a href="/logout" class="menu-link">Logout</a></li>
                <?php else: ?>
                    <!-- Jika user belum login, tampilkan Login dan Register -->
                    <li><a href="/login" class="menu-link">Login</a></li>
                    <li><a href="/register" class="menu-link">Register</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="campaign-section">
            <div class="campaign-container">
                <!-- Campaign 1 -->
                <div class="campaign-card">
                    <img src="fotoCampaign/fotocampaign1.jpeg" alt="Kegiatan Penghijauan" class="campaign-img">
                    <h3>Kegiatan Penghijauan</h3>
                    <p>Dalam rangka memperingati Hari Bumi, kami melakukan kegiatan penanaman 1000 pohon di sekitar perkotaan untuk meningkatkan kualitas udara dan menghijaukan lingkungan.</p>
                </div>

                <!-- Campaign 2 -->
                <div class="campaign-card">
                    <img src="fotoCampaign/fotocampaign2.jpeg" alt="Edukasi Daur Ulang" class="campaign-img">
                    <h3>Edukasi Daur Ulang</h3>
                    <p>Kami mengadakan workshop dan edukasi di sekolah-sekolah mengenai pentingnya daur ulang dan cara pengelolaan sampah plastik dengan benar.</p>
                </div>

                <!-- Campaign 3 -->
                <div class="campaign-card">
                    <img src="fotoCampaign/fotocampaign3.jpeg" alt="Bersih Pantai" class="campaign-img">
                    <h3>Gerakan Bersih Pantai</h3>
                    <p>Melalui program ini, kami bersama dengan masyarakat lokal berhasil membersihkan lebih dari 3 ton sampah di sepanjang garis pantai dalam rangka menjaga ekosistem laut.</p>
                </div>

                <!-- Campaign 4 -->
                <div class="campaign-card">
                    <img src="fotoCampaign/fotocampaign4.jpeg" alt="Pengumpulan Sampah Elektronik" class="campaign-img">
                    <h3>Pengumpulan Sampah Elektronik</h3>
                    <p>Kami menyelenggarakan event pengumpulan sampah elektronik untuk memastikan pengolahan yang aman dan ramah lingkungan dari perangkat elektronik bekas.</p>
                </div>

                <!-- Campaign 5 -->
                <div class="campaign-card">
                    <img src="fotoCampaign/fotocampaign5.jpeg" alt="Kampanye Bebas Plastik" class="campaign-img">
                    <h3>Kampanye Bebas Plastik</h3>
                    <p>Program ini bertujuan untuk mengurangi penggunaan plastik sekali pakai dengan menggantinya dengan alternatif yang lebih ramah lingkungan.</p>
                </div>

                <!-- Campaign 6 -->
                <div class="campaign-card">
                    <img src="fotoCampaign/fotocampaign6.jpeg" alt="Pendidikan Lingkungan" class="campaign-img">
                    <h3>Pendidikan Lingkungan</h3>
                    <p>Kami mengadakan seminar dan pelatihan mengenai pentingnya pelestarian lingkungan dan cara-cara sederhana untuk menjadi lebih ramah lingkungan dalam kehidupan sehari-hari.</p>
                </div>
            </div>
        </section>
    </main>

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
