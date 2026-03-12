<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../database/conexion.php';

ensure_session_started();

if (current_user_id() !== null) {
    redirect_to('index.php');
}

if (!isset($_POST['register'])) {
    return;
}

$nombre = request_string('name', 80);
$apellido = request_string('apellido', 120);
$email = request_string('email', 150);
$usuario = request_string('usuario', 80);
$clave = (string) ($_POST['password'] ?? '');
$rol = (string) ($_POST['rol'] ?? '');

set_old_input('registro', [
    'name' => $nombre,
    'apellido' => $apellido,
    'email' => $email,
    'usuario' => $usuario,
    'rol' => $rol,
]);

if ($nombre === '' || $apellido === '' || $email === '' || $usuario === '' || $clave === '' || $rol === '') {
    set_flash('register_error', 'Completa todos los campos.');
    redirect_to('registro_usuario.php');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    set_flash('register_error', 'Introduce un correo valido.');
    redirect_to('registro_usuario.php');
}

if (!in_array($rol, ['author', 'lector'], true)) {
    set_flash('register_error', 'Selecciona un rol valido.');
    redirect_to('registro_usuario.php');
}

if (strlen($clave) < 8) {
    set_flash('register_error', 'La contrasena debe tener al menos 8 caracteres.');
    redirect_to('registro_usuario.php');
}

$checkStmt = $conexion->prepare('SELECT id FROM users WHERE usuario = ? OR email = ? LIMIT 1');

if (!$checkStmt) {
    set_flash('register_error', 'No se pudo validar el usuario.');
    redirect_to('registro_usuario.php');
}

$checkStmt->bind_param('ss', $usuario, $email);
$checkStmt->execute();
$existingUser = $checkStmt->get_result();

if ($existingUser && $existingUser->num_rows > 0) {
    $checkStmt->close();
    set_flash('register_error', 'El usuario o el correo ya existen.');
    redirect_to('registro_usuario.php');
}

$checkStmt->close();

$passwordHash = password_hash($clave, PASSWORD_BCRYPT);
$insertStmt = $conexion->prepare('INSERT INTO users (name, apellido, email, usuario, password, rol) VALUES (?, ?, ?, ?, ?, ?)');

if (!$insertStmt) {
    set_flash('register_error', 'No se pudo crear la cuenta.');
    redirect_to('registro_usuario.php');
}

$insertStmt->bind_param('ssssss', $nombre, $apellido, $email, $usuario, $passwordHash, $rol);
$created = $insertStmt->execute();
$insertId = (int) $insertStmt->insert_id;
$insertStmt->close();

if (!$created) {
    set_flash('register_error', 'No se pudo crear la cuenta.');
    redirect_to('registro_usuario.php');
}

session_regenerate_id(true);
$_SESSION['user_id'] = $insertId;
$_SESSION['user_name'] = $nombre;
$_SESSION['user_rol'] = $rol;
unset($_SESSION['old_input']['registro']);

redirect_to('index.php');
