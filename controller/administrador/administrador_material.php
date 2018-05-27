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

    $mensajeEstablecer = null;
    $usuario  = $_SESSION['usuario'];
    $rol = $_SESSION['rol'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $uri =  $_SERVER['REQUEST_URI'];

    $consulta = "SELECT stock.fecha, stock.antenas, stock.routers,stock.atas,stock.ultimousuario,stock.antenasM,stock.routersM,stock.atasM, usuario.dni, usuario.usuario FROM stock INNER JOIN usuario ON stock.ultimousuario  = usuario.dni ORDER BY stock.fecha DESC ";
    $parametros = array();
    $datos = new Consulta();
    $materiales = $datos->get_conDatos($consulta,$parametros);

    if($materiales){
        $mensaje = 'ok';
    }else{
        $mensaje = 'error';
    }

    if(isset($_POST['establecer'])){
        $mensajeEstablecer = 'ok';
    }

    if(isset($_POST['aceptarEstablecer'])){

        $antenas = $_POST['antenas'];
        $routers = $_POST['routers'];
        $atas = $_POST['atas'];

        $consulta = "INSERT INTO stock (antenas,routers,atas,ultimousuario,antenasM,routersM,atasM) VALUES (:antenas, :routers, :atas, :ultimo, :antenasM, :routersM, :atasM)";
        $parametros = array(":antenas"=>$antenas,":routers"=>$routers,":atas"=>$atas,":ultimo"=>$idUsuario,":antenasM"=>$antenas,":routersM"=>$routers,":atasM"=>$atas);
        $datos = new Consulta();
        $resultado = $datos->get_sinDatos($consulta,$parametros);

        if($resultado > 0){
            $mensajeResultadoEstablecer = 'ok';
            header("location: administrador_material.php");
        }else{
            $mensajeResultadoEstablecer = 'error';
        }
    }



    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('administrador/administrador_material.twig', compact(
            'usuario',
            'tecnicos',
            'mensaje',
            'rol',
            'materiales',
            'mensajeEstablecer',
            'mensajeResultadoEstablecer',
            'uri'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}
