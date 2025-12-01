<?php
include "../config.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak.");
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Delete related records first
    $sql1 = "DELETE FROM recipe_detail WHERE recipe_id='$id'";
    $sql2 = "DELETE FROM comment WHERE id_recipe='$id'";
    $sql3 = "DELETE FROM favorite WHERE id_recipe='$id'";
    
    // Then delete the recipe
    $sql4 = "DELETE FROM recipe WHERE id_recipe='$id'";

    if (mysqli_query($conn, $sql1) && mysqli_query($conn, $sql2) && mysqli_query($conn, $sql3) && mysqli_query($conn, $sql4)) {
        echo "<script>alert('Recipe berhasil dihapus!'); window.location='../admin/dashboard.php';</script>";
    } else {
        echo "Gagal menghapus: " . mysqli_error($conn);
    }
}
?>