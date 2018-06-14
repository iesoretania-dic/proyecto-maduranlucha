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
    $usuario  = $_SESSION['usuario'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $uri =  $_SERVER['REQUEST_URI'];
    $arrayFilas = [];
    $mensaje = null;
    $fechaActual = date("Y-m-d H:i:s");
    try{
        //Consulta para obtener las incidencias pendientes del tecnico
        $sentencia = "SELECT incidencia.id_incidencia,incidencia.id_usuario,incidencia.id_cliente,incidencia.fecha_creacion,incidencia.fecha_resolucion,incidencia.disponible,incidencia.otros,incidencia.urgente, cliente.nombre, cliente.telefono,cliente.ciudad, cliente.direccion, usuario.asignada FROM incidencia INNER JOIN cliente ON incidencia.id_cliente = cliente.dni INNER JOIN usuario ON incidencia.id_usuario = usuario.dni WHERE incidencia.tecnico= :tecnico AND incidencia.fecha_resolucion IS NULL ORDER BY fecha_creacion";
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

    if(isset($_POST['incidencia_aceptar'])){
        try{
            //Consulta para obtener el valor de la incidencia asignada
            $sentencia = "SELECT asignada FROM usuario WHERE dni=:dni";
            $parametros = (array(":dni"=>$idUsuario));
            $datos = new Consulta();
            $resultado = $datos->get_conDatosUnica($sentencia,$parametros);
            $asignada = $resultado['asignada'];
            $actual = $_POST['incidencia_aceptar'];

            if(!$asignada){

                try {
                    //Usamos una transacción para que en caso de error no ejecute ninguna sentencia.
                    $datos->conexionDB->beginTransaction();

                    //Consulta para asignar la incidencia en caso de que no tenga ya una asignada
                    $sentencia = "UPDATE usuario SET asignada = :asignada WHERE dni= :dni";
                    $parametros = (array(":asignada"=>$actual, ":dni"=>$idUsuario));
                    $datos->get_sinDatos($sentencia,$parametros);

                    $consulta = "UPDATE incidencia SET incidencia.llamada_obligatoria = :llamada WHERE id_incidencia = :incidencia";
                    $parametros = array(":llamada"=>'No',":incidencia"=>$actual);
                    $datos->get_sinDatos($consulta,$parametros);
                    $datos->conexionDB->commit();

                    header("Location: tecnico.php");

                } catch (PDOException $e) {
                    $datos->conexionDB->rollBack();
                    die('Error: ' . $e->getMessage());
                } finally {
                    $datos->conexionDB = null;
                }


            }
        }catch (Exception $e){
            die('Error: ' . $e->GetMessage());
        }finally{
            $bbdd = null;
        }
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
            'rol',
            'fechaActual',
            'usuario',
            'uri'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}