<?php
require '../../php/Consulta.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0'){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../../index.php');
}else{

    $usuario  = $_SESSION['usuario'];
    $rol = $_SESSION['rol'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $uri =  $_SERVER['REQUEST_URI'];

    $consulta = "SELECT conexiones.fecha, conexiones.usuario,conexiones.tipo, usuario.nombre FROM conexiones INNER JOIN usuario ON conexiones.usuario = usuario.dni ORDER BY conexiones.fecha DESC";
    $parametros = array();
    $datos = new Consulta();
    $conexiones = $datos->get_conDatos($consulta,$parametros);

    if($conexiones){
        $mensaje = 'ok';
    }else{
        $mensaje = 'error';
    }

    $vista = 'conexiones';

    if(isset($_POST['limpiar'])){
        $vista = 'limpiar';

    }

    if(isset($_POST['aceptarLimpiar'])){

        $consulta ="DELETE FROM conexiones";
        $parametros = array();
        $datos = new Consulta();
        $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);
        header("Location: administrador_conexiones.php");
    }


    if(isset($_POST['cancelarLimpiar'])){
        $vista = 'conexiones';

    }



    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('administrador/administrador_conexiones.twig', compact(
            'usuario',
            'tecnicos',
            'mensaje',
            'rol',
            'conexiones',
            'vista',
            'uri'

        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}