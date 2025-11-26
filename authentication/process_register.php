<?php
require_once '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = 'user';

    // Cek apakah username/email sudah digunakan
    $check = "SELECT * FROM user WHERE email = '$email' OR username = '$username'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        header("Location: register.php?error=Username atau email sudah digunakan!");
        exit;
    }

    // Insert ke database
    $query = "INSERT INTO user (username, email, password, role, created_at)
              VALUES ('$username', '$email', '$password', '$role', NOW())";

    if (mysqli_query($conn, $query)) {
    header("Location: login.php?success=Registrasi berhasil! Silakan login.");
    exit;
    } else {
        echo "❌ Gagal registrasi: " . mysqli_error($conn);
    }
}
?>
