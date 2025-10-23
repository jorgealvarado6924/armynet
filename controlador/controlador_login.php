<?php
session_start();
include("conexion.php");
if (isset($_POST["btningresar"])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    $sql = "select * from users where usuario='$usuario' ";
    $result = mysqli_query($conexion, $sql);
    if (!$result) {
        echo "Error!: {$conexion->error}";
    } else {
        if ($result-> num_rows > 0) {
            $row = mysqli_fetch_assoc($result);

            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['user_rol'] = $row['rol'];
            header("location: index.php");
            // guardar contraseñas encriptadas 
            $password_hash = password_hash($password, PASSWORD_BCRYPT);

        } else {
echo "<div class='alerta-error'> Contraseña incorrecta <div>";            
        } 
    }
}

?>