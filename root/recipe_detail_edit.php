<?php
include "../config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_detail = mysqli_real_escape_string($conn, $_POST['id_detail']);
    $recipe_id = mysqli_real_escape_string($conn, $_POST['recipe_id']);
    $ingredients = mysqli_real_escape_string($conn, $_POST['ingredients']);
    $steps = mysqli_real_escape_string($conn, $_POST['steps']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $sql = "UPDATE recipe_detail SET 
            recipe_id = '$recipe_id', 
            ingredients = '$ingredients', 
            steps = '$steps', 
            notes = '$notes' 
            WHERE id_detail = '$id_detail'";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ../admin/dashboard.php?success=Detail resep berhasil diupdate!");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>