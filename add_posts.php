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
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include('particiones/head.php');
    ?>
</head>

<body>
    <!-- Inicio de Barra de Navegación -->

    <nav class="navigation">
        <h2 class="logo">Army Net </h2>
        <a href="index.php">Inicio</a>
        <a href="index.php#sobremi">Sobre Nosotros</a>
        <a href="blog.php">Blogs</a>
        <a href="add_posts.php">Crea tu Blog</a>
        <a href="index.php#contactame">Contacto</a>
        <a class="btn" href="controlador/cerrarsesion.php">Salir</a>

    </nav>


    <!-- ********************** -->

    <!-- Crear Posts -->
    <section class="createPost">

        <form action="add_posts.php" method="POST" enctype="multipart/form-data">
            <h1>Crear Nuevo Blog</h1>
            <div class="addpostField">
                <label for="title">Título:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="addpostField"> 
                <label for="categories">Categoría:</label>
                <select id="category" name="category" required>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <option value="<?php echo "{$row['category']}"; ?>"><?php echo "{$row['category']}"; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="addpostField"> 
                <label for="image">Imagen:</label>
                <input type="file" class="customFile" id="image" name="image" required>
            </div>
            <div class="addpostField"> 
                <label for="resume">Resumen:</label>
                <input id="resume" name="resume" required></input>
            </div>
            <div class="addpostField"> 
                <label for="content">Contenido:</label>
                <input id="content" name="content" required></input>
            </div>
            <input style="margin-bottom: 0px;" type="submit" value="Crear Blog" name="submit" class="btn">
        </form>
    </section>


    <!-- Footer -->
    <?php include('particiones/footer.php'); ?>
    <!-- *********************** -->
</body>

</html>