<?php
include "../config.php";
session_start();

// Debug: Log request
error_log("add_favorite.php called");

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_recipe'])) {
    $id_user = $_SESSION['id_user'];
    $id_recipe = mysqli_real_escape_string($conn, $_POST['id_recipe']);
    
    error_log("User: $id_user, Recipe: $id_recipe");
    
    // Cek apakah sudah ada di favorite
    $check = mysqli_query($conn, "SELECT * FROM favorite WHERE id_user = '$id_user' AND id_recipe = '$id_recipe'");
    
    if (!$check) {
        error_log("Check query failed: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
        exit;
    }
    
    if (mysqli_num_rows($check) > 0) {
        echo json_encode(['success' => false, 'message' => 'Already in favorites']);
        exit;
    }
    
    // Get recipe data
    $recipe_query = mysqli_query($conn, "SELECT * FROM recipe WHERE id_recipe = '$id_recipe'");
    if (!$recipe_query) {
        error_log("Recipe query failed: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Recipe not found']);
        exit;
    }
    
    $recipe = mysqli_fetch_assoc($recipe_query);
    if (!$recipe) {
        echo json_encode(['success' => false, 'message' => 'Recipe not found']);
        exit;
    }
    
    // Tambah ke favorite
    $insert = mysqli_query($conn, "INSERT INTO favorite (id_user, id_recipe, created_at) VALUES ('$id_user', '$id_recipe', NOW())");
    
    if ($insert) {
        echo json_encode([
            'success' => true,
            'title' => $recipe['title'],
            'description' => $recipe['description'],
            'image' => $recipe['image']
        ]);
    } else {
        error_log("Insert failed: " . mysqli_error($conn));
        echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>