<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

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
            header("Location: logout");
            exit();
        }
    }

    $_SESSION['timeout'] = time();

    if (!isset($_SESSION["is_login"]) || !$_SESSION["is_login"]) {
        header("Location: login.php");
        exit();
    }

    $is_logged_in = $_SESSION["username"];

    require "./php/connectphp.php";

    $conn = new mysqli($hostname, $username, $password, $databasename);

    if ($conn->connect_error) {
        die("Koneksi gagal: " . $conn->connect_error);
    }

    // Ambil data accname dan accnumber user dari database
    $walletQuery = "SELECT accname, accnumber FROM users WHERE username = ?";
    $walletStmt = $conn->prepare($walletQuery);
    $walletStmt->bind_param("s", $is_logged_in);
    $walletStmt->execute();
    $walletResult = $walletStmt->get_result();
    $walletData = $walletResult->fetch_assoc();

    $isWalletSet = !empty($walletData['accname']) && !empty($walletData['accnumber']);

    $response = "";
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (!$isWalletSet) {
            $response = "Anda belum mengatur tujuan Withdraw, silakan klik halaman edit profil untuk mengisinya.";
        } else {
            $withdrawAmount = $_POST['amount'];

            if ($withdrawAmount < 10000) {
                $response = "Jumlah penarikan minimum adalah Rp 10.000.";
            } else {
                $balanceQuery = "SELECT balance FROM users WHERE username = ?";
                $balanceStmt = $conn->prepare($balanceQuery);
                $balanceStmt->bind_param("s", $is_logged_in);
                $balanceStmt->execute();
                $result = $balanceStmt->get_result();
                $user = $result->fetch_assoc();
                $currentBalance = $user['balance'];

                if ($withdrawAmount > $currentBalance) {
                    $response = "Saldo tidak mencukupi, silakan cek saldo.";
                } else {
                    $newBalance = $currentBalance - $withdrawAmount;
                    $updateQuery = "UPDATE users SET balance = ? WHERE username = ?";
                    $updateStmt = $conn->prepare($updateQuery);
                    $updateStmt->bind_param("ds", $newBalance, $is_logged_in);

                    if ($updateStmt->execute()) {
                        // Ambil accname dan accnumber dari hasil query sebelumnya
                        $accname = $walletData['accname'];
                        $accnumber = $walletData['accnumber'];

                        $transactionCode = uniqid('wd');
                        $insertTransactionQuery = "INSERT INTO transactions_wd (username, transactioncode, item, amount, status, inputdate, accname, accnumber) VALUES (?, ?, 'Withdrawal', ?, 'Verify', NOW(), ?, ?)";
                        $transactionStmt = $conn->prepare($insertTransactionQuery);
                        $transactionStmt->bind_param("ssdss", $is_logged_in, $transactionCode, $withdrawAmount, $accname, $accnumber);

                        if ($transactionStmt->execute()) {
                            $response = "Permintaan sedang diproses!. Saldo saat ini Rp " . number_format($newBalance, 0, ',', '.');
                        } else {
                            $response = "Error saat menambahkan transaksi: " . $transactionStmt->error;
                        }

                        $transactionStmt->close();
                    } else {
                        $response = "Error: " . $updateStmt->error;
                    }

                    $updateStmt->close();
                }

                $balanceStmt->close();
            }
        }
    }

    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw Credit</title>
    <link rel="stylesheet" href="withdrawStyle.css">
</head>
<body>
<header>
    <div class="logo">
        <h1>PilahSampah Indonesia</h1>
    </div>
    <div class="menu-toggle" id="menu-toggle">&#9776;</div>
    <nav>
        <ul id="menu">
            <li><a href="index" class="menu-link">Home</a></li>
            <li><a href="about" class="menu-link">About</a></li>
            <li><a href="contact" class="menu-link active">Contact</a></li>
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

<section class="withdraw-section">
    <div class="container">
        <h2>Welcome, <?php echo htmlspecialchars($is_logged_in); ?>!</h2>
        <?php if (!$isWalletSet): ?>
            <div class="response-message">
                Anda belum mengatur tujuan Withdraw, silakan klik halaman edit profil untuk mengisinya.
                <br><br>
                <a href="editProfile" class="edit-profile-button">Edit Profil</a>
            </div>
        <?php else: ?>
            <h3>Ketentuan Withdraw:</h3>
            <p>1. Silakan masukkan jumlah penarikan yang diinginkan.</p>
            <p>2. Tidak perlu menambahkan tanda koma atau titik.</p>
            <p>3. Proses Withdraw bisa ditunggu selama 2 hari kerja.</p>
            <p>4. Minimum withdraw adalah Rp 10.000.</p>
            <p>5. Silakan hubungi tim CS kami jika ada kendala.</p>
            <a href="transaction-verify" class="back-button" title="Back to transaction"> &#8592; Transactions</a>
            <form method="POST" action="">
                <label for="amount" class="label-amount">Jumlah Penarikan (Rp):</label>
                <input type="number" name="amount" id="amount" required min="10000"><br>
                <input type="submit" value="Tarik">
            </form>
        <?php endif; ?>

        <div class="response-message"><?php echo htmlspecialchars($response); ?></div>
    </div>
</section>

<footer>
    <p>&copy; 2024 PilahSampah. All rights reserved.</p>
</footer>

<script>
    document.getElementById('menu-toggle').addEventListener('click', function() {
        var menu = document.querySelector('.menu');
        menu.classList.toggle('show');
    });

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
