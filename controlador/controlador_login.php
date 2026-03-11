<?php
session_start();
include("database/conexion.php");
if (isset($_POST["btningresar"])) {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conexion->prepare("SELECT id, name, rol, password FROM users WHERE usuario = ? LIMIT 1");
    if (!$stmt) {
        echo "Error!: {$conexion->error}";
    } else {
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = mysqli_fetch_assoc($result);

            if (password_verify($password, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_rol'] = $row['rol'];
                header("location: index.php");
                exit;
            }

            echo "<div class='alerta-error'> Contraseña incorrecta <div>";

        } else {
echo "<div class='alerta-error'> Contraseña incorrecta <div>";            
        } 
        $stmt->close();
    }
}

?>
