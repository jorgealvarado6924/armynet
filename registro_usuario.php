<?php
// Incluir la conexión a la base de datos
include('conexion.php');

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

// Si ya está logueado, redirigir al inicio
if (!empty($_SESSION["user_id"])) {
   header("location: index.php");
   exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <link rel="stylesheet" href="styles_login.css?<?php echo time(); ?>">
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
      <h2 class="logo">Army Net</h2>
      <nav class="navigation">
         <a href="index.php#sobremi">Sobre Nosotros</a>
         <a href="index.php#contactame">Contacto</a>
         <a class="boton" href="login.php">Inicia Sesión</a>
      </nav>
   </header>

   <!-- Registro -->
   <div class="cuadrologinre">
      <div class="form-boxregi">
         <h2>Regístrate</h2>

         <!-- Controlador registrar -->
         <?php include('controlador/controlador_registrar.php'); ?>

         <form method="POST" action="controlador/controlador_registrar.php">

            <div class="input-box">
               <span class='icon'><ion-icon name="person-outline"></ion-icon></span>
               <input type="text" name="name" autocomplete="off" required>
               <label>Nombre</label>
            </div>

            <div class="input-box">
               <span class='icon'><ion-icon name="person"></ion-icon></span>
               <input type="text" name="apellido" required autocomplete="off">
               <label>Apellidos</label>
            </div>

            <div class="input-box">
               <span class='icon'><ion-icon name="at-circle"></ion-icon></span>
               <input type="email" name="email" required autocomplete="off">
               <label>E-mail</label>
            </div>

            <div class="input-box">
               <span class='icon'><ion-icon name="mail"></ion-icon></span>
               <input type="text" name="usuario" required autocomplete="off">
               <label>Usuario</label>
            </div>

            <div class="input-box">
               <span class='icon'><ion-icon name="lock-closed-outline"></ion-icon></span>
               <input type="password" name="password" required>
               <label>Contraseña</label>
            </div>

            <div class="input-boxrol">
               <select name="rol" required>
                  <option value="">Selecciona un rol</option>
                  <option value="author">Autor</option>
                  <option value="lector">Lector</option>
               </select>

            </div>
            <br>
            <button class="btn" type="submit" name="register">Registrar</button>
            <div class="login-register">
               <p><a href="login.php">¿Quieres ser autor del blog? <br> Rellena el formulario del final cuando inicies sesión</a></p>
            </div>


         </form>
      </div>
   </div>

   <script src="js/preload.js"></script>
   <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
   <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</body>

</html>