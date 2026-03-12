<?php
require_once __DIR__ . '/controlador/helpers.php';
require_once __DIR__ . '/database/conexion.php';

ensure_session_started();
$currentUserId = require_login();
ensure_comments_user_tracking($conexion);

$getId = (int) ($_GET['post_id'] ?? 0);

if ($getId <= 0) {
    exit('<p>No se encontro el post.</p>');
}

$postStmt = $conexion->prepare('SELECT p.*, u.name AS author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id WHERE p.id = ? LIMIT 1');
$postStmt->bind_param('i', $getId);
$postStmt->execute();
$result = $postStmt->get_result();

if (!$result || $result->num_rows === 0) {
    $postStmt->close();
    exit('<p>No se encontro el post.</p>');
}

$data = $result->fetch_assoc();
$postStmt->close();

$commentsStmt = $conexion->prepare('SELECT id, user_name, message, post_id, user_id FROM comments WHERE post_id = ? ORDER BY id DESC');
$commentsStmt->bind_param('i', $getId);
$commentsStmt->execute();
$getComment = $commentsStmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include('particiones/head.php'); ?>
</head>

<body>
    <?php include('particiones/navbar.php'); ?>

    <div class="detailsBlog">
        <div class="updatePost">
            <a href="controlador/update_post.php?post_id=<?php echo $data['id']; ?>">Actualiza tu post</a> /
            <a href="controlador/delete_post.php?post_id=<?php echo $data['id']; ?>">Elimina tu post</a><br>
        </div>
        <br>
        <div class="blogDetailsDiv">
            <img src="img/<?php echo htmlspecialchars($data['image'], ENT_QUOTES, 'UTF-8'); ?>" alt="Imagen del post" width="640" height="360">
            <div class="blogdetailsDivContent">
                <h3><?php echo htmlspecialchars($data['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <b><?php echo htmlspecialchars($data['author_name'] ?? 'Autor desconocido', ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($data['created_at'], ENT_QUOTES, 'UTF-8'); ?></b>
                <p><?php echo nl2br(htmlspecialchars($data['content'], ENT_QUOTES, 'UTF-8')); ?></p>
            </div>
        </div>

        <div class="blogCategory">
            <h4>Blogs Recientes</h4>
            <?php
            $recientes = mysqli_query($conexion, "SELECT p.*, u.name AS author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.id DESC LIMIT 4");
            if ($recientes && mysqli_num_rows($recientes)) {
                while ($blogData = mysqli_fetch_assoc($recientes)) {
                    echo '<a href="read_more.php?post_id=' . (int) $blogData['id'] . '">' . htmlspecialchars($blogData['title'], ENT_QUOTES, 'UTF-8') . '</a>';
                }
            } else {
                echo '<p>No hay blogs recientes.</p>';
            }
            ?>
        </div>
    </div>

    <div class="comentarios">
        <?php
        if ($getComment && mysqli_num_rows($getComment)) {
            while ($comment = mysqli_fetch_assoc($getComment)) {
                $canDelete = (int) ($comment['user_id'] ?? 0) === $currentUserId;
                ?>
                <div class="individualComment commentLeft" id="comment-<?php echo (int) $comment['id']; ?>">
                    <?php if ($canDelete) { ?>
                        <form class="deleteCommentForm" method="POST" action="controlador/eliminar_comentario.php">
                            <input type="hidden" name="comment_id" value="<?php echo (int) $comment['id']; ?>">
                            <input type="hidden" name="post_id" value="<?php echo $getId; ?>">
                            <button type="submit" class="deleteCommentBtn" title="Eliminar comentario" aria-label="Eliminar comentario">&times;</button>
                        </form>
                    <?php } ?>

                    <div class="commentImg">
                        <img src="img/iniciodesesion.png" alt="comentario">
                    </div>
                    <div class="commentContent">
                        <b><?php echo htmlspecialchars($comment['user_name'], ENT_QUOTES, 'UTF-8'); ?></b>
                        <p><?php echo htmlspecialchars($comment['message'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<h2>No hay comentarios todavia...</h2>';
        }
        $commentsStmt->close();
        ?>
    </div>

    <form class="commentForm" method="POST">
        <input type="hidden" name="post_id" value="<?php echo $getId; ?>">
        <input type="text" placeholder="Nombre" name="user_name" value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" readonly>
        <textarea name="message" placeholder="Comentario" required></textarea>
        <input type="submit" name="commentSubmit" value="Enviar" class="btn">
    </form>

    <script>
        $(document).ready(function() {
            $(".commentForm").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "controlador/guardar_comentario.php",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (!response.success) {
                            alert("Error al guardar el comentario: " + response.error);
                            return;
                        }

                        const safeUserName = $('<div>').text(response.user_name).html();
                        const safeMessage = $('<div>').text(response.message).html();
                        const commentId = Number(response.comment_id || 0);

                        $(".comentarios").prepend(`
                            <div class="individualComment commentLeft" id="comment-${commentId}">
                                <form class="deleteCommentForm" method="POST" action="controlador/eliminar_comentario.php">
                                    <input type="hidden" name="comment_id" value="${commentId}">
                                    <input type="hidden" name="post_id" value="<?php echo $getId; ?>">
                                    <button type="submit" class="deleteCommentBtn" title="Eliminar comentario" aria-label="Eliminar comentario">&times;</button>
                                </form>
                                <div class="commentImg"><img src="img/iniciodesesion.png" alt="comentario"></div>
                                <div class="commentContent">
                                    <b>${safeUserName}</b>
                                    <p>${safeMessage}</p>
                                </div>
                            </div>
                        `);

                        $(".commentForm textarea[name='message']").val('');
                    },
                    error: function() {
                        alert("Error al conectar con el servidor.");
                    }
                });
            });

            $(document).on("submit", ".deleteCommentForm", function(e) {
                e.preventDefault();

                const form = $(this);

                $.ajax({
                    type: "POST",
                    url: "controlador/eliminar_comentario.php",
                    data: form.serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (!response.success) {
                            alert("Error al eliminar el comentario: " + (response.error || 'Solicitud no valida.'));
                            return;
                        }

                        form.closest(".individualComment").remove();
                    },
                    error: function() {
                        alert("Error al conectar con el servidor.");
                    }
                });
            });
        });
    </script>

    <?php include('particiones/footer.php'); ?>
    <div id="subirboton"><i class="fa fa-chevron-circle-up fa-3x"></i></div>
    <script src="js/boton_subir.js"></script>
</body>

</html>
