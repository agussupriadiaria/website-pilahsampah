<?php
$hostname = "localhost";
$username = "u682594628_ariaxx";
$password = "2390agUS**";
$databasename = "u682594628_bukutamu";
$port = 3306; // Menentukan port MySQL

// Menghubungkan ke database dengan port yang ditentukan
$db = mysqli_connect($hostname, $username, $password, $databasename, $port);

if ($db->connect_error) {
    echo "Koneksi database error: " . $db->connect_error;
    die("Error");
}
// echo "Koneksi berhasil";
?>