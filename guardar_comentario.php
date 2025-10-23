<?php
session_start();
include('conexion.php');

if (isset($_POST['user_name']) && isset($_POST['message']) && isset($_POST['post_id'])) {
    $user_name = mysqli_real_escape_string($conexion, $_POST['user_name']);
    $message = mysqli_real_escape_string($conexion, $_POST['message']);
    $post_id = intval($_POST['post_id']);

    $query = "INSERT INTO comments (user_name, message, post_id) VALUES ('$user_name', '$message', $post_id)";
    $res = mysqli_query($conexion, $query);

    if ($res) {
        echo json_encode([
            "success" => true,
            "user_name" => $user_name,
            "message" => $message
        ]);
    } else {
        echo json_encode(["success" => false, "error" => mysqli_error($conexion)]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Datos incompletos"]);
}


?>
