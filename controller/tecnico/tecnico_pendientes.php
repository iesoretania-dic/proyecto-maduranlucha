<?php
require_once '../../php/Consulta.php';
session_start();
var_dump($_POST);
var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: index.php');
}elseif(($_SESSION['rol'] != '2') and ($_SESSION['rol'] != '0')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: index.php');
}else{
    $rol = $_SESSION['rol'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $arrayFilas = [];
    $mensaje = null;
    try{
        //Consulta para obtener las incidencias pendientes del tecnico
        $sentencia = "SELECT incidencia.id_incidencia,incidencia.id_usuario,incidencia.id_cliente,incidencia.fecha_creacion,incidencia.fecha_resolucion,incidencia.disponible,incidencia.otros, cliente.nombre, cliente.telefono, cliente.direccion, usuario.asignada FROM incidencia INNER JOIN cliente ON incidencia.id_cliente = cliente.dni INNER JOIN usuario ON incidencia.id_usuario = usuario.dni WHERE incidencia.tecnico= :tecnico AND incidencia.fecha_resolucion IS NULL";
        $parametros = (array(":tecnico"=>$idUsuario));
        $datos = new Consulta();
        $arrayFilas = $datos->get_conDatos($sentencia,$parametros);
        if(!isset($arrayFilas[0])){
            $mensaje = "No";
        }else{
            $mensaje = "Si";
        }
    }catch (Exception $e){
        die('Error: ' . $e->GetMessage());
    }


    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('tecnico/tecnico_pendientes.twig', compact(
            'mensaje',
            'incidencia',
            'arrayFilas',
            'asignada',
            'actual',
            'rol'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}

