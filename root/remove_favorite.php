<?php
include "../config.php";
session_start();

// Debug: Log request
error_log("remove_favorite.php called");

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_recipe'])) {
    $id_user = $_SESSION['id_user'];
    $id_recipe = mysqli_real_escape_string($conn, $_POST['id_recipe']);
    
    error_log("Remove - User: $id_user, Recipe: $id_recipe");
    
    // Hapus dari favorite
    $delete = mysqli_query($conn, "DELETE FROM favorite WHERE id_user = '$id_user' AND id_recipe = '$id_recipe'");
    
    if ($delete) {
        echo json_encode(['success' => true]);
    } else {
        error_log("Delete failed: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>