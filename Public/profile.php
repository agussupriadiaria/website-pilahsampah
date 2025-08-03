<?php
    // Include koneksi ke database
    include "php/connectphp.php";
    // Memulai session
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

    // Cek apakah user sudah login
    if (!isset($_SESSION["is_login"]) || !$_SESSION["is_login"]) {
        header("Location: login.php");
        exit(); // Hentikan eksekusi skrip setelah redirect
    }

    // Ambil username dari session
    $username = $_SESSION["username"];

    // Query untuk mengambil data user dari databaseadfa
    $sql = "SELECT fullname, phone, address, email, createdate, balance, accname, accnumber /*profilepicture*/ FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $fullname = $row["fullname"];
        $phone = $row["phone"];
        $address = $row["address"];
        $email = $row["email"];
        $joindate = $row["createdate"];
        $accname = $row["accname"];
        $accnumber = $row["accnumber"];
        $balance = $row["balance"];
        // $profile_pic = $row["profilepicture"]; // Ambil nama file foto profil
    } else {
        echo "User tidak ditemukan.";
        exit();
    }

    // Jika user belum upload foto profil, gunakan gambar default
    if (empty($profile_pic)) {
        $profile_pic = "webImages/defaultProfile.jpeg"; // Path gambar default
    } else {
        $profile_pic = "uploads/" . $profile_pic; // Path ke gambar yang diupload user
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="profileStyle.css">
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
                <li><a href="contact" class="menu-link">Contact</a></li>
                <li><a href="logout" class="menu-link">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="profile-container">
        <div class="profile-header">
            <!-- Menampilkan gambar profil -->
            <img src="<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile Picture" class="profile-pic">
            
            <!-- Menampilkan username dari session -->
            <h1>Welcome, <?= htmlspecialchars($_SESSION["username"]) ?></h1>
        </div>
        <div class="profile-details">
            <div class="detail-item">
                <strong>UserID:</strong>
                <p><?php echo htmlspecialchars($_SESSION["username"]); ?></p>
            </div>
            <div class="detail-item">
                <strong>Fullname:</strong>
                <p><?php echo htmlspecialchars($fullname); ?></p>
            </div>
            <div class="detail-item">
                <strong>Email:</strong>
                <p><?php echo htmlspecialchars($email); ?></p>
            </div>
            <div class="detail-item">
                <strong>Phone:</strong>
                <p><?php echo htmlspecialchars($phone); ?></p>
            </div>
            <div class="detail-item">
                <strong>Address:</strong>
                <p><?php echo htmlspecialchars($address); ?></p>
            </div>
            <div class="detail-item">
                <strong>Join Date:</strong>
                <p><?php $joinDate = new DateTime($joindate); echo htmlspecialchars($joinDate->format('d-m-Y')); ?></p>
            </div>
            <div class="detail-item">
                <strong>Account Name:</strong>
                <p><?php echo htmlspecialchars($accname); ?></p>
            </div>
            <div class="detail-item">
                <strong>Account Number:</strong>
                <p><?php echo htmlspecialchars($accnumber); ?></p>
            </div>
            <div class="detail-item">
                <strong>Balance:</strong>
                <p class="balance"><?php echo "Rp " . number_format($balance, 0, ',', '.'); ?></p>
            </div>
        </div>
        <a href="transaction" class="btn-transaction">Transaction</a>
        <a href="editProfile" class="btn-edit">Edit Profile</a>
    </div>

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
