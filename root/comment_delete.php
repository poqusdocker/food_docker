<?php
include "../config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Nonaktifkan foreign key check sementara
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");
    
    $sql = "DELETE FROM comment WHERE id_comment='$id'";

    if (mysqli_query($conn, $sql)) {
        // Aktifkan kembali foreign key check
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
        // Langsung redirect tanpa alert
        header("Location: ../admin/dashboard.php");
    } else {
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
        // Hanya tampilkan alert jika error
        echo "<script>alert('Gagal menghapus komentar: " . mysqli_error($conn) . "'); window.location.href='../admin/dashboard.php';</script>";
    }
}
?>