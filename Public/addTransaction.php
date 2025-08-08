<?php
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

    // Cek apakah user sudah login, jika belum arahkan ke halaman login
    if (!isset($_SESSION["is_login"]) || !$_SESSION["is_login"]) {
        header("Location: login.php");
        exit();
    }

    // Ambil username dari session
    $is_logged_in = $_SESSION["username"];

    // Memanggil hostkey.php
    require './php/hostkey.php';

    // Buat koneksi
    $conn = new mysqli($hostname, $username, $password, $databasename);

    // Periksa koneksi
    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Cek apakah form sudah di-submit
    $response = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Ambil data dari form
        $transactioncode = $_POST['transactioncode'];
        $transactiondate = $_POST['transactiondate'];

        // Konversi tanggal ke format dd-mm-yyyy
        $dateObj = DateTime::createFromFormat('Y-m-d', $transactiondate);
        $formattedDate = $dateObj ? $dateObj->format('d-m-Y') : null;

        // Siapkan query untuk mengecek apakah transactioncode dan transactiondate sudah ada
        $checkQuery = "SELECT * FROM transactions WHERE transactioncode = ? AND transactiondate = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ss", $transactioncode, $transactiondate);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        // Jika data sudah ada, tampilkan pesan error
        if ($result->num_rows > 0) {
            $response = "Transaction already in use!";
        } else {
            // Siapkan query untuk menyimpan transactioncode, transactiondate, dan username
            $sql = "INSERT INTO transactions (transactioncode, transactiondate, username) VALUES (?, ?, ?)";

            // Siapkan statement
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $transactioncode, $transactiondate, $is_logged_in);

            // Eksekusi query
            if ($stmt->execute()) {
                $response = "Berhasil!. Silakan input lagi atau cek halaman transaksi.";
            } else {
                $response = "Error: " . $stmt->error;
            }

            // Tutup statement
            $stmt->close();
        }

        // Tutup statement pengecekan
        $checkStmt->close();
    }

    // Tutup koneksi
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Transaction</title>
    <link rel="stylesheet" href="addTransactionStyle.css">
</head>
<body>
<header>
    <div class="logo">
        <h1>PilahSampah Indonesia</h1>
    </div>
    <div class="menu-toggle" id="menu-toggle">
        &#9776; <!-- Icon hamburger -->
    </div>
    <nav>
        <ul id="menu">
            <li><a href="index" class="menu-link">Home</a></li>
            <li><a href="about" class="menu-link">About</a></li>
            <li><a href="contact" class="menu-link">Contact</a></li>
            <?php if ($is_logged_in): ?>
                <li><a href="profile" class="menu-link">Profile</a></li>
                <li><a href="logout" class="menu-link">Logout</a></li>
            <?php else: ?>
                <li><a href="login" class="menu-link">Login</a></li>
                <li><a href="register" class="menu-link">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<section class="input-section">
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($is_logged_in); ?>!</h2>
        <p>Silakan tambahkan kode unik [transactioncode] di kolom yang tersedia.</p>

        <a href="profile" class="profile-button" title="Back to profile"> &#8592; Profile</a>
        <a href="transaction-verify" class="transaction-button" title="Back to transaction"> &#8592; Transactions</a>

        <form method="POST" action="">
            <label for="transactioncode" class="label-code">Transaction code:</label>
            <input type="number" name="transactioncode" id="transactioncode" maxlength="5" required oninput="validateLength(this)">
            <div id="error-message" style="color: red; display: none;">Maksimal 5 digit angka!</div><br>

            <label for="transactiondate" class="label-date">Transaction date:</label>
            <input type="date" name="transactiondate" required><br>

            <input type="submit" value="Simpan">
        </form>

        <div class="data-berhasil"><?php echo htmlspecialchars($response); ?></div>
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

    // Validasi panjang input untuk transactioncode
    function validateLength(input) {
        const errorMessage = document.getElementById('error-message');
        if (input.value.length > 5) {
            errorMessage.style.display = 'block';
        } else {
            errorMessage.style.display = 'none';
        }
    }
</script>

</body>
</html>
