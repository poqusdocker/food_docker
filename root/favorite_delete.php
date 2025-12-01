<?php
include "../config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "DELETE FROM favorite WHERE id_favorite='$id'";

    if (mysqli_query($conn, $sql)) {
        // Langsung redirect tanpa alert
        header("Location: ../admin/dashboard.php");
    } else {
        // Hanya tampilkan alert jika error
        echo "<script>alert('Gagal menghapus favorite: " . mysqli_error($conn) . "'); window.location.href='../admin/dashboard.php';</script>";
    }
}
?>