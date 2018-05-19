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
    $idCliente = $_SESSION['dniCliente']; //Si pulsamos nueva incidencia desde el panel de incidencias del cliente guardamos el id en una sesion
    $tipoI = $_GET['tipo'];
    if(isset($_GET['add'])){
        $idCliente = $_GET['add']; //como vamos con el acceso directo del cliente le pasamos el id por post en vez de por la sesion.
        $_SESSION['dniCliente'] = $_GET['add']; //Lo guardamos en una sesion para cuando vuelva a listar las incidencias tenga el id del cliente.
    }
    $mensaje = null;
    //Si pulsa añadir nos añade la incidencia
    if(isset($_POST['btnCrearIncidencia'])){

        $tipo = $_POST['tipo'];
        $otros = $_POST['otros'];
        $estado = 0;
        if($tipo != 'averia'){
            $estado = 1;
        }
        $consulta = "INSERT INTO incidencia(id_usuario,id_cliente,tipo,otros, estado) values (:usuario, :cliente, :tipo, :otros, :estado)";
        $parametros = array(":usuario"=>$idUsuario,":cliente"=>$idCliente,":tipo"=>$tipo,":otros"=>$otros,":estado"=>$estado);
        $datos = new Consulta();
        $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

        if($filasAfectadas > 0){
            $mensaje = 'ok';
            if($tipoI == 1){
                header('Location: ../cliente/cliente_listar.php');
            }elseif($tipoI == 0){
                header('Location: ../cliente/cliente_incidencias.php?dni='.$idCliente."&tipo=1");
            }
        }else{
            $mensaje = "error";
        }
    }


    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_add.twig', compact(
            'mensaje',
            'usuario',
            'rol'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}