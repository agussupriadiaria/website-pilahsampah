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

    
    // Ambil data dari transactions
    $stmt = $db->prepare("SELECT * FROM transactions WHERE username = ? ORDER BY inputdate DESC");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Ambil data dari transactions_success
    $stmt1 = $db->prepare("SELECT * FROM transactions_success WHERE username = ? ORDER BY inputdate DESC");
    $stmt1->bind_param("s", $username);
    $stmt1->execute();
    $result1 = $stmt1->get_result();
    
    
    // Ambil data dari transactions_wd
    $stmt2 = $db->prepare("SELECT * FROM transactions_wd WHERE username = ? ORDER BY inputdate DESC");
    $stmt2->bind_param("s", $username);
    $stmt2->execute();
    $result2 = $stmt2->get_result();


    $stmt->close();
    $stmt1->close();
    $stmt2->close();
    $db->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link rel="stylesheet" href="transactionStyle.css">
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
        <a href="withdraw" class="withdraw-button">Withdraw</a>

        <h2>Deposit Transactions Verify</h2>
        <?php if ($result->num_rows > 0): ?>
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
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php $inputDate = new DateTime($row['inputdate']); echo htmlspecialchars($inputDate->format('d-m-Y')); ?></td>
                            <td><?php echo htmlspecialchars($row['transactioncode']); ?></td>
                            <td><?php echo htmlspecialchars($row['item']); ?></td>
                            <td><?php echo "Rp " . number_format($row['amount'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes'] ?? '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No recycling transaction history found.</p>
        <?php endif; ?>

        <h2>Deposit Transactions Success</h2>
        <?php if ($result2->num_rows > 0): ?>
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
                    <?php while ($row = $result2->fetch_assoc()): ?>
                        <tr>
                            <td><?php $inputDate = new DateTime($row['inputdate']); echo htmlspecialchars($inputDate->format('d-m-Y')); ?></td>
                            <td><?php echo htmlspecialchars($row['transactioncode']); ?></td>
                            <td><?php echo htmlspecialchars($row['item']); ?></td>
                            <td><?php echo "Rp " . number_format($row['amount'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes'] ?? '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No recycling transaction history found.</p>
        <?php endif; ?>

        <h2>Deposit Transactions Invalid</h2>
        <?php if ($result2->num_rows > 0): ?>
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
                    <?php while ($row = $result2->fetch_assoc()): ?>
                        <tr>
                            <td><?php $inputDate = new DateTime($row['inputdate']); echo htmlspecialchars($inputDate->format('d-m-Y')); ?></td>
                            <td><?php echo htmlspecialchars($row['transactioncode']); ?></td>
                            <td><?php echo htmlspecialchars($row['item']); ?></td>
                            <td><?php echo "Rp " . number_format($row['amount'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes'] ?? '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No recycling transaction history found.</p>
        <?php endif; ?>
        
        <h2>Withdrawal Transactions</h2>
        <?php if ($result->num_rows > 0): ?>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Input Date</th>
                        <th>Transaction Code</th>
                        <th>Transaction Date</th>
                        <th>Item</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php $inputDate = new DateTime($row['inputdate']); echo htmlspecialchars($inputDate->format('d-m-Y')); ?></td>
                            <td><?php echo htmlspecialchars($row['transactioncode']); ?></td>
                            <td><?php echo !empty($row['transactiondate']) ? htmlspecialchars((new DateTime($row['transactiondate']))->format('d-m-Y')) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($row['item']); ?></td>
                            <td><?php echo "Rp " . number_format($row['amount'], 0, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['notes'] ?? '-'); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No withdrawal transaction history found.</p>
        <?php endif; ?>
        
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