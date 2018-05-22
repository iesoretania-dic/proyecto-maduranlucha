<?php
require '../../php/Consulta.php';
session_start();
//var_dump($_POST);
//var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0'){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../../index.php');
}else{

    $usuario  = $_SESSION['usuario'];
    $rol = $_SESSION['rol'];
    $rolUsuario = $_SESSION['rolUsuario'];

    //Obtenemos la infomacion del usuario
    $dniUsuario = $_GET['Id'];
    $datos = new Consulta();
    $nombreUsuario = $datos->get_nombreUsuario($dniUsuario);
    $_SESSION['rolUsuario'] = $_GET['rolUsuario']; //Guardamos en una sesion el rol del usuario que estamos tratando.


    if(isset($_POST['btnEliminar'])){

        $consulta ="DELETE FROM usuario WHERE dni = :dni";
        $parametros = array(":dni"=> $dniUsuario);
        $datos = new Consulta();
        $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

        if($filasAfectadas > 0){
            $mensaje = 'ok';
            header('Location: ../usuario/usuario_listar.php?rol='.$rolUsuario);
        }else{
            $mensaje = "error";
        }

    }

    if(isset($_POST['cancelar'])){
        header('Location: ../usuario/usuario_listar.php?rol='.$rolUsuario);
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('usuario/usuario_eliminar.twig', compact(
            'usuario',
            'datosUsuario',
            'comerciales',
            'mensaje',
            'rol',
            'dni',
            'rolUsuario',
            'nombreUsuario'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}