<?php
include "../config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Jangan izinkan menghapus user sendiri
    if ($id == $_SESSION['user_id']) {
        header("Location: ../admin/dashboard.php");
        exit;
    }
    
    $sql = "DELETE FROM user WHERE id_user='$id'";

    if (mysqli_query($conn, $sql)) {
        // Langsung redirect tanpa alert
        header("Location: ../admin/dashboard.php");
    } else {
        // Hanya tampilkan alert jika error
        echo "<script>alert('Gagal menghapus user: " . mysqli_error($conn) . "'); window.location.href='../admin/dashboard.php';</script>";
    }
}
?>