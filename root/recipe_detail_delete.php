<?php
session_start();
include "../config.php";

// Cek apakah user adalah admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../authentication/login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_detail = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Hapus recipe detail
    $deleteQuery = "DELETE FROM recipe_detail WHERE id_detail = '$id_detail'";
    
    if (mysqli_query($conn, $deleteQuery)) {
        header("Location: ../admin/dashboard.php?success=Recipe detail deleted successfully");
        exit();
    } else {
        header("Location: ../admin/dashboard.php?error=Failed to delete recipe detail: " . mysqli_error($conn));
        exit();
    }
} else {
    header("Location: ../admin/dashboard.php?error=Invalid request");
    exit();
}
?>