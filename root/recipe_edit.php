<?php
include "../config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_recipe = mysqli_real_escape_string($conn, $_POST['id_recipe']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $id_category = mysqli_real_escape_string($conn, $_POST['id_category']);

    // Build update query
    $sql = "UPDATE recipe SET title = '$title', description = '$description', id_category = '$id_category'";
    
    // Handle image upload if new image is provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../images/";
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        $allowTypes = array('jpg','png','jpeg','gif');
        if(in_array($fileType, $allowTypes)){
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $sql .= ", image = '$targetFilePath'";
            } else {
                header("Location: ../admin/dashboard.php?error=Upload gambar gagal!");
                exit;
            }
        } else {
            header("Location: ../admin/dashboard.php?error=Hanya file JPG, JPEG, PNG, & GIF yang diizinkan!");
            exit;
        }
    }
    
    $sql .= " WHERE id_recipe = '$id_recipe'";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../admin/dashboard.php?success=Resep berhasil diupdate!");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>