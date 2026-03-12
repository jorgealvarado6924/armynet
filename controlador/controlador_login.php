<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../database/conexion.php';

ensure_session_started();

if (!isset($_POST['btningresar'])) {
    return;
}

$usuario = request_string('usuario', 80);
$password = (string) ($_POST['password'] ?? '');

if ($usuario === '' || $password === '') {
    echo "<div class='alerta-error'>Completa usuario y contrasena.</div>";
    return;
}

$stmt = $conexion->prepare('SELECT id, name, rol, password FROM users WHERE usuario = ? LIMIT 1');

if (!$stmt) {
    echo "<div class='alerta-error'>No se pudo iniciar sesion.</div>";
    return;
}

$stmt->bind_param('s', $usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result ? $result->fetch_assoc() : null;
$stmt->close();

if (!$user || !password_verify($password, $user['password'])) {
    echo "<div class='alerta-error'>Usuario o contrasena incorrectos.</div>";
    return;
}

session_regenerate_id(true);
$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_rol'] = $user['rol'];

redirect_to('index.php');
