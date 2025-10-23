<?php
include('conexion.php');
session_start();
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

    <!-- Blog Navigation category -->
    <div class="blogNav">

        <?php
        $enlace = mysqli_query($conexion, "select * from categories");
        if (mysqli_num_rows($enlace)) {
            while ($data = mysqli_fetch_assoc($enlace)) {
        ?>
                <span class="<?php echo ($data['id'] == 1) ? "activeBlog" : ""; ?>" id="<?php echo $data['category']; ?>"
                    onclick="showBlogs('#<?php echo $data['category']; ?>Blogs','#<?php echo $data['category']; ?>');"><?php echo $data['category']; ?></span>
        <?php
            }
        }
        ?>


    </div>
    <!-- BLOGS -->
    <!-- BLOGS ARMAMENTO -->
    <?php
    $enlace2 = mysqli_query($conexion, "select * from categories");
    if (mysqli_num_rows($enlace2)) {
        while ($cate = mysqli_fetch_assoc($enlace2)) {

    ?>
            <div class="blogs <?php echo ($cate['id'] == 1) ? "" : "hideClass" ?>" id="<?php echo $cate['category']; ?>Blogs">
                <?php
                $category = $cate['id'];
                $blogs = mysqli_query($conexion, " select p.*, u.name as author_name from posts p left join users u ON p.author_id = u.id where p.category_id = $category");
                if (mysqli_num_rows($blogs)) {
                    while ($blogData = mysqli_fetch_assoc($blogs)) {
                ?>
                        <div class="blogIndividual">
                            <div class="blogImg">
                                <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>">
                                <img src="img/<?php echo $blogData['image']; ?>" alt="fusil 416" srcset="" width="640px" height="360px"></a>
                            </div>
                            <div class="contenidoBlog">
                                <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>">
                                    <h3><?php echo $blogData['title']; ?></h3>
                                </a>
                                <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>"><b><?php echo $blogData['author_name']; ?>, <?php echo $blogData['created_at']; ?></b></a>
                                <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>">
                                    <p><?php echo $blogData['resume']; ?></p>
                                </a>
                                <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>" class="btn">Lee más</a>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
            </div>
    <?php
        }
    }
    ?>


    <!-- **************************** -->

    <!-- Footer -->
    <?php include('particiones/footer.php'); ?>
    <!-- *********************** -->

    <!-- SCRIPT -->
    <script>
        function showBlogs(blogDiv, blogNav) {
            <?php
            $enlacescr = mysqli_query($conexion, "select * from categories");
            if (mysqli_num_rows($enlacescr)) {
                while ($datascr = mysqli_fetch_assoc($enlacescr)) {
            ?>
                    $('#<?php echo $datascr['category']; ?>Blogs').addClass('hideClass');
                    $('#<?php echo $datascr['category']; ?>').removeClass('activeBlog');
            <?php
                }
            }
            ?>
            $(blogDiv).removeClass('hideClass');
            $(blogNav).addClass('activeBlog');
        }
    </script>


    <!-- ************************** -->
</body>

</html>