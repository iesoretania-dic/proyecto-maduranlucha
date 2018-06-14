<?php
require '../../php/Consulta.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0' and ($_SESSION['rol'] != '1')  and ($_SESSION['rol'] != '2') and ($_SESSION['rol'] != '4') ){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{

    $mensaje = null;
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $idIncidencia =  $_GET['Id'];
    //Obtener los comentarios de la incidencia
    $consulta = "SELECT id_incidencia, (SELECT nombre FROM usuario WHERE dni = tecnico) AS tecnico, fecha,texto FROM comentarios WHERE id_incidencia = :incidencia ORDER BY fecha";
    $parametros = array(":incidencia"=> $idIncidencia);
    $datos = new Consulta();
    $comentarios = $datos->get_conDatos($consulta,$parametros);

    //Obtener la informacion del comercial dela incidencia
    $consulta = "SELECT otros FROM incidencia WHERE id_incidencia = :incidencia";
    $parametros = array(":incidencia"=> $idIncidencia);
    $datos = new Consulta();
    $incidencia = $datos->get_conDatosUnica($consulta,$parametros);

    if($comentarios){
        $mensaje = 'Ok';
    }else{
        $mensaje = 'error';
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_comentario.twig', compact(
            'mensaje',
            'usuario',
            'rol',
            'comentarios',
            'idIncidencia',
            'incidencia'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}