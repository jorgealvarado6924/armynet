
<!DOCTYPE html>
<html lang="es">

<head>
    <?php 
    include('particiones/head.php');
    ?>
</head>

<body>
    <!-- Inicio de Barra de Navegación -->

    <nav class="navigation">
        <h2 class="logo">Army Net </h2>
        <a href="index.php">Inicio</a>
        <a href="index.php#sobremi">Sobre Nosotros</a>
        <a href="blog.php">Blogs</a>
        <a href="add_blog.php">Crea tu Blog</a>
        <a href="index.php#contactame">Contacto</a>
        <a class="btn" href="controlador/cerrarsesion.php">Salir</a>

    </nav>


    <!-- ********************** -->
    <?php
    include('controlador/guardar_post.php');
    ?>
    <!-- Crear Posts -->
    <section class="createPost">

        <form action="add_blog.php" method="POST" enctype="multipart/form-data">
            <h1>Crear Nuevo Blog</h1>
            <div class="addpostField">
                <label for="title">Título:</label>
                <input type="text" id="title" name="title" required>
            </div>
            <div class="addpostField"> 
                <label for="categories">Categoría:</label>
                <select id="category" name="category" required>
                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                        <option value="<?php echo "{$row['category']}"; ?>"><?php echo "{$row['category']}"; ?></option>
                    <?php } ?>
                </select>
            </div>

            <div class="addpostField"> 
                <label for="image">Imagen:</label>
                <input type="file" class="customFile" id="image" name="image" required>
            </div>
            <div class="addpostField"> 
                <label for="resume">Resumen:</label>
                <input id="resume" name="resume" required></input>
            </div>
            <div class="addpostField"> 
                <label for="content">Contenido:</label>
                <input id="content" name="content" required></input>
            </div>
            <input style="margin-bottom: 0px;" type="submit" value="Crear Blog" name="submit" class="btn">
        </form>
    </section>


    <!-- Footer -->
    <?php include('particiones/footer.php'); ?>
    <!-- *********************** -->
</body>

</html>