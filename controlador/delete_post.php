<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../database/conexion.php';

$userId = require_role('author');
$postId = (int) ($_GET['post_id'] ?? 0);

if ($postId <= 0) {
    redirect_to('blog.php');
}

$postStmt = $conexion->prepare('SELECT id, author_id FROM posts WHERE id = ? LIMIT 1');
$postStmt->bind_param('i', $postId);
$postStmt->execute();
$postResult = $postStmt->get_result();
$post = $postResult ? $postResult->fetch_assoc() : null;
$postStmt->close();

if (!$post) {
    redirect_to('blog.php');
}

if ((int) $post['author_id'] !== $userId) {
    exit('No tienes permiso para eliminar este post.');
}

mysqli_begin_transaction($conexion);

try {
    $deleteCommentsStmt = $conexion->prepare('DELETE FROM comments WHERE post_id = ?');
    $deleteCommentsStmt->bind_param('i', $postId);
    $deleteCommentsStmt->execute();
    $deleteCommentsStmt->close();

    $deletePostStmt = $conexion->prepare('DELETE FROM posts WHERE id = ?');
    $deletePostStmt->bind_param('i', $postId);
    $deletePostStmt->execute();
    $affectedRows = $deletePostStmt->affected_rows;
    $deletePostStmt->close();

    if ($affectedRows !== 1) {
        throw new RuntimeException('No se pudo eliminar el post.');
    }

    mysqli_commit($conexion);
} catch (Throwable $exception) {
    mysqli_rollback($conexion);
    exit('No se pudo eliminar el post.');
}

redirect_to('blog.php');
