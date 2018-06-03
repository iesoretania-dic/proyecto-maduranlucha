<?php

require '../../php/Consulta.php';

$datos = new Conexion();

if ($datos->conexionDB == null){
    echo "no conectado";
    header('Location: conexion.php');
}else{
    echo "conectado";
    header('Location: login.php');
}

