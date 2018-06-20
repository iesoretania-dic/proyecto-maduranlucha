<?php
require_once '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: index.php');
}elseif(($_SESSION['rol'] != '2') and ($_SESSION['rol'] != '0')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: index.php');
}else{
    comprobarSesion();
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $arrayFilas = [];
    $mensaje = null;
    $fechaActual = date("Y-m-d H:i:s");
    $idIncidencia = $_SESSION['idIncidencia'];

    if(isset($_POST['aceptarParcial'])){

        try{
            //Consulta para actualizar la incidencia a estado finalizada parcial
            $sentencia = "UPDATE incidencia SET estado = :estado, fecha_parcial = :fparcial,parcial= :parcial WHERE id_incidencia= :incidencia";
            $parametros = (array(":estado"=>'4',":incidencia"=>$idIncidencia,":fparcial"=>date("Y-m-d H:i:s"),":parcial"=>'Si'));
            $datos = new Consulta();
            $datos->get_sinDatos($sentencia,$parametros);
            header("Location: ../tecnico/tecnico.php");
        }catch (Exception $e){
            die('Error: ' . $e->GetMessage());
        }
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('tecnico/tecnico_finalizar_parcial.twig', compact(
            'mensaje',
            'incidencia',
            'rol',
            'usuario'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}