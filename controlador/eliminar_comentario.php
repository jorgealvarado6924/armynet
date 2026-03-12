<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../database/conexion.php';

$userId = require_login();
$commentId = (int) ($_POST['comment_id'] ?? $_GET['comment_id'] ?? 0);
$postId = (int) ($_POST['post_id'] ?? $_GET['post_id'] ?? 0);
$isAjax = strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';

if ($commentId <= 0 || $postId <= 0) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'Solicitud invalida.']);
        exit;
    }

    redirect_to('read_more.php?post_id=' . $postId);
}

if (!ensure_comments_user_tracking($conexion)) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'No se pudo verificar la autoria del comentario.']);
        exit;
    }

    exit('No se pudo verificar la autoria del comentario.');
}

$commentStmt = $conexion->prepare('SELECT id, post_id, user_id FROM comments WHERE id = ? LIMIT 1');
$commentStmt->bind_param('i', $commentId);
$commentStmt->execute();
$commentResult = $commentStmt->get_result();
$comment = $commentResult ? $commentResult->fetch_assoc() : null;
$commentStmt->close();

if (!$comment || (int) $comment['post_id'] !== $postId) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'Comentario no encontrado.']);
        exit;
    }

    redirect_to('read_more.php?post_id=' . $postId);
}

if ((int) ($comment['user_id'] ?? 0) !== $userId) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'error' => 'No puedes eliminar este comentario.']);
        exit;
    }

    exit('No puedes eliminar este comentario.');
}

$deleteStmt = $conexion->prepare('DELETE FROM comments WHERE id = ?');
$deleteStmt->bind_param('i', $commentId);
$deleted = $deleteStmt->execute();
$deleteStmt->close();

if ($isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => $deleted]);
    exit;
}

redirect_to('read_more.php?post_id=' . $postId);
