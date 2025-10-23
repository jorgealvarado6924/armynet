<?php
session_start();
include('conexion.php');

if (!isset($_SESSION["user_id"])) {
    header("location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="es">

<head>
    <?php include('particiones/head.php'); ?>
</head>

<body>
    <header>
    <!-- Inicio de Barra de Navegación -->
    <?php include('particiones/navbar.php'); ?>
</header>
    <!-- ********************** -->

    <!-- Boton Subir -->
    <div id="subirboton">
        <i class="fa fa-chevron-circle-up fa-3x"></i>
    </div>


    <!-- Banner -->
    <section class="banner">
        <div class="bannerThought">
            <h1><span>Aprende </span>Sobre Armamento & Tácticas de Combate</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque
                ornare rhoncus mauris, nec finibus urna suscipit non. In in risus ut
                diam eleifend pharetra. Vestibulum at tincidunt dui, luctus condimentum
                lorem. Nam vel libero vitae sem aliquam rutrum. Curabitur id diam quis
                justo rhoncus luctus. In hac habitasse platea dictumst. Ut sodales finibus porta.
                Vivamus nec pretium metus. Nunc eget dolor viverra, elementum erat quis, efficitur lacus.
            </p>
            <a href="blog.php" class="btn">Explora más blogs</a>
        </div>
        <div class="bannerImg">
            <img src="img/soldado.png" alt="LoginMilitar" srcset="" width="300" height="500">
        </div>
    </section>
    <!-- *********************** -->

    <!-- Sobre Nosotros -->
    <h1 id="sobremi" class="heading">Sobre Nosotros</h1>
    <div class="aboutUs" id="about">
        <div class="aboutImg">
            <img src="img/favicon.ico" alt="LoginArmyNet" width="500" height="500">
        </div>
        <div class="aboutContent">
            <p><span>Mi nombre es Jorge Alvarado </span>Lorem ipsum, dolor sit amet consectetur
                adipisicing elit. Ipsum, corrupti! Vel nobis distinctio facere dolorum numquam, quis
                a qui nihil, accusamus dolore pariatur quam corporis magnam quos ipsam, s dolore pariatur
                quam corporis magnam quos ipsam, s dolore pariatur quam corporis magnam quos ipsam, s dolore
                pariatur quam corporis magnam quos ipsam
            </p>
        </div>
    </div>

    <!-- Blogs Recientes -->
    <div class="recentBlogs" id="ourBlogs">
        <h1 class="heading" style="color: #b0a059;">Blogs Recientes</h1>
        <div class="blogsDiv">
            <?php
            $enlace = mysqli_query($conexion, "SELECT p.*, u.name AS author_name FROM posts p LEFT JOIN users u ON p.author_id = u.id ORDER BY p.id DESC LIMIT 3");
            if (mysqli_num_rows($enlace)) {
                while ($blogData = mysqli_fetch_assoc($enlace)) {

            ?>
                    <div class="individualBlogReciente">
                        <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>"><img src="img/<?php echo $blogData['image'] ?>" alt="armamentoTactico"></a>
                        <div class="blogRecienteContenido">
                            <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>">
                                <h4><?php echo substr($blogData['title'], 0, 35); ?>...</h4>
                            </a>
                            <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>"><b><?php echo "Por, " . $blogData['author_name'] ?>, <?php echo $blogData['created_at'] ?></b></a>
                            <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>">
                                <p><?php echo $blogData['resume'] ?></p>
                            </a>
                            <a href="read_more.php?post_id=<?php echo $blogData['id']; ?>" class="btn"> Lee más </a>
                        </div>
                    </div>
            <?php
                }
            }
            ?>


        </div>
    </div>

    <!-- Contacto -->

    <h1 class="heading">Contáctame</h1>
    <div class="contacto" id="contactame">
        <div class="contactoImg">
            <img src="img/tactifotos/formconta.png" alt="caso Tactico" srcset="">
        </div>
        <form action="" method="POST">
            <h2>¿Te gustaría ser autor del blog? Rellena el formulario</h2>
            <div class="formularioDireccion">
                <div>
                    <i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>
                    <p>Dirección: <br><span>Madrid, España</span></p>
                </div>
                <div>
                    <i class="fa fa-envelope-open fa-2x" fa-2x aria-hidden="true"></i>
                    <p>E-Mail: <br><span>jorgealvarado6924@gmail.com</span></p>
                </div>
                <div>
                    <i class="fa fa-phone fa-2x" aria-hidden="true"></i>
                    <p>Teléfono: <br><span>+(34) 612242946</span></p>
                </div>

            </div>
            <input type="text" class="formField" placeholder="Nombre" name="name" autocomplete="name" required>
            <input type="text" class="formField" placeholder="Teléfono" name="phone" autocomplete="phone" required>
            <input type="email" class="formField" placeholder="Email " name="email" autocomplete="email" required>
            <input type="text" class="formField" placeholder="Tema" name="subject" required>
            <textarea name="message" class="formField" placeholder="Mensaje" required></textarea>
            <div class="formBtn">
                <input type="Submit" class="btn" value="Enviar" name="contactSubmit">
                <input type="Reset" class="btn" value="Reset" name="reset">
                <?php
                // Conexion con formulario de contacto
                if (isset($_POST['contactSubmit'])) {
                    $name = $_POST['name'];
                    $email = $_POST['email'];
                    $phone = $_POST['phone'];
                    $subject = $_POST['subject'];
                    $message = $_POST['message'];

                    // Esto hace el formulario mucho más seguro, evitando inyeccion de sql
                    $stmt = $conexion->prepare("INSERT INTO contact (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssss", $name, $phone, $email, $subject, $message);
                    $stmt->execute();
                    $stmt->close();
                    echo ("<div>Formulario de contacto enviado exitosamente</div>");
                }
                ?>
            </div>
        </form>

    </div>
    <!-- Footer -->
    <?php include('particiones/footer.php'); ?>
    <!-- *********************** -->
    <script src="js/main.js"></script>
</body>

</html>