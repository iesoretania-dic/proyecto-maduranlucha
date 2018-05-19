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

    //Obtenemos la fecha de disponibilidad de la incidencia

    $consulta = "SELECT disponible FROM incidencia WHERE id_incidencia = :incidencia";
    $parametros = array(":incidencia"=>$idIncidencia);
    $datos = new Consulta();
    $misDatos = $datos->get_conDatosUnica($consulta,$parametros);

    $fechaDisponible = NULL;

    if($misDatos){
        $fechaDisponible = $misDatos['disponible'];
    }

    if(isset($_POST['btnModificar'])){

        $fecha = $_POST['disponible'];

        if($fecha == null){
            $fecha = date("Y-m-d H:i:s");
        }

        $consulta = "UPDATE incidencia SET disponible = :fecha WHERE id_incidencia = :incidencia";
        $parametros = array(":fecha"=>$fecha,":incidencia"=> $idIncidencia);
        $datos = new Consulta();
        $datosIncidencia = $datos->get_sinDatos($consulta,$parametros);

        if($datosIncidencia > 0){
            $mensaje = 'ok';
            if(isset($_SESSION['Id']) and $_SESSION['Id'] != ""){
                header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo."&dni=".$id);
            }else{
                header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo);
            }
        }else{
            $mensaje = 'error';
        }
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_disponible.twig', compact(
            'mensaje',
            'usuario',
            'rol',
            'fechaDisponible'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}