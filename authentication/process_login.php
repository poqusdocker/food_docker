<?php
session_start();
require_once '../config.php';

// echo '<pre>';
// print_r($_POST);
// echo '</pre>';
// exit;


$username = $_POST['username'];
$password = $_POST['password'];

$query = "SELECT * FROM user WHERE username = '$username'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    // Karena password belum di-hash, bandingkan biasa dulu
    if ($user['password'] === $password) {
        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect sesuai role
        if ($user['role'] === 'admin') {
            header("Location: ../admin/dashboard.php");
        } else {
            header("Location: ../user/index.php");
        }
        exit;
    } else {
        header("Location: login.php?error=Password salah!");
        exit;
    }
} else {
    header("Location: login.php?error=Username tidak ditemukan!");
    exit;
}
?>
