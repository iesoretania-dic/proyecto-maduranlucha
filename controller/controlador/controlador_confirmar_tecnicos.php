<?php
require '../../php/Consulta.php';

session_start();
var_dump($_POST);
var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif(($_SESSION['rol'] != '4') and ($_SESSION['rol'] != '0')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../../index.php');
}else{
    $usuario  = $_SESSION['usuario'];
    $rol = $_SESSION['rol'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $idIncidencia = $_GET['Id'];
    $tecnicos = null;

    if(isset($_SESSION['tipo'])){
        $tipo = $_SESSION['tipo'];
    }

    if(isset($_SESSION['dni'])){
        $dni= $_SESSION['dni'];
    }

    //Consulta para obtener los tecnicos
    $sentencia = "SELECT dni, usuario, nombre FROM usuario WHERE rol = :rol";
    $parametros = (array(":rol"=>'2'));
    $datos = new Consulta();
    $tecnicos = $datos->get_conDatos($sentencia,$parametros);
    //Consulta para obtener la proxima llamada de la incidencia
    $sentencia = "SELECT disponible FROM incidencia WHERE id_incidencia = :idIncidencia";
    $parametros = (array(":idIncidencia"=>$idIncidencia));
    $datos = new Consulta();
    $datosIncidencia = $datos->get_conDatosUnica($sentencia,$parametros);
    $proLlamada = $datosIncidencia['disponible'];

    if($tecnicos > 0){
        $mensajeTecnicos = 'ok';
    }else{
        $mensajeTecnicos = 'error';
    }

    //Accion al pulsar el boton aceptar
    if(isset($_POST['aceptarMoverTecnicos'])){
        $comentario = trim($_POST['comentario']);
        $cita = $_POST['cita'];
        if($rol == '0'){ //Si viene por el administrador añadimos al tecnico
            $tecnico = $_POST['tecnico'];
        }elseif($rol == '4'){ //Si viene por el controlador le indicamos que no hay tecnico
            $tecnico = "";
        }
        $pllamada = $_POST['llamada'];

        if(!$pllamada){
            $pllamada = date("Y-m-d H:i:s");
        }


        if(isset($tecnico) AND $tecnico == "" ){
            //Si el tecnico viene vacio ponemos la incidencia a estado activa
            $sentencia = "UPDATE incidencia SET estado = :estado, disponible = :pllamada WHERE id_incidencia= :incidencia";
            $parametros = (array(":estado"=>'1',":pllamada"=>$pllamada, ":incidencia"=>$idIncidencia));
            $datos = new Consulta();
            $datos->get_sinDatos($sentencia,$parametros);
        }else{
            //Si se selecciona un tecnico actualizamos la incidencia a estado asignada y le añadimos el id del tecnico
            $sentencia = "UPDATE incidencia SET estado = :estado,disponible = :pllamada, tecnico = :tecnico WHERE id_incidencia= :incidencia";
            $parametros = (array(":estado"=>'2',":pllamada"=>$pllamada,"tecnico"=>$tecnico, ":incidencia"=>$idIncidencia));
            $datos = new Consulta();
            $datos->get_sinDatos($sentencia,$parametros);
        }


        //CONSULTA PARA EL COMENTARIO
        $sentencia = "INSERT INTO comentarios(id_incidencia,tecnico,texto,cita) VALUES (:incidencia,:tecnico,:comentario,:cita)";
        $parametros= (array(":incidencia"=>$idIncidencia,":tecnico"=>$idUsuario,":comentario"=>$comentario,":cita"=>$pllamada));
        $datos = new Consulta();
        $datos->get_sinDatos($sentencia,$parametros);

        if($rol == '4'){
            //Para el comercial le redirigimos a la pagina de controladores.
            header("Location: ../cliente/cliente_incidencias.php");

        }elseif($rol == '0'){
            if(isset($_SESSION['dni']) and $_SESSION['dni'] != ""){
                header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo."&dni=".$dni);
            }else{
                header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo);
            }
        }
    }


    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('controlador/controlador_confirmar_tecnicos.twig', compact(
            'mensaje',
            'error',
            'usuario',
            'rol',
            'tecnicos',
            'mensajeTecnicos',
            'proLlamada'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}

