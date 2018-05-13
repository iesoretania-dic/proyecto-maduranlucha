<?php
//Iniciamos la sesion
session_start();
var_dump($_SESSION);

require '../../php/Consulta.php';
require '../../php/funciones.php';

if(isset($_POST['enviar'])){
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $datos = new Consulta();
    $passwordCifrado = $datos->get_clave($usuario);

    if(password_verify($password,$passwordCifrado)){
        header('Location: ../../index.php');
    }else{
        $consulta = new Consulta();
        $_SESSION['usuario'] = $usuario;
        $_SESSION['rol'] = $consulta->get_rol();
        $consulta->conexion();
        if(isset($_SESSION['rol'])){
            switch ($_SESSION['rol']) {
                case 0: header('Location: ../cliente/cliente_incidencias.php?tipo=0');
                    break;
                case 1: header('Location: ../cliente/cliente_listar.php');
                    break;
                case 2: header('');
                    echo "no disponible";
                    break;
                case 3: header('');
                    echo "no disponible";
                    break;
                case 4: header('Location: ../cliente/cliente_incidencias.php');
                    break;
                default: echo "Error";
            }
        }
    }
}






