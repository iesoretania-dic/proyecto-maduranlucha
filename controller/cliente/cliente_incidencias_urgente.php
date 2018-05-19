<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';

session_start();
//var_dump($_POST);
//var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0' and ($_SESSION['rol'] != '4')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{

    $mensaje = null;
    $nombreTecnico = null;
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $idIncidencia = $_GET['Id'];

    if(isset($_SESSION['tipo'])){
        $tipo = $_SESSION['tipo'];
    }

    if(isset($_SESSION['dni'])){
        $dni= $_SESSION['dni'];
    }

    //Obtenemos el campo de urgente
    $sentencia = "SELECT urgente from incidencia WHERE id_incidencia = :incidencia";
    $parametros = array(":incidencia"=>$idIncidencia);
    $datos = new Consulta();
    $resultado =  $datos->get_conDatosUnica($sentencia,$parametros);

    $urgenteI = $resultado['urgente'];

    if($urgenteI == 'No'){
        $mensajeUrgente = 'No';
        $urgenteI = 'Si';
    }elseif($urgenteI == 'Si') {
        $mensajeUrgente = 'Si';
        $urgenteI = 'No';
    }
    
    if(isset($_POST['btnUrgente'])){
        //Actualizamos el estado de urgente
        $consulta = "UPDATE incidencia SET urgente = :urgente WHERE id_incidencia = :incidencia";
        $parametros = array(":urgente"=>$urgenteI,":incidencia"=>$idIncidencia);
        $datos = new Consulta();
        $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

        if(isset($_SESSION['dni']) and $_SESSION['dni'] != ""){
            header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo."&dni=".$dni);
        }else{
            header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo);
        }
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_urgente.twig', compact(
            'mensaje',
            'comentarios',
            'rol',
            'usuario',
            'tecnicos',
            'nombreTecnico',
            'mensajeUrgente'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}