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
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $fechaActual = date("Y-m-d H:i:s");

    if(isset($_SESSION['origen'])){
        $origen = $_SESSION['origen'];
    }


    if(isset($_GET['Id'])){
        $_SESSION['idIncidencia'] = "";
        $idIncidencia =  $_GET['Id'];
    }else{
        $idIncidencia = $_SESSION['idIncidencia'];
    }

    //Con este parametro enviado por get indicamos que es la incidencia original cuando navegemos por la incidencias de un cliente par volver a la incidencia de partida.
    if(isset($_GET['o']) AND $_GET['o'] == '0'){
        $_SESSION['ultimaIncidenciaUsuario'] = $idIncidencia;
    }

    if(isset($_SESSION['ultimaIncidenciaUsuario'])){
        $ultimaIncidencia = $_SESSION['ultimaIncidenciaUsuario'];
    }

    try {
        $datos = new Consulta();
        //Usamos uns transaccion para que en caso de error no ejecute ninguna sentencia.
        $datos->conexionDB->beginTransaction();

        //Consulta para obtener los datos de la incidencia
        $consulta = "SELECT * FROM incidencia WHERE incidencia.id_incidencia = :incidencia";
        $parametros = array(":incidencia"=>$idIncidencia);
        $incidencia = $datos->get_conDatosUnica($consulta,$parametros);
        $idTecnico = $incidencia['tecnico'];
        $idComercial = $incidencia['id_usuario'];
        $idCliente = $incidencia['id_cliente'];

        //Consulta para obtener los datos del comercial de la incidencia
        $consulta = "SELECT dni,usuario,nombre,rol,telefono FROM usuario WHERE dni = :tecnico";
        $parametros = array(":tecnico"=>$idComercial);
        $comercial= $datos->get_conDatosUnica($consulta,$parametros);

        //Consulta para obtener los datos del cliente de la incidencia
        $consulta = "SELECT dni,nombre,direccion,telefono,ciudad,cp,provincia FROM cliente WHERE dni = :cliente";
        $parametros = array(":cliente"=>$idCliente);
        $cliente= $datos->get_conDatosUnica($consulta,$parametros);

        //Consulta para obtener los datos del tecnico de la incidencia
        $consulta = "SELECT dni,usuario,nombre,rol,telefono FROM usuario WHERE dni = :comercial";
        $parametros = array(":comercial"=>$idTecnico);
        $tecnico = $datos->get_conDatosUnica($consulta,$parametros);
        $datos->conexionDB->commit();

        //Obtener los comentarios de la incidencia ****
        $sentencia = "SELECT comentarios.texto FROM comentarios WHERE id_incidencia = :incidencia ORDER BY comentarios.fecha ASC";
        $parametros = (array(":incidencia"=>$idIncidencia));
        $datos = new Consulta();
        $arrayComentarios = $datos->get_conDatos($sentencia,$parametros);

    } catch (PDOException $e) {
        $datos->conexionDB->rollBack();
        die('Error: ' . $e->getMessage());
    } finally {
        $datos->conexionDB = null;
    }

    $interval = restarfechas($incidencia['fecha_creacion'],$incidencia['fecha_resolucion']);
    $tiempoResolucion = round($interval / 3600,2) . " Horas o (". round($interval / 86400,2) . ") Dias"  ; //dividimos entre 3600 para obtener las horas.

    $interval = restarfechas($incidencia['fecha_inicio'],$incidencia['fecha_resolucion']);
    $tiempoResoluciondos = round($interval / 3600,2) . " Horas o (". round($interval / 86400,2) . ") Dias"  ; //dividimos entre 3600 para obtener las horas.


    if($incidencia['estado'] == '3'){
        //Consulta para obtener la solucion si la incidencia esta finalizada

        $listaResultado = [];

        $consulta = "SELECT solucion.fecha ,solucion.solucion, usuario.nombre as tecnico FROM solucion INNER JOIN usuario ON solucion.tecnico = usuario.dni  WHERE id_incidencia = :idIncidencia";
        $parametros = array(":idIncidencia"=>$idIncidencia);
        $datos = new Consulta();
        $resultado = $datos->get_conDatosUnica($consulta,$parametros);

        if($resultado){
            $listaSolucion = json_decode($resultado['solucion']);
            $fechaSolucion = $resultado['fecha'];
            $tecnicoSolucion = $resultado['tecnico'];
        }
    }

    //ver historial de incidencias de cliente

    $consulta = "SELECT incidencia.id_incidencia, incidencia.fecha_creacion,incidencia.otros as info, solucion.solucion FROM incidencia INNER JOIN solucion ON incidencia.id_incidencia = solucion.id_incidencia WHERE id_cliente = :cliente ORDER BY incidencia.fecha_creacion";
    $parametros = array(":cliente"=>$idCliente);
    $datos = new Consulta();
    $historialIncidencias = $datos->get_conDatos($consulta,$parametros);

    if($incidencia){
        $mensajeIncidencia = 'Ok';
    }else{
        $mensajeIncidencia = 'error';
    }

    if($comercial){
        $mensajeComercial = 'Ok';
    }else{
        $mensajeComercial = 'error';
    }

    if($tecnico){
        $mensajeTecnico = 'Ok';
    }else{
        $mensajeTecnico = 'error';
    }

    if($cliente){
        $mensajeCliente = 'Ok';
    }else{
        $mensajeCliente = 'error';
    }
    if(isset($_POST['btnVolver'])){

        header('Location:'.$origen);

    }



    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_info.twig', compact(
            'mensaje',
            'comentarios',
            'rol',
            'usuario',
            'incidencia',
            'tecnico',
            'comercial',
            'cliente',
            'fechaActual',
            'tiempoResolucion',
            'tiempoResoluciondos',
            'listaSolucion',
            'fechaSolucion',
            'tecnicoSolucion',
            'historialIncidencias',
            'ultimaIncidencia',
            'arrayComentarios'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}