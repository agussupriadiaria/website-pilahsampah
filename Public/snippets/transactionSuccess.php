<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit Transactions</title>
    <link rel="stylesheet" href="transactionSuccessStyle.css">
</head>

<body>
    <section class="transaction-history-section">
        <div class="container">
            <a href="profile" class="back-button">&#8592; Profile</a>
            <a href="addTransaction" class="add-button">Add Transaction</a>
            <a href="transaction-verify" class="add-button">Transactions Verify</a>
            <a href="transaction-invalid" class="add-button">Transactions Invalid</a>
            <a href="withdrawal-history" class="withdraw-button">Withdrawal History</a>

            <h2>Deposit Transactions Success</h2>
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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
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