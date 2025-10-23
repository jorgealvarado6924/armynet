<?php
session_start();
include "conexion.php";
if(!isset($_SESSION ['user_id'])){
    header("Location: login.php");
} else {
    if($_SESSION['user_rol' ] == "admin"){
        echo "Eres admin";
    }else{
        header("Location: blog.php");
    }
}
?>