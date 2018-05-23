<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';

session_start();
//var_dump($_POST);
//var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0' and ($_SESSION['rol'] != '1')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{

    $mensaje = null;
    $nombreTecnico = null;
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $idIncidencia = $_GET['Id'];


    $sentencia = "SELECT solucion FROM solucion WHERE id_incidencia =:idIncidencia ";
    $parametros = (array(":idIncidencia"=>$idIncidencia));
    $datos = new Consulta();
    $resultado = $datos->get_conDatosUnica($sentencia,$parametros);

    $listaSolucion = json_decode($resultado['solucion']);


    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_solucion.twig', compact(
            'mensaje',
            'comentarios',
            'rol',
            'usuario',
            'tecnicos',
            'listaSolucion',
            'idIncidencia'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}