<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../database/conexion.php';

$userId = require_role('author');
$createdAt = date('Y-m-d H:i:s');

$sql = 'SELECT id, category FROM categories ORDER BY category ASC';
$result = mysqli_query($conexion, $sql);

if (!$result) {
    exit('No se pudieron cargar las categorias.');
}

if (!isset($_POST['submit'])) {
    return;
}

$title = request_string('title', 180);
$categoryName = request_string('category', 120);
$resume = request_string('resume', 400);
$content = request_string('content', 5000);

if ($title === '' || $categoryName === '' || $resume === '' || $content === '') {
    echo "<div class='alerta-error'>Completa todos los campos del post.</div>";
    return;
}

$categoryStmt = $conexion->prepare('SELECT id FROM categories WHERE category = ? LIMIT 1');
$categoryStmt->bind_param('s', $categoryName);
$categoryStmt->execute();
$categoryResult = $categoryStmt->get_result();
$category = $categoryResult ? $categoryResult->fetch_assoc() : null;
$categoryStmt->close();

if (!$category) {
    echo "<div class='alerta-error'>La categoria seleccionada no es valida.</div>";
    return;
}

try {
    $imageName = store_uploaded_image($_FILES['image'] ?? [], dirname(__DIR__) . DIRECTORY_SEPARATOR . 'img', true);
} catch (RuntimeException $exception) {
    echo "<div class='alerta-error'>" . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . "</div>";
    return;
}

$insertStmt = $conexion->prepare(
    'INSERT INTO posts (title, resume, content, category_id, author_id, created_at, image) VALUES (?, ?, ?, ?, ?, ?, ?)'
);

if (!$insertStmt) {
    echo "<div class='alerta-error'>No se pudo guardar el post.</div>";
    return;
}

$categoryId = (int) $category['id'];
$insertStmt->bind_param('sssiiis', $title, $resume, $content, $categoryId, $userId, $createdAt, $imageName);
$saved = $insertStmt->execute();
$insertStmt->close();

if (!$saved) {
    echo "<div class='alerta-error'>No se pudo guardar el post.</div>";
    return;
}

redirect_to('blog.php');
