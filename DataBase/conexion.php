<?php
    $server="localhost";
    $username="root";
    $password="";
    $database="dbarmynet";
    $port="3307";
    $conexion = new mysqli($server, $username, $password, $database, $port);
    $conexion->set_charset("utf8");

    if (!$conexion){
        echo "<script>alert('conexion Error');</script";
    }
?>