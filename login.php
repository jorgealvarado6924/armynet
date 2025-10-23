<?php
include('conexion.php');
include("controlador/controlador_login.php");
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="styles_login.css">
   <link rel="stylesheet" href="responsive_login.css">
   <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
   <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
   <link rel="icon" type="favicon/x-icon" href="img/favicon.ico" />
   <title>Army Net</title>
   <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

   <!-- Preload -->
   <div id="preloader" class="preloader">
      <img src="img/preload.gif" alt="Cargando...">
   </div>
</head>

<body>
   <header>
      <h2 class="logo">Army Net </h2>
      <nav class="navigation">
         <a href="index.php#sobremi">Sobre Nosotros</a>
         <a href="#contactame">Contacto</a>
         <a class="boton" href="registro_usuario.php">Regístrate</a>
      </nav>
   </header>

   <!-- Login -->
   <div class="cuadrologin">
      <div class="form-boxlogi">
         <h2>Iniciar sesión</h2>
         <form method="POST" action="#">
            <div class="input-box">
               <span class='icon'>
                  <ion-icon name="person-outline"></ion-icon></span>
               <input type="text" name="usuario" required autocomplete="off">
               <label>Usuario</label>
            </div>
            <div class="input-box">
               <span class='icon'>
                  <ion-icon name="lock-closed-outline"></ion-icon></span>
               <input type="password" name="password" required>
               <label>Contraseña</label>
            </div>
            <div class="forgotuser">
               <label><input type="checkbox">
                  Recuérdame</label>
               <a href="#"> Olvidé mi contraseña</a>
            </div>
            <button name="btningresar" class="btn" type="submit" value="INICIAR SESION">Ingresar</button>

            <div class="login-register">
               <p>Créate una cuenta <a class="register-link" href="registro_usuario.php">Registrarse</a></p>
            </div>
            <div class="login-register">
               <p> <a href="registro_usuario.php">¿Quieres ser autor del blog?</a></p>
            </div>
         </form>
      </div>
   </div>

   <script src="js/preload.js"></script>
   <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
   <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

   <!-- Footer -->
   <!-- <?php include('particiones/footer.php'); ?> -->
   <!-- *********************** -->

</body>

</html>