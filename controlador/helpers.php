<?php

function ensure_session_started(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function app_base_path(): string
{
    $documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
    $projectRoot = realpath(dirname(__DIR__));

    if ($documentRoot && $projectRoot) {
        $documentRoot = str_replace('\\', '/', $documentRoot);
        $projectRoot = str_replace('\\', '/', $projectRoot);

        if (str_starts_with($projectRoot, $documentRoot)) {
            $relative = trim(substr($projectRoot, strlen($documentRoot)), '/');
            return $relative === '' ? '' : '/' . $relative;
        }
    }

    return '';
}

function url_for(string $path = ''): string
{
    $basePath = rtrim(app_base_path(), '/');
    $cleanPath = '/' . ltrim($path, '/');

    return $basePath . ($cleanPath === '/' ? '' : $cleanPath);
}

function redirect_to(string $path): void
{
    $target = url_for($path);

    if (!headers_sent()) {
        header('Location: ' . $target);
        exit;
    }

    echo '<script>window.location.href=' . json_encode($target) . ';</script>';
    exit;
}

function current_user_id(): ?int
{
    ensure_session_started();
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function current_user_role(): ?string
{
    ensure_session_started();
    return $_SESSION['user_rol'] ?? null;
}

function require_login(): int
{
    $userId = current_user_id();

    if ($userId === null) {
        redirect_to('login.php');
    }

    return $userId;
}

function require_role(string $role): int
{
    $userId = require_login();

    if (current_user_role() !== $role) {
        redirect_to('index.php');
    }

    return $userId;
}

function store_uploaded_image(array $file, string $destinationDir, bool $required = true): ?string
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;

    if ($error === UPLOAD_ERR_NO_FILE) {
        if ($required) {
            throw new RuntimeException('Debes seleccionar una imagen.');
        }

        return null;
    }

    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('No se pudo subir la imagen.');
    }

    $tmpName = $file['tmp_name'] ?? '';
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        throw new RuntimeException('La subida de imagen no es valida.');
    }

    $mimeType = mime_content_type($tmpName);
    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
    ];

    if (!isset($allowedMimeTypes[$mimeType])) {
        throw new RuntimeException('La imagen debe ser JPG, PNG, WEBP o GIF.');
    }

    $fileName = bin2hex(random_bytes(16)) . '.' . $allowedMimeTypes[$mimeType];
    $targetPath = rtrim($destinationDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        throw new RuntimeException('No se pudo guardar la imagen.');
    }

    return $fileName;
}

function request_string(string $key, int $maxLength = 255): string
{
    $value = trim((string) ($_POST[$key] ?? ''));
    return mb_substr($value, 0, $maxLength);
}

function set_flash(string $key, string $message): void
{
    ensure_session_started();
    $_SESSION['flash'][$key] = $message;
}

function get_flash(string $key): ?string
{
    ensure_session_started();

    if (!isset($_SESSION['flash'][$key])) {
        return null;
    }

    $message = $_SESSION['flash'][$key];
    unset($_SESSION['flash'][$key]);

    return $message;
}

function set_old_input(string $form, array $values): void
{
    ensure_session_started();
    $_SESSION['old_input'][$form] = $values;
}

function get_old_input(string $form): array
{
    ensure_session_started();
    $values = $_SESSION['old_input'][$form] ?? [];
    unset($_SESSION['old_input'][$form]);

    return is_array($values) ? $values : [];
}

function ensure_comments_user_tracking(mysqli $conexion): bool
{
    static $checked = null;

    if ($checked !== null) {
        return $checked;
    }

    $columnResult = $conexion->query("SHOW COLUMNS FROM comments LIKE 'user_id'");
    if ($columnResult && $columnResult->num_rows > 0) {
        $checked = true;
        return true;
    }

    $altered = $conexion->query('ALTER TABLE comments ADD COLUMN user_id INT NULL AFTER post_id');
    if (!$altered) {
        $checked = false;
        return false;
    }

    $conexion->query('ALTER TABLE comments ADD INDEX idx_comments_user_id (user_id)');
    $conexion->query('ALTER TABLE comments ADD CONSTRAINT comments_ibfk_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE');

    $checked = true;
    return true;
}
