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
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $idIncidencia = $_SESSION['idIncidencia'];
    $tipo = $_SESSION['tipo'];
    $reincidencia = $_SESSION['reincidencia'];
    $error = null;

    //Accion al pulsar el boton aceptar
    if(isset($_POST['aceptarMoverError'])){

        $comentario = trim($_POST['comentario']);
        if($comentario != ""){

            if($tipo == 'averia'){
                $fechaProxima = time() + 3600;
            }elseif($tipo == 'cambiodomicilio'){
                $fechaProxima = time() + 7200;
            }else{
                $fechaProxima = time() + 86400;
            }
            $reincidencia++;
            $fechaNueva = date("Y-m-d H:i:s",$fechaProxima);


            try {
                $datos = new Consulta();
                //Usamos uns transaccion para que en caso de error no ejecute ninguna sentencia.
                $datos->conexionDB->beginTransaction();

                $mensajeLlamada = 'Si';
                //Consulta para desasignar la incidencia al usuario
                $sentencia = "UPDATE usuario SET asignada = :asignada WHERE dni= :dni";
                $parametros = (array(":asignada"=>NULL, ":dni"=>$idUsuario));
                $datos->get_sinDatos($sentencia,$parametros);

                //Consulta para cambiar el estado de la incidencia a activa y añadir una fecha de proxima llamada 1 hora para averias 1 dia para el resto
                $sentencia = "UPDATE incidencia SET estado = :estado, tecnico = :tecnico, reincidencia = :reincidencia, disponible = :proxima, fecha_inicio = :fechInicio WHERE id_incidencia= :incidencia";
                $parametros = (array(":estado"=>'1',":tecnico"=>NULL,":reincidencia"=>$reincidencia, ":proxima"=>$fechaNueva, ":incidencia"=>$idIncidencia,":fechInicio"=> NULL));
                $datos->get_sinDatos($sentencia,$parametros);

                //Consulta para añadir el comentario
                $sentencia = "INSERT INTO comentarios(id_incidencia,tecnico,texto) VALUES (:incidencia,:tecnico,:comentario)";
                $parametros =(array(":incidencia"=>$idIncidencia,":tecnico"=>$idUsuario,":comentario"=>$comentario));
                $datos->get_sinDatos($sentencia,$parametros);


                $datos->conexionDB->commit();
                header("Location: ../tecnico/tecnico.php");
            } catch (PDOException $e) {
                $datos->conexionDB->rollBack();
                die('Error: ' . $e->getLine());
            } finally {
                $datos->conexionDB = null;
            }

        }else{
            $error = "error";
        }
    }

    //Accion al pulsar el boton cancelar
    if(isset($_POST['cancelarMoverError'])){
        header("Location: tecnico.php");
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('tecnico/tecnico_confirmar_no_contacto.twig', compact(
            'error',
            'rol'

        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}
