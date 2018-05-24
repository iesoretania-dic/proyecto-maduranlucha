<?php
require_once '../../php/Consulta.php';

session_start();
//var_dump($_POST);
//var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: index.php');
}elseif(($_SESSION['rol'] != '2') and ($_SESSION['rol'] != '0')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: index.php');
}else{
    try{
        $rol = $_SESSION['rol'];
        $usuario  = $_SESSION['usuario'];
        $datos = new Consulta();
        $idUsuario = $datos->get_id();
        //Consulta para saber si ya tiene un tarea asignada
        $sentencia = "SELECT nombre,dni,asignada,antenas,routers FROM usuario WHERE dni= :asignada";
        $parametros = (array(":asignada"=>$idUsuario));
        $datos = new Consulta();
        $datosUsuario = $datos->get_conDatosUnica($sentencia,$parametros);
        $asignada = $datosUsuario['asignada'];
        $cliente = null;

        //comprobar si hay averias sin asignar
        $sentencia = "SELECT COUNT(*) as averias FROM incidencia WHERE tipo = :tipo and estado = :estado AND disponible <= now()";
        $parametros = (array(":tipo"=>'averia',":estado"=>'1'));
        $datos = new Consulta();
        $averias= $datos->get_conDatosUnica($sentencia,$parametros);

        if($averias['averias'] > '0'){
            $mensajeAverias = 'Si';
        }else{
            $mensajeAverias = 'No';
        }

        //En el caso de que si esta asignada devolvera el id de la incidencia
        if($asignada){
            $mensaje = 'Si';
            $sentencia = "SELECT id_cliente,otros,tipo,reincidencia,llamada_obligatoria,parcial,urgente,disponible FROM incidencia WHERE id_incidencia =:idIncidencia ";
            $parametros = (array(":idIncidencia"=>$asignada));
            $datos = new Consulta();
            $resultado = $datos->get_conDatosUnica($sentencia,$parametros);
            $reincidencia = $resultado['reincidencia'];
            $id_cliente = $resultado['id_cliente'];
            $otros = $resultado['otros'];
            $tipo = $resultado['tipo'];
            $llamada = $resultado['llamada_obligatoria'];
            $parcial = $resultado['parcial'];
        }else{

            try {
                $datos = new Consulta();
                //Usamos uns transaccion para que en caso de error no ejecute ninguna sentencia.
                $datos->conexionDB->beginTransaction();

                //Consulta si no tiene una incidencia asignada
                $mensaje = 'No';
                $sentencia = "SELECT id_incidencia, id_cliente,fecha_creacion,otros,tipo,reincidencia,llamada_obligatoria,parcial,urgente,disponible FROM incidencia WHERE (disponible < NOW() OR disponible IS NULL) and fecha_resolucion IS NULL AND estado = :estado AND tecnico IS NULL ORDER BY urgente = :urgente DESC, tipo= :tipouno or tipo = :tipodos DESC, fecha_creacion LIMIT 1";
                $parametros = (array(":estado"=>'1', ":tipouno"=>'averia',"tipodos"=>'cambiodomicilio',":urgente"=>'Si'));
                $resultado = $datos->get_conDatosUnica($sentencia,$parametros);

                $asignada = $resultado['id_incidencia'];
                $reincidencia = $resultado['reincidencia'];
                $id_cliente = $resultado['id_cliente'];
                $otros = $resultado['otros'];
                $tipo = $resultado['tipo'];
                $llamada = $resultado['llamada_obligatoria'];
                $parcial = $resultado['parcial'];

                //Actualizar la fecha de inicio
                $sentencia = "UPDATE incidencia SET fecha_inicio = :inicio WHERE id_incidencia = :incidencia";
                $parametros = (array(":inicio"=>date("Y-m-d H:i:s"), "incidencia"=>$asignada));
                $datos->get_sinDatos($sentencia,$parametros);
                $datos->conexionDB->commit();

            } catch (PDOException $e) {
                $datos->conexionDB->rollBack();
                die('Error: ' . $e->getMessage());
            } finally {
                $datos->conexionDB = null;
            }

        }
        if(isset($asignada)){
            $sentencia = "SELECT * FROM cliente WHERE dni = :dni";
            $parametros = (array(":dni"=>$id_cliente));
            $datos = new Consulta();
            $cliente= $datos->get_conDatosUnica($sentencia,$parametros);
        }

    }catch (Exception $e){
        die('Error: ' . $e->GetMessage());
    }

    //Accion si pulsa el boton Aceptar
    if(isset($_POST['btnAceptarIncidencia'])){

        try {
            $datos = new Consulta();
            //Usamos uns transaccion para que en caso de error no ejecute ninguna sentencia.
            $datos->conexionDB->beginTransaction();

            //Consulta asignar la incidencia al usuario
            $sentencia = "UPDATE usuario SET asignada = :asignada WHERE dni= :dni";
            $parametros = (array(":asignada"=>$asignada, ":dni"=>$idUsuario));
            $datos->get_sinDatos($sentencia,$parametros);

            //Consulta para actualizar la incidencia a estado asignada
            $sentencia = "UPDATE incidencia SET estado = :estado, tecnico = :usuario WHERE id_incidencia= :incidencia";
            $parametros = (array(":estado"=>'2',":usuario"=>$idUsuario,":incidencia"=>$asignada));
            $datos->get_sinDatos($sentencia,$parametros);

            $datos->conexionDB->commit();
            header("Location: ../tecnico/tecnico.php");
        } catch (PDOException $e) {
            $datos->conexionDB->rollBack();
            die('Error: ' . $e->getMessage());
        } finally {
            $datos->conexionDB = null;
        }
    }

    //ACCION DE LOS BOTONES

    //Accion si se pulsa el boton de confirmar llamada
    if(isset($_POST['confirmarLlamada'])){
        try{
            $sentencia = "UPDATE incidencia SET llamada_obligatoria = :llamada WHERE id_incidencia= :incidencia";
            $parametros = (array(":llamada"=>'Si', ":incidencia"=>$asignada));
            $datos = new Consulta();
            $datos->get_sinDatos($sentencia,$parametros);
        }catch (Exception $e){
            die('Error: ' . $e->GetMessage());
        }finally{
            $bbdd = null;
            header("Location: ../tecnico/tecnico.php");
        }
    }

    $mensajeLlamada = null;
    //Accion si pulsa el boton Finalizar
    if(isset($_POST['btnFinalizarIncidencia'])){
        if($llamada == 'Si'){
            $_SESSION['asignada'] = $asignada;
            $_SESSION['antenas'] = $datosUsuario['antenas'];
            $_SESSION['routers'] = $datosUsuario['routers'];
            $_SESSION['tipo'] = $tipo;
            //Datos del cliente
            $_SESSION['dniCliente'] = $cliente['dni'];
            $_SESSION['antenasCliente'] = $cliente['antenas'];
            $_SESSION['routersCliente'] = $cliente['routers'];
            $_SESSION['retiradaCompleta'] =$cliente['retiradacompleta'];

            header("Location: tecnico_finalizar.php");
        }else{
            $mensajeLlamada = 'No';
        }
    }

    //Accion si pulsa el boton finalizar parcial
    if(isset($_POST['btnFinalizarParcialIncidencia'])){

        if($llamada == 'Si'){
            if($parcial == 'No'){
                $_SESSION['idIncidencia'] = $asignada;
                header("Location: tecnico_finalizar_parcial.php");
            }else{
                $mensajeParcial = 'No';
            }

        }else{
            $mensajeLlamada = 'No';
        }
    }

    //Accion si pulsa el boton resolver mas tarde
    if(isset($_POST['btnPendiente'])){
        if($llamada == "Si") {
            $_SESSION['idIncidencia'] = $asignada;
            $_SESSION['disponible'] = $resultado['disponible'];
            header("Location: tecnico_confirmar_pendiente.php");
        }else{
            $mensajeLlamada = 'No';
        }
    }

    //Accion si pulsa el boton no se pudo contactar con el cliente
    if(isset($_POST['btnErrorIncidencia'])){
        if($llamada == "Si"){
            $_SESSION['idIncidencia'] = $asignada;
            $_SESSION['tipo'] = $tipo;
            $_SESSION['reincidencia'] = $reincidencia;
            header("Location: tecnico_confirmar_no_contacto.php");
        }else{
            $mensajeLlamada = 'No';
        }
    }



    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('tecnico/tecnico.twig', compact(
            'mensaje',
            'asignada',
            'cliente',
            'otros',
            'tipo',
            'llamada',
            'mensajeLlamada',
            'datosUsuario',
            'usuario',
            'rol',
            'mensajeParcial',
            'mensajeAverias',
            'resultado'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}

