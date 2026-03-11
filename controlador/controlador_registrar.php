<?php
session_start();
include("database/conexion.php");

if (!empty($_SESSION["user_id"])) {
    header("Location: ../index.php");
    exit;
}

if (isset($_POST['register'])) {
    if (empty($_POST['name']) || empty($_POST['apellido']) || empty($_POST['email']) || empty($_POST['usuario']) || empty($_POST['password']) || empty($_POST['rol'])) {
        $_SESSION['error'] = "Rellena todos los campos";
        header("Location: ../registro.php");
        exit;
    }

    $nombre = trim($_POST['name']);
    $apellido = trim($_POST['apellido']);
    $email = trim($_POST['email']);
    $usuario = trim($_POST['usuario']);
    $rol = trim($_POST['rol']);
    $clave = $_POST['password'];

    $password_hash = password_hash($clave, PASSWORD_BCRYPT);

    $checkUser = $conexion->prepare("SELECT id FROM users WHERE usuario = ? OR email = ? LIMIT 1");
    if (!$checkUser) {
        $_SESSION['error'] = "Error al validar el usuario";
        header("Location: ../registro.php");
        exit;
    }

    $checkUser->bind_param("ss", $usuario, $email);
    $checkUser->execute();
    $existingUser = $checkUser->get_result();

    if ($existingUser && $existingUser->num_rows > 0) {
        $_SESSION['error'] = "El usuario o correo ya están registrados";
        $checkUser->close();
        header("Location: ../registro.php");
        exit;
    }
    $checkUser->close();

    $insertUser = $conexion->prepare("INSERT INTO users (name, apellido, email, usuario, password, rol) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$insertUser) {
        $_SESSION['error'] = "Error al preparar el registro";
        header("Location: ../registro.php");
        exit;
    }

    $insertUser->bind_param("ssssss", $nombre, $apellido, $email, $usuario, $password_hash, $rol);

    if ($insertUser->execute()) {
        $insertUser->close();
        header("Location: ../index.php");
        exit;
    }

    $insertUser->close();
    $_SESSION['error'] = "Error al registrar el usuario";
    header("Location: ../registro.php");
    exit;
}
?>
