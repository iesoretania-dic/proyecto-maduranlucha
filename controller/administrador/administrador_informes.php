<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();
//var_dump($_POST);
//var_dump($_SESSION);

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

    $mensaje = null;
    $vista = 'botones';
    $consulta = null;


    if(isset($_POST['bajaMaterial'])){

        $sentencia = "SELECT cliente.dni, cliente.nombre, cliente.antenas,cliente.telefono, cliente.routers, incidencia.tipo FROM cliente INNER JOIN incidencia ON cliente.dni = incidencia.id_cliente WHERE incidencia.tipo ='baja' and (cliente.routers  or cliente.antenas)";
        $parametros = array();
        $datos = new Consulta();
        $clientes = $datos->get_conDatos($sentencia,$parametros);

        if($clientes){
            $vista = 'consulta';
            $consulta = '1';
        }else{
            $mensaje = 'error';
        }
    }

    if(isset($_POST['btnVolver'])){
        header('Location: ../administrador/administrador_informes.php');
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('administrador/administrador_informes.twig', compact(
            'usuario',
            'tecnicos',
            'mensaje',
            'rol',
            'vista',
            'clientes',
            'incidencias',
            'mensaje',
            'consulta',
            'tiempo_resultado',
            'arraymeses',
            'incidenciasTecnico',
            'nombreTecnico',
            'mensajeTecnicoResueltas',
            'nombreCliente',
            'uri'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}