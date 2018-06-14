<?php
require_once '../../php/Consulta.php';
session_start();

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
    $idIncidencia = $_SESSION['idIncidencia'];

    if(isset($_SESSION['disponible'])){
        $disponible = $_SESSION['disponible'];
    }

    $error = null;
    //Accion al pulsar el boton aceptar
    if(isset($_POST['aceptarMoverPendiente'])){
        $comentario = trim($_POST['comentario']);
        $pllamada = $_POST['pllamada'];

        if($pllamada == null){
            $pllamada =  date("Y-m-d H:i:s");
        }

        if($comentario != ""){
            try {
                //Usamos una transacción para que en caso de error no ejecute ninguna sentencia.
                $datos->conexionDB->beginTransaction();

                //Consulta para desasignar la incidencia al usuario
                $sentencia = "UPDATE usuario SET asignada = :asignada WHERE dni= :dni";
                $parametros = (array(":asignada"=> NULL,":dni"=>$idUsuario));
                $datos->get_sinDatos($sentencia,$parametros);

                //Consulta para cambiar el estado de la incidencia a asignada
                $sentencia = "UPDATE incidencia SET tecnico = :tecnico, estado = :estado, disponible =:pllamada WHERE id_incidencia= :incidencia";
                $parametros =(array(":tecnico"=> $idUsuario,":estado"=>'2', ":incidencia"=>$idIncidencia,":pllamada"=>$pllamada));
                $datos->get_sinDatos($sentencia,$parametros);

                //Consulta para añadir el comentario
                $sentencia = "INSERT INTO comentarios(id_incidencia,tecnico,texto) VALUES (:incidencia,:tecnico,:comentario)";
                $parametros =(array(":incidencia"=>$idIncidencia,":tecnico"=>$idUsuario,":comentario"=>$comentario));
                $datos->get_sinDatos($sentencia,$parametros);

                $datos->conexionDB->commit();
                header("Location: tecnico.php");
            } catch (PDOException $e) {
                $datos->conexionDB->rollBack();
                die('Error: ' . $e->getMessage());
            } finally {
                $datos->conexionDB = null;
            }

        }else{
            $error = "error";
        }
    }

    //Accion al pulsar el boton cancelar
    if(isset($_POST['cancelarMoverPendiente'])){
        header("Location: tecnico.php");
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('tecnico/tecnico_confirmar_pendientes.twig', compact(
            'error',
            'rol',
            'disponible'

        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}

