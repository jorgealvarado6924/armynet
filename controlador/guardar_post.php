<?php
session_start();
include('conexion.php');
if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
}

$user_id = $_SESSION['user_id'];
$created_at = date('Y-m-d H:i:s');


if (!isset($_SESSION['user_id'])) {
    header("location:index.php");
    exit;
} else {
    if ($_SESSION['user_rol'] == "author") {
        $sql = "select * from categories";
        $result = mysqli_query($conexion, $sql);
        if (!$result) {
            echo "Error!: {$conexion->error}";
        } else {
            if (isset($_POST['submit'])) {
                $title = $_POST['title'];
                $category = $_POST['category'];
                $resume = $_POST['resume'];
                $content = $_POST['content'];
                $name = $_FILES['image']['name'];
                $temp_location = $_FILES['image']['tmp_name'];
                $our_location = "img/";
                if (!empty($name)) {
                    move_uploaded_file($temp_location, $our_location . $name);
                }
                $sql1 = "select id from categories where category = '$category'";
                $result1 = mysqli_query($conexion, $sql1);
                if ($result1->num_rows > 0) {
                    $row = mysqli_fetch_assoc($result1);
                    $idforcategory = $row['id'];
                }
                $sql2 = "INSERT INTO posts (title, resume, content, category_id, author_id, created_at, image )  VALUES ('$title', '$resume', '$content',  '$idforcategory', '$user_id', '$created_at', '$name')";
                $result2 = mysqli_query($conexion, $sql2);
                if ($result2) {
                    header("location: blog.php");
                    echo "Post Subido Con Éxito";
                }
            }
        }
    } else {
        header("location: index.php");
        echo "<div>No puedes crear un blog, eres lector.</div>";

        exit;
    }
}
?>