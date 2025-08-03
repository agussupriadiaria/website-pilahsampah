<?php
    // Include file koneksi
    include "php/connectphp.php";
    session_start(); // Memulai session

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

    // Mengecek apakah user sudah login
    if (!isset($_SESSION["is_login"])) {
        header("Location: login.php");
        exit(); // Hentikan eksekusi skrip setelah redirect
    }

    // Mengambil informasi pengguna yang sedang login dari session
    $username = $_SESSION["username"];

    // Query untuk mengambil data pengguna dari database berdasarkan username
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Mengecek apakah user ditemukan
    if ($result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
    } else {
        header("Location: login");
        exit();
    }

    // Mengecek apakah form telah disubmit
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Mengambil data dari form
        $fullname = mysqli_real_escape_string($db, $_POST['fullname']);
        $phone = mysqli_real_escape_string($db, $_POST['phone']);
        $address = mysqli_real_escape_string($db, $_POST['address']);
        $email = mysqli_real_escape_string($db, $_POST['email']);
        $accname = mysqli_real_escape_string($db, $_POST['accname']);
        $accnumber = mysqli_real_escape_string($db, $_POST['accnumber']);

        // Mengecek apakah ada file gambar yang diupload
        if (!empty($_FILES['profile-pic']['name'])) {
            $file_extension = pathinfo($_FILES['profile-pic']['name'], PATHINFO_EXTENSION);
            $profile_pic = $username . '.' . $file_extension;
            $target_dir = "uploads/";
            $target_file = $target_dir . $profile_pic;

            if (move_uploaded_file($_FILES["profile-pic"]["tmp_name"], $target_file)) {
                echo "File " . htmlspecialchars(basename($profile_pic)) . " telah diupload.";
            } else {
                echo "Terjadi kesalahan saat mengupload file.";
            }

            $sql = "UPDATE users SET fullname='$fullname', phone='$phone', address='$address', email='$email', profilepicture='$profile_pic', accname='$accname', accnumber='$accnumber' WHERE username='$username'";
        } else {
            $sql = "UPDATE users SET fullname='$fullname', phone='$phone', address='$address', email='$email', accname='$accname', accnumber='$accnumber' WHERE username='$username'";
        }

        if (mysqli_query($db, $sql)) {
            header("Location: editProfSuccess");
            exit();
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($db);
        }
    }

    $stmt->close();
    mysqli_close($db);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="editProfileStyle.css">
    <script>
        function updateWalletOptions() {
            const method = document.getElementById('withdraw-method').value;
            const accnameSelect = document.getElementById('accname');
            const accnumberLabel = document.getElementById('accnumber-label');

            accnameSelect.innerHTML = ''; // Kosongkan dropdown accname

            if (method === 'Bank Transfer') {
                const bankOptions = ['Mandiri', 'BRI', 'BCA', 'BNI'];
                bankOptions.forEach(bank => {
                    const option = document.createElement('option');
                    option.value = bank;
                    option.text = bank;
                    accnameSelect.add(option);
                });
                accnumberLabel.innerHTML = 'Nomor Rekening';
            } else if (method === 'E-Wallet') {
                const ewalletOptions = ['Ovo', 'Dana', 'GoPay'];
                ewalletOptions.forEach(wallet => {
                    const option = document.createElement('option');
                    option.value = wallet;
                    option.text = wallet;
                    accnameSelect.add(option);
                });
                accnumberLabel.innerHTML = 'Nomor E-Wallet';
            }
        }
    </script>
</head>
<body>
    <div class="edit-profile-container">
        <h2>Edit Profile</h2>
        <form action="editProfile.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user_data['fullname']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user_data['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($user_data['address']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="withdraw-method">Withdraw Methode</label>
                <select id="withdraw-method" name="withdraw-method" onchange="updateWalletOptions()" required>
                    <option value="">Choose an option</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="E-Wallet">E-Wallet</option>
                </select>
            </div>

            <div class="form-group">
                <label for="accname">Bank / E-Wallet</label>
                <select id="accname" name="accname" required>
                    <option value="">Choose an option</option>
                </select>
            </div>

            <div class="form-group">
                <label id="accnumber-label" for="accnumber">Bank / E-Wallet Numbers</label>
                <input type="text" id="accnumber" name="accnumber" value="<?php echo htmlspecialchars($user_data['accnumber']); ?>" required>
            </div>

            <button type="submit" class="btn-submit">Save Changes</button>
        </form>
        <a href="profile" class="btn-cancel">Cancel</a>
    </div>
</body>
</html>
