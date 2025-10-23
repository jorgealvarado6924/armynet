<?php
session_start();
include("conexion.php");
$getId = intval($_GET['post_id']);
$result = mysqli_query($conexion, "select p.*, u.name as author_name from posts p left join users u on p.author_id = u.id where p.id = $getId");

if (!$result) {
    die("Error en la consulta");
}

if (mysqli_num_rows($result) === 0) {
    echo "<p>No se encontró el post.</p>";
    exit;
}

$data = mysqli_fetch_assoc($result); 

?>


<!DOCTYPE html>
<html lang="es">

<head>
    <?php include('particiones/head.php'); ?>
</head>

<body>
    <!-- Inicio Barra de Navegación -->
    <?php include('particiones/navbar.php'); ?>
    <!-- ********************** -->

    <!-- Contenido del Post -->
    <div class="detailsBlog">

        <div class="updatePost">
            <a href="update_post.php?post_id=<?php echo $data['id']; ?>">Actualiza tu post</a> /
            <a href="delete_post.php?post_id=<?php echo $data['id']; ?>">Elimina tu post</a><br>

        </div>
        <br>
        <div class="blogDetailsDiv"> <img src="img/<?php echo htmlspecialchars($data['image']); ?>" alt="Imagen del post" width="640" height="360">
            <div class="blogdetailsDivContent">
                <!-- Evita que el usuario meta código HTML o JavaScript que se ejecute en la página. Protege contra ataques -->
                <h3><?php echo htmlspecialchars($data['title']); ?></h3>
                <b> <?php echo htmlspecialchars($data['author_name'] ?? 'Autor desconocido'); ?>, <?php echo htmlspecialchars($data['created_at']); ?> </b>
                <!-- Convierte los saltos de línea (\n) del texto en etiquetas <br> de HTML -->
                <p><?php echo nl2br(htmlspecialchars($data['content'])); ?></p>
            </div>

        </div>



        <!-- Sección de Blogs Recientes  -->
        <div class="blogCategory">
            <h4>Blogs Recientes</h4>
            <?php $recientes = mysqli_query($conexion, "SELECT p.*, u.name AS author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.id DESC LIMIT 4");
            if (mysqli_num_rows($recientes)) {
                while ($blogData = mysqli_fetch_assoc($recientes)) {
                    echo '<a href="read_more.php?post_id=' . $blogData['id'] . '">' . htmlspecialchars($blogData['title']) . '</a>';
                }
            } else {
                echo "<p>No hay blogs recientes.</p>";
            } ?>
        </div>
    </div>


    <!-- Comentarios -->
    <div class="comentarios">
        <?php $getComment = mysqli_query($conexion, "SELECT * FROM comments WHERE post_id=$getId ORDER BY id DESC");
        if (mysqli_num_rows($getComment)) {
            while ($comment = mysqli_fetch_assoc($getComment)) {
                echo '  <div class="individualComment commentLeft"> 
                            <div class="commentImg"> <img src="img/iniciodesesion.png" alt="comentario"> </div> 
                                <div class="commentContent"> 
                                    <b>' . htmlspecialchars($comment['user_name']) . '</b> 
                                    <p>' . htmlspecialchars($comment['message']) . '</p> 
                                </div> 
                            </div>';
            }
        } else {
            echo "<h2>No hay comentarios todavía...</h2>";
        } ?> </div>

    <!-- Formulario de Comentario -->

    <form class="commentForm" method="POST">
        <input type="hidden" name="post_id" value="<?php echo $getId; ?>">
        <input type="text" placeholder="Nombre" name="user_name" autocomplete="" required>
        <textarea name="message" placeholder="Comentario" required></textarea>
        <input type="submit" name="commentSubmit" value="Enviar" class="btn">
    </form>
    <script>
        $(document).ready(function() {
            $(".commentForm").on("submit", function(e) {
                //Sirve para enviar un formulario sin recargar la página
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    //Aqui se procesará los comentarios para guardarse en la base de datos
                    url: "guardar_comentario.php",
                    data: $(this).serialize(),
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            const safeuser_name = $('<div>').text(response.user_name).html();
                            const safeMessage = $('<div>').text(response.message).html();
                            $(".comentarios").prepend(`
                             <div class="individualComment commentLeft">
                                <div class="commentImg"><img src="img/iniciodesesion.png" alt="comentario"></div>
                                <div class="commentContent">
                                    <b>${safeuser_name}</b>
                                    <p>${safeMessage}</p>
                                </div>
                            </div>
                        `);
                            $(".commentForm")[0].reset();
                        } else {
                            alert("Error al guardar el comentario: " + response.error);
                        }
                    },
                    error: function() {
                        alert("Error al conectar con el servidor.");
                    }
                });
            });
        });
    </script>

    <!-- Footer -->

    <?php include('particiones/footer.php'); ?>
    <div id="subirboton"> <i class="fa fa-chevron-circle-up fa-3x"></i> </div>
    <script src="js/main.js"></script>

</body>

</html>