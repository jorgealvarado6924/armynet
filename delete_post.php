<?php
session_start();
include('conexion.php');

// Verificar que el usuario esté logueado
if (empty($_SESSION["user_id"])) {
    header("location: login.php");
    exit;
}

$post_id = intval($_GET['post_id']);
$user_id = intval($_SESSION['user_id']);
$user_rol = $_SESSION['user_rol'] ?? '';

// Verificar que el post pertenece al usuario o que es admin
$query = "SELECT * FROM posts WHERE id = $post_id";
$result = mysqli_query($conexion, $query);
$post = mysqli_fetch_assoc($result);


if ($post['author_id'] != $user_id && $user_rol == 'lector') {
    echo "<p>No tienes permiso para eliminar este post</p>";
    exit;
}

$check = mysqli_query($conexion, "SELECT 1 FROM comments WHERE post_id = $post_id");
if (mysqli_num_rows($check) > 0) {
    echo "<script>
        alert('No puedes eliminar este post porque tiene comentarios.');
        window.history.back();
    </script>";
    exit;
}

// Eliminar el post
$delete = mysqli_query($conexion, "DELETE FROM posts WHERE id = $post_id");

if ($delete) {
    echo "<script>
        alert('✅ Post eliminado correctamente');
        window.location.href = 'index.php';
    </script>";
} else {
    echo "<script>
        alert('❌ Error al eliminar el post');
        window.history.back();
    </script>";
}
exit;
?>
