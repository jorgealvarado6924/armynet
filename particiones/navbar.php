<?php
include('conexion.php');

if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
}
?>
<nav class="navigation">
    <div class="nombreUsuario"><?php 
    echo "Bienvenido, estas en modo: ". $_SESSION['user_rol'] . " " . $_SESSION['user_name']; ?></div>
    <a href="index.php">Inicio</a>
    <a href="index.php#sobremi">Sobre Nosotros</a>
    <a href="blog.php">Blog</a>
    <a href="add_posts.php">Crea tu Blog</a>
    <a href="index.php#contactame">Contacto</a>
    <a class="btn" href="controlador/cerrarsesion.php">Salir</a>
</nav>