<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../database/conexion.php';

$userId = require_role('author');
$postId = (int) ($_GET['post_id'] ?? $_POST['post_id'] ?? 0);

if ($postId <= 0) {
    exit('No se proporciono un ID de post valido.');
}

$postStmt = $conexion->prepare('SELECT id, title, resume, content, category_id, author_id, image FROM posts WHERE id = ? LIMIT 1');
$postStmt->bind_param('i', $postId);
$postStmt->execute();
$postResult = $postStmt->get_result();
$data = $postResult ? $postResult->fetch_assoc() : null;
$postStmt->close();

if (!$data) {
    exit('No se encontro el post solicitado.');
}

if ((int) $data['author_id'] !== $userId) {
    exit('No tienes permiso para editar este post.');
}

$categories = [];
$categoriesQuery = mysqli_query($conexion, 'SELECT id, category FROM categories ORDER BY category ASC');

if ($categoriesQuery) {
    while ($row = mysqli_fetch_assoc($categoriesQuery)) {
        $categories[] = $row;
    }
}

if (isset($_POST['submit'])) {
    $title = request_string('title', 180);
    $categoryName = request_string('category', 120);
    $resume = request_string('resume', 400);
    $content = request_string('content', 5000);

    if ($title === '' || $categoryName === '' || $resume === '' || $content === '') {
        exit('Completa todos los campos del post.');
    }

    $categoryStmt = $conexion->prepare('SELECT id FROM categories WHERE category = ? LIMIT 1');
    $categoryStmt->bind_param('s', $categoryName);
    $categoryStmt->execute();
    $categoryResult = $categoryStmt->get_result();
    $category = $categoryResult ? $categoryResult->fetch_assoc() : null;
    $categoryStmt->close();

    if (!$category) {
        exit('La categoria seleccionada no es valida.');
    }

    $imageName = $data['image'];

    try {
        $uploadedImage = store_uploaded_image($_FILES['image'] ?? [], dirname(__DIR__) . DIRECTORY_SEPARATOR . 'img', false);
        if ($uploadedImage !== null) {
            $imageName = $uploadedImage;
        }
    } catch (RuntimeException $exception) {
        exit(htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
    }

    $categoryId = (int) $category['id'];
    $updateStmt = $conexion->prepare(
        'UPDATE posts SET title = ?, resume = ?, content = ?, category_id = ?, image = ? WHERE id = ?'
    );

    if (!$updateStmt) {
        exit('No se pudo preparar la actualizacion del post.');
    }

    $updateStmt->bind_param('sssisi', $title, $resume, $content, $categoryId, $imageName, $postId);
    $updated = $updateStmt->execute();
    $updateStmt->close();

    if (!$updated) {
        exit('No se pudo actualizar el post.');
    }

    redirect_to('read_more.php?post_id=' . $postId);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include('../particiones/head.php'); ?>
</head>
<body>
    <?php include('../particiones/navbar.php'); ?>

    <section class="createPost">
        <form action="update_post.php" method="POST" enctype="multipart/form-data">
            <h1>Actualizar Blog</h1>
            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars((string) $data['id'], ENT_QUOTES, 'UTF-8'); ?>">

            <div>
                <label for="title">Titulo:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8'); ?>" required>
            </div>

            <div>
                <label for="category">Categoria:</label>
                <select id="category" name="category" required>
                    <?php foreach ($categories as $row) { ?>
                        <option value="<?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ((int) $row['id'] === (int) $data['category_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="addpostField">
                <label for="image">Imagen:</label>
                <input type="file" class="customFile" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
                <p>Imagen actual: <?php echo htmlspecialchars($data['image'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <div>
                <label for="resume">Resumen:</label>
                <textarea id="resume" name="resume" required><?php echo htmlspecialchars($data['resume'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div>
                <label for="content">Contenido:</label>
                <textarea id="content" name="content" required><?php echo htmlspecialchars($data['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <input type="submit" value="Actualizar Blog" name="submit" class="btn">
        </form>
    </section>

    <?php include('../particiones/footer.php'); ?>
</body>
</html>
