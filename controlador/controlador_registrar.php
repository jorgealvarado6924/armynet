<?php
//Este método sivre para que encuentre el archivo donde sea que este su carpeta
include(__DIR__ . "/../conexion.php");

if (!empty($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit;
}

// Procesar formulario
if (isset($_POST['register'])) {

    // Validar campos
    if (empty($_POST['name']) || empty($_POST['apellido']) || empty($_POST['email']) || empty($_POST['usuario']) || empty($_POST['password']) || empty($_POST['rol'])) {
        $_SESSION['error'] = "Rellena todos los campos";
        header("Location: ../registro.php");
        exit;
    }

    // Preparar datos
    $nombre   = $_POST['name'];
    $apellido = $_POST['apellido'];
    $email    = $_POST['email'];
    $usuario  = $_POST['usuario'];
    $rol      = $_POST['rol'];
    $clave    = $_POST['password'];

    // Verificar si ya existe usuario o email
    $checkUser = $conexion->query("SELECT * FROM users WHERE usuario='$usuario' OR email='$email'");
    if ($checkUser && $checkUser->num_rows > 0) {
        $_SESSION['error'] = "El usuario o correo ya están registrados";
        header("Location: ../registro.php");
        exit;
    }
    $sql = $conexion->query("insert into users (name, apellido, email, usuario, password, rol)
                             values ('$nombre', '$apellido', '$email', '$usuario', '$clave', '$rol')");

    if ($sql) {
        session_start();
        header("Location: ../index.php");
        exit;
    } else {
        $_SESSION['error'] = "Error al registrar el usuario";
        header("Location: ../registro.php");
        exit;
    }
}
?>
