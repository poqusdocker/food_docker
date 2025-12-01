<?php
$host = "db";
$user = "root";
$pass = "rootpass";
$db   = "food_recipe";

$conn = mysqli_connect($host, $user, $pass, $db, 3306);

if (!$conn) {
    die("Yahh, gagal bre koneknya" . mysqli_connect_error());
}
?>
