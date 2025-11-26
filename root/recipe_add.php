<?php
include "../config.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $id_category = mysqli_real_escape_string($conn, $_POST['id_category']);
    $id_user = $_SESSION['user_id'];

    // Upload gambar
    $targetDir = "../images/";
    $fileName = basename($_FILES["image"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

    // Allow certain file formats
    $allowTypes = array('jpg','png','jpeg','gif');
    if(in_array($fileType, $allowTypes)){
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Simpan ke database
            $sql = "INSERT INTO recipe (id_user, id_category, title, description, created_at, image)
                    VALUES ('$id_user', '$id_category', '$title', '$description', NOW(), '$targetFilePath')";
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Resep berhasil ditambahkan!'); window.location.href='../admin/dashboard.php';</script>";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "<script>alert('Upload gambar gagal.'); window.location.href='../admin/dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Hanya file JPG, JPEG, PNG, & GIF yang diizinkan.'); window.location.href='../admin/dashboard.php';</script>";
    }
}
?>