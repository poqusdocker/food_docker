<?php
include "../config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = mysqli_real_escape_string($conn, $_POST['recipe_id']);
    $ingredients = mysqli_real_escape_string($conn, $_POST['ingredients']);
    $steps = mysqli_real_escape_string($conn, $_POST['steps']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $sql = "INSERT INTO recipe_detail (recipe_id, ingredients, steps, notes)
            VALUES ('$recipe_id', '$ingredients', '$steps', '$notes')";
    
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Detail resep berhasil ditambahkan!'); window.location.href='../admin/dashboard.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>