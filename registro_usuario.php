<?php
require_once __DIR__ . '/controlador/helpers.php';
require_once __DIR__ . '/database/conexion.php';

ensure_session_started();

if (!empty($_SESSION['user_id'])) {
    redirect_to('index.php');
}

include __DIR__ . '/controlador/controlador_registrar.php';

$registerError = get_flash('register_error');
$oldInput = get_old_input('registro');
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="estilos/styles_login.css?<?php echo time(); ?>">
    <link rel="stylesheet" href="estilos/responsive_login.css">
    <link href='https://cdn.boxicons.com/fonts/basic/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="favicon/x-icon" href="img/favicon.ico" />
    <title>Army Net</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
    <header>
        <h2 class="logo">Army Net</h2>
        <nav class="navigation">
            <a href="index.php#sobremi">Sobre Nosotros</a>
            <a href="index.php#contactame">Contacto</a>
            <a class="boton" href="login.php">Inicia Sesion</a>
        </nav>
    </header>

    <div class="cuadrologinre">
        <div class="form-boxregi">
            <h2>Registrate</h2>

            <?php if ($registerError) { ?>
                <div class="alerta-error"><?php echo htmlspecialchars($registerError, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php } ?>

            <form method="POST" action="registro_usuario.php">
                <div class="input-box">
                    <span class='icon'><ion-icon name="person-outline"></ion-icon></span>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($oldInput['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" autocomplete="off" required>
                    <label>Nombre</label>
                </div>

                <div class="input-box">
                    <span class='icon'><ion-icon name="person"></ion-icon></span>
                    <input type="text" name="apellido" value="<?php echo htmlspecialchars($oldInput['apellido'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="off">
                    <label>Apellidos</label>
                </div>

                <div class="input-box">
                    <span class='icon'><ion-icon name="at-circle"></ion-icon></span>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($oldInput['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="off">
                    <label>E-mail</label>
                </div>

                <div class="input-box">
                    <span class='icon'><ion-icon name="mail"></ion-icon></span>
                    <input type="text" name="usuario" value="<?php echo htmlspecialchars($oldInput['usuario'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="off">
                    <label>Usuario</label>
                </div>

                <div class="input-box">
                    <span class='icon'><ion-icon name="lock-closed-outline"></ion-icon></span>
                    <input type="password" name="password" required>
                    <label>Contrasena</label>
                </div>

                <div class="input-boxrol">
                    <select name="rol" required>
                        <option value="">Selecciona un rol</option>
                        <option value="author" <?php echo (($oldInput['rol'] ?? '') === 'author') ? 'selected' : ''; ?>>Autor</option>
                        <option value="lector" <?php echo (($oldInput['rol'] ?? '') === 'lector') ? 'selected' : ''; ?>>Lector</option>
                    </select>
                </div>

                <br>
                <button class="btn" type="submit" name="register">Registrar</button>
                <div class="login-register">
                    <p><a href="login.php">Ya tengo cuenta</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="js/preload.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>

</html>
