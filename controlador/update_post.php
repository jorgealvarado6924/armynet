<?php
session_start();
include('conexion.php');

// Verifica sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);
$post_id = 0;

// Seleccion post_id
if (isset($_GET['post_id'])) {
    $post_id = intval($_GET['post_id']);
} elseif (isset($_POST['post_id'])) {
    $post_id = intval($_POST['post_id']);
} else {
    die("<p style='color:red;'>No se proporcionó un ID de post.</p>");
}

// Obtiene datos del post
$query = mysqli_query($conexion, "select * from posts where id = $post_id");
if (!$query || mysqli_num_rows($query) === 0) {
    die("<p style='color:red;'>No se encontró el post solicitado</p>");
}

$data = mysqli_fetch_assoc($query);

// Verifica que el autor sea el único que pueda editar el post seleccionado
if ($data['author_id'] != $user_id) {
    die("<p style='color:red;'>No eres autor del post</p>");
}

// Obtiene categorías
$categories = mysqli_query($conexion, "SELECT * FROM categories");

// Procesa actualización
// Esta cadena hace que no se pueda hacer sql injection
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $category_name = $_POST['category'];
    $resume = $_POST['resume'];
    $content = $_POST['content'];
    $created_at = date('Y-m-d H:i:s');

    // Obtiene el id de la categoría
    $catQuery = mysqli_query($conexion, "select id from categories WHERE category = '$category_name'");
    $catData = mysqli_fetch_assoc($catQuery);
    $category_id = $catData['id'];

    // Conserva imagen anterior
    $name = $data['image'];
    if (!empty($_FILES['image']['name'])) {
        $name = $_FILES['image']['name'];
        $temp_location = $_FILES['image']['tmp_name'];
        $our_location = "img/";
        move_uploaded_file($temp_location, $our_location . $name);
    }

    // Actualiza post
    $updateSQL = "UPDATE posts SET 
                    title='$title',
                    resume='$resume',
                    content='$content',
                    category_id='$category_id',
                    image='$name',
                    created_at='$created_at'
                  WHERE id='$post_id'";

    if (mysqli_query($conexion, $updateSQL)) {
        header("Location: read_more.php?post_id=$post_id");
        exit;
    } else {
        echo "<p style='color:red;'>Error al actualizar el post: " . mysqli_error($conexion) . "</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include('particiones/head.php'); ?>
</head>
<body>
    <?php include('particiones/navbar.php'); ?>

    <section class="createPost">
        
        <form action="update_post.php" method="POST" enctype="multipart/form-data">
            <h1>Actualizar Blog</h1>
            <input type="hidden" name="post_id" value="<?php echo $data['id']; ?>">

            <div>
                <label for="title">Título:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
            </div>

            <div>
                <label for="category">Categoría:</label>
                <select id="category" name="category" required>
                    <?php while ($row = mysqli_fetch_assoc($categories)) { ?>
                        <option value="<?php echo $row['category']; ?>" <?php echo ($row['id'] == $data['category_id']) ? 'selected' : ''; ?>>
                            <?php echo $row['category']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="addpostField">
                <label for="image">Imagen:</label>
                <input type="file" class="customFile" id="image" name="image">
                <p>Imagen actual: <?php echo htmlspecialchars($data['image']); ?></p>
            </div>

            <div>
                <label for="resume">Resumen:</label>
                <textarea id="resume" name="resume" required><?php echo htmlspecialchars($data['resume']); ?></textarea>
            </div>

            <div>
                <label for="content">Contenido:</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($data['content']); ?></textarea>
            </div>

            <input type="submit" value="Actualizar Blog" name="submit" class="btn">
        </form>
    </section>

    <?php include('particiones/footer.php'); ?>
</body>
</html>