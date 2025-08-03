<?php
    include "php/connectphp.php";
    $error_message = "";

    if (isset($_POST['register'])) {
        // Ambil data dari form
        $fullname = $_POST['fullname'];
        $phone = $_POST['phone'];
        $username = $_POST['username'];
        $address = $_POST['address'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Hash password menggunakan password_hash() untuk keamanan
        $hash_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            // Siapkan query dengan placeholder
            $stmt = $db->prepare("INSERT INTO users (fullname, phone, username, address, email, password) VALUES (?, ?, ?, ?, ?, ?)");
            // Bind parameter dengan tipe data string ('s')
            $stmt->bind_param("ssssss", $fullname, $phone, $username, $address, $email, $hash_password);
            // Eksekusi query
            if ($stmt->execute()) {
                // Redirect jika berhasil
                header("Location: regsuccess.php");
                exit();
            } else {
                // Tampilkan pesan error jika gagal
                echo "Data error: " . $stmt->error;
            }
            // Tutup statement
            $stmt->close();
        }catch(mysqli_sql_exception $e){
            //Memunculkan error asli dari php codenya ========
            // $error_message = "Database Error: " . $e -> getMessage();
            $error_message = "Username or email is already taken!";
        }

        // Tutup koneksi database
        $db->close(); 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="registerStyle.css">
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
                <li><a href="login" class="menu-link">Login</a></li>
                <li><a href="register" class="menu-link active">Register</a></li>
            </ul>
        </nav>
    </header>

    <section class="register-section">
        <div class="container">
            <h2>PilahSampah Register</h2>
            <?php if ($error_message): ?>
                <p class="error-message"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form action="register.php" method="post">
                <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="phone">No HP</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group password-container">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <span class="toggle-password" onclick="togglePassword()">👁️</span>
                </div>

                <button type="submit" class="btn" name="register">Register</button>
            </form>

            <div class="confirmation">
                <p>Already have an account? <a href="login">Login here</a></p>
            </div>
        </div>
    </section>

    <footer>
        <p>&copy; 2024 PilahSampah. All rights reserved.</p>
    </footer>

    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.textContent = '🙈'; // Change icon to indicate password is visible
            } else {
                passwordField.type = 'password';
                toggleIcon.textContent = '👁️'; // Change icon to indicate password is hidden
            }
        }
    </script>

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