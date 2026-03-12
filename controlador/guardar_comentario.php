<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../database/conexion.php';

header('Content-Type: application/json; charset=utf-8');

$userId = require_login();
$userName = trim((string) ($_SESSION['user_name'] ?? ''));
$message = request_string('message', 1000);
$postId = (int) ($_POST['post_id'] ?? 0);

if ($userName === '' || $message === '' || $postId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos.']);
    exit;
}

ensure_comments_user_tracking($conexion);

$postStmt = $conexion->prepare('SELECT id FROM posts WHERE id = ? LIMIT 1');
$postStmt->bind_param('i', $postId);
$postStmt->execute();
$postExists = $postStmt->get_result();
$postStmt->close();

if (!$postExists || $postExists->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'El post no existe.']);
    exit;
}

$insertStmt = $conexion->prepare('INSERT INTO comments (user_name, message, post_id, user_id) VALUES (?, ?, ?, ?)');

if (!$insertStmt) {
    echo json_encode(['success' => false, 'error' => 'No se pudo guardar el comentario.']);
    exit;
}

$insertStmt->bind_param('ssii', $userName, $message, $postId, $userId);
$saved = $insertStmt->execute();
$commentId = (int) $insertStmt->insert_id;
$insertStmt->close();

if (!$saved) {
    echo json_encode(['success' => false, 'error' => 'No se pudo guardar el comentario.']);
    exit;
}

echo json_encode([
    'success' => true,
    'comment_id' => $commentId,
    'can_delete' => true,
    'user_name' => $userName,
    'message' => $message,
]);
