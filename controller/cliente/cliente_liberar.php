<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0'){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{
    comprobarSesion();
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $dni = $_GET['Id']; // Aqui recibimos el dni del cliente o del comercial depende de donde se envie.
    $nombreCliente = $datos->get_nombreCliente($dni);
    $tipo = $_GET['tipo']; // 0 para usuario, 1 para cliente
    $mensajeUpdate = null;

    //Accion al pulsar el boton de liberar
    if(isset($_POST['btnLiberar'])){

        if ($tipo == '1'){

            $consulta = "UPDATE cliente SET id_usuario = NULL WHERE dni = :dni";
            $parametros = array(":dni"=>$dni);
            $datos = new Consulta();
            $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

            if($filasAfectadas > 0){
                $mensajeUpdate = "ok";
                header('Location: ../cliente/cliente_listar.php');
            }else{
                $mensajeUpdate = "error";
            }
        }

        if ($tipo == '0'){

            //Comprobamos si el comercial tiene clientes
            $consulta = "SELECT nombre FROM cliente WHERE id_usuario = :usuario";
            $parametros = array(":usuario"=>$dni);
            $datos = new Consulta();
            $filasAfectadas = $datos->get_conDatos($consulta,$parametros);

            if ($filasAfectadas){
                $consulta = "UPDATE cliente SET id_usuario = NULL WHERE id_usuario = :usuario";
                $parametros = array(":usuario"=>$dni);
                $datos = new Consulta();
                $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

                $mensajeUpdate = "ok";
                header('Location: ../usuario/usuario_listar.php?rol=1');

            }else{
                $mensajeUpdate = 'sinClientes';
            }
        }
    }

    //Accion en el caso de pulsar el boton de cancelar
    if(isset($_POST['btnCancelar'])){
        if($tipo == '0'){
            header('Location: ../usuario/usuario_listar.php?rol=1');
        }elseif($tipo == '1'){
            header('Location: ../cliente/cliente_listar.php');
        }
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_liberar.twig', compact(
            'usuario',
            'clientes',
            'mensajeUpdate',
            'cliente',
            'dni',
            'nombre',
            'comerciales',
            'rol',
            'filasAfectadas',
            'tipo',
            'nombreCliente'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}