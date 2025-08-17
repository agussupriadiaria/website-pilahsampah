<?php
include "php/connectphp.php";
session_start();

$inactive = 600;
if (isset($_SESSION['timeout'])) {
    if (time() - $_SESSION['timeout'] > $inactive) {
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

$username = $_SESSION["username"];
$sql = "SELECT fullname, phone, address, email, createdate, balance, accname, accnumber FROM users WHERE username = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "User tidak ditemukan.";
    exit();
}

$row = $result->fetch_assoc();
$fullname = $row["fullname"];
$phone = $row["phone"];
$address = $row["address"];
$email = $row["email"];
$joindate = $row["createdate"];
$accname = $row["accname"];
$accnumber = $row["accnumber"];
$balance = $row["balance"];
$profile_pic = "webImages/defaultProfile.jpeg"; // default image
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Page</title>
    <link rel="stylesheet" href="profileStyle.css">
</head>
<body>

<header>
    <div class="logo"><h1>PilahSampah Indonesia</h1></div>
    <div class="menu-toggle" id="menu-toggle">&#9776;</div>
    <nav>
        <ul id="menu">
            <li><a href="index" class="menu-link">Home</a></li>
            <li><a href="about" class="menu-link">About</a></li>
            <li><a href="contact" class="menu-link">Contact</a></li>
            <li><a href="logout" class="menu-link">Logout</a></li>
            <li><a href="/wp/transactions" class="menu-link">wp</a></li>
        </ul>
    </nav>
</header>

<div class="profile-container">
    <div class="profile-header">
        <img src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile Picture" class="profile-pic">
        <h1>Welcome, <?= htmlspecialchars($username) ?></h1>
    </div>

    <div class="profile-details">
        <div class="detail-item"><strong>UserID:</strong><p><?= htmlspecialchars($username) ?></p></div>
        <div class="detail-item"><strong>Fullname:</strong><p><?= htmlspecialchars($fullname) ?></p></div>
        <div class="detail-item"><strong>Email:</strong><p><?= htmlspecialchars($email) ?></p></div>
        <div class="detail-item"><strong>Phone:</strong><p><?= htmlspecialchars($phone) ?></p></div>
        <div class="detail-item"><strong>Address:</strong><p><?= htmlspecialchars($address) ?></p></div>
        <div class="detail-item"><strong>Join Date:</strong><p><?= (new DateTime($joindate))->format("d-m-Y") ?></p></div>
        <div class="detail-item"><strong>Account Name:</strong><p><?= htmlspecialchars($accname) ?></p></div>
        <div class="detail-item"><strong>Account Number:</strong><p><?= htmlspecialchars($accnumber) ?></p></div>
        <div class="detail-item"><strong>Balance:</strong><p class="balance">Rp <?= number_format($balance, 0, ',', '.') ?></p></div>
    </div>

    <a href="transaction-verify" class="btn-transaction">Transactions</a>
    <a href="editProfile" class="btn-edit">Edit Profile</a>
</div>

<footer>
    <p>&copy; 2024 PilahSampah. All rights reserved.</p>
</footer>

<script>
document.getElementById('menu-toggle').addEventListener('click', function () {
    document.getElementById('menu').classList.toggle('show');
});
</script>

</body>
</html>
