<?php
require '../../php/Consulta.php';
session_start();
//var_dump($_POST);
//var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0' and ($_SESSION['rol'] != '1')){ //posibilidad de quitar al comercial de aqui
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{

    $mensaje = null;
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $idIncidencia =  $_GET['Id'];

    if(isset($_SESSION['tipo'])){
        $tipo = $_SESSION['tipo'];
    }

    if(isset($_SESSION['Id'])){
        $id= $_SESSION['Id'];
    }

    if(isset($_POST['confirmarEliminar'])){

        //Eliminar la incidencia
        $consulta ="DELETE FROM incidencia WHERE id_incidencia = :idIncidencia";
        $parametros = array(":idIncidencia"=> $idIncidencia);
        $datos = new Consulta();
        $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

        if($filasAfectadas > 0){
            $mensaje = 'ok';

            header("Location: ../cliente/cliente_incidencias.php?tipo=0");
        }else{
            $mensaje = 'error';
        }
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_eliminar.twig', compact(
            'mensaje',
            'usuario',
            'rol',
            'idIncidencia'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}