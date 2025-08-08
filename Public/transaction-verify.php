<?php
session_start();

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

if (!isset($_SESSION["is_login"])) {
    header("Location: login.php");
    exit();
}

$is_logged_in = true;
require "./php/connectphp.php";
$username = $_SESSION['username'];
$error_message = "";

// PAGINATION LOGIC for Deposit Transactions Success
$limit = 10;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

// Hitung total rows
$count_stmt = $db->prepare("SELECT COUNT(*) FROM transactions WHERE username = ?");
$count_stmt->bind_param("s", $username);
$count_stmt->execute();
$count_stmt->bind_result($total_rows);
$count_stmt->fetch();
$count_stmt->close();

$total_pages = ceil($total_rows / $limit);

// Ambil data halaman aktif
$stmt = $db->prepare("SELECT * FROM transactions WHERE username = ? ORDER BY inputdate DESC LIMIT ? OFFSET ?");
$stmt->bind_param("sii", $username, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$transaction_data = [];
while ($row = $result->fetch_assoc()) {
    $transaction_data[] = $row;
}

$stmt->close();
$db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Transactions</title>
    <link rel="stylesheet" href="transactionSuccessStyle.css">
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

    <section class="transaction-history-section">
        <div class="container">
            <a href="profile" class="back-button">&#8592; Profile</a>
            <a href="addTransaction" class="add-button">Add Transaction</a>
            <a href="withdraw" class="add-button">Add Withdrawal</a>
            <a href="transaction-success" class="add-button">Transactions Success</a>
            <a href="transaction-invalid" class="add-button">Transactions Invalid</a>
            <a href="withdrawal-history" class="withdraw-button">Withdrawal History</a>

            <h2>Deposit Transactions Verify</h2>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Input Date</th>
                        <th>Transaction Code</th>
                        <th>Item</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 0; $i < 10; $i++): ?>
                        <tr class="<?= isset($transaction_data[$i]) ? '' : 'empty-row' ?>">
                            <?php if (isset($transaction_data[$i])): 
                                $row = $transaction_data[$i];
                                $inputDate = new DateTime($row['inputdate']);
                            ?>
                                <td><?= htmlspecialchars($inputDate->format('d-m-Y')) ?></td>
                                <td><?= htmlspecialchars($row['transactioncode']) ?></td>
                                <td><?= htmlspecialchars($row['item']) ?></td>
                                <td><?= "Rp " . number_format($row['amount'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['notes'] ?? '-') ?></td>
                            <?php else: ?>
                                <td></td><td></td><td></td><td></td><td></td><td></td>
                            <?php endif; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>">&laquo; Previous</a>
                <?php endif; ?>
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?>">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </section>
 
    <footer>
        <p>&copy; 2024 PilahSampah. All rights reserved.</p>
    </footer>

    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('menu').classList.toggle('show');
        });
        document.querySelectorAll('.menu-link').forEach(link => {
            link.addEventListener('click', function() {
                document.querySelectorAll('.menu-link').forEach(item => item.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
</body>
</html>