<?php
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../database/conexion.php';

$userId = require_login();
$role = current_user_role();

if ($role !== 'admin') {
    redirect_to('blog.php');
}

echo 'Eres admin';
