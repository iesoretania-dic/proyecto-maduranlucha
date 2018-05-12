<?php
require '../../php/Consulta.php';
session_start();
//var_dump($_POST);
//var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0' and ($_SESSION['rol'] != '1')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{

    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $datos = new Consulta();
    $idUsuario= $datos->get_id();

    if(isset($_GET['tipo'])){
        $tipo = $_GET['tipo'];
        $_SESSION['tipo'] = $_GET['tipo'];
    }
    if(isset($_GET['Id'])){
        $dniCliente = $_GET['Id'];
        $_SESSION['Id'] = $_GET['Id'];
    }else{
        $_SESSION['Id'] ='';
    }


    //Consulta si viene desde el apartado de incidencias
    if($tipo == '0'){
        //Consulta para obtener todas las incidencias
        $consulta = "SELECT incidencia.*, usuario.usuario FROM incidencia INNER JOIN usuario ON incidencia.id_usuario = usuario.dni ORDER BY tipo = :tipouno or tipo = :tipodos DESC, estado = :estado DESC, fecha_creacion";
        $parametros = array(":tipouno"=>'averia',"tipodos"=>'cambiodomicilio',":estado"=>'0');
        $datos = new Consulta();
        $arrayFilas = $datos->get_conDatos($consulta,$parametros);

        if($arrayFilas){
            $mensaje = 'Si';
        }else{
            $mensaje = 'No';
        }
    }

    //Accion si existe la variable de session dniIncidencias a causa de pulsar el boton incidencias de la pagina de los clientes.
    if($tipo == '1'){
        //CONSULTA PARA OBTENER Las incidencias de un cliente.
        $consulta = "SELECT incidencia.*, usuario.usuario, usuario.nombre FROM incidencia INNER JOIN usuario ON incidencia.id_usuario = usuario.dni WHERE incidencia.id_cliente = :dni ORDER BY fecha_creacion DESC";
        $parametros = array(":dni"=>$dniCliente);
        $datos = new Consulta();
        $arrayFilas = $datos->get_conDatos($consulta,$parametros);

        if($arrayFilas){
            $mensaje = 'Si';
        }else{
            $mensaje = 'No';
        }
    }

    if($tipo == '1'){
        //Accion si se pulsa el boton añadir incidencia
        if(isset($_POST['btnAdd'])){
            $_SESSION['dniCliente'] = $dniCliente;
            header('Location: cliente_incidencias_add.php?tipo=0');
        }
        //Accion si se pulsa el boton volver
        if(isset($_POST['btnVolver'])){
            header('Location: cliente_listar.php');
        }
    }

    //BOTONES DE ACCION//

    //Accion si pulsa el boton de de informacion
      if(isset($_POST['informacion'])){
          header('Location: ../cliente/cliente_incidencias_info.php');
    }

    //Accion si pulsa el boton de de comentario
    if(isset($_POST['comentarios'])){
        $_SESSION['idIncidencia'] = $_POST['comentarios'];
        header('Location: ../cliente/cliente_incidencias_comentario.php');
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias.twig', compact(
            'arrayFilas',
            'dniCliente',
            'mensaje',
            'rol',
            'usuario',
            'tipo'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}