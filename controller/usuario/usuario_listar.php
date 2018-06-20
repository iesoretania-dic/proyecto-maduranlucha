<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0'){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../../index.php');
}else{

    /*Primero comprobamos si el rol viene por GET por la URL esto sera cuando pulsemos los botones del menu de navegacion
    pero cuando pusamos cancelar a la hora de insertar un usuario o bien el usuario se inserta con exito nos redirige a la pagina
    de los usuarios y debemos especificar el rol y como ya no esta por la url se lo enviamos de vuelta en una variable de session.*/
    comprobarSesion();
    if(isset($_GET['rol'])){
        if($_GET['rol'] == '1'){
            $rolUsuario = '1';
        }elseif($_GET['rol'] == '2'){
            $rolUsuario = '2';
        }elseif($_GET['rol'] == '4'){
            $rolUsuario = '4';
        }elseif($_GET['rol'] == '0'){
            $rolUsuario = '0';
        }
    }elseif(isset($_SESSION['rolUsuario'])){
        $rolUsuario = $_SESSION['rolUsuario'];
    }

    $usuario  = $_SESSION['usuario'];
    $rol = $_SESSION['rol'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $uri =  $_SERVER['REQUEST_URI'];
    $zona = 'password';

    //Comprobamos si hay cambios al modificar o añadir datos
    if(isset($_GET['cambios']) and $_GET['cambios'] == '0'){
        $mensajeCambios = 'Si';
    }

    if(isset($_GET['cambios']) and $_GET['cambios'] == '1'){
        $mensajeCambios = 'No';
    }

//    if(!isset($_SESSION['mod'])){
//        $zona = 'password';
//    }
//
//    if(isset($_SESSION['mod']) and $_SESSION['mod'] == 'formulario'){
//        $zona = 'formulario';
//    }else{
//        $zona = 'password';
//    }

    $mensaje = null;
    $usuarios = [];
    $sentencia = "SELECT * FROM usuario WHERE rol = :rol";
    $parametros = array(":rol"=>$rolUsuario);
    $datos = new Consulta();
    $usuarios = $datos->get_conDatos($sentencia,$parametros);

    if($usuarios){
        $mensaje = 'Ok';
    }else{
        $mensaje = 'error';
    }

    if(isset($_POST['aceptar'])){
        $datos = new Consulta();
        $hash = $datos->get_hash();
        $clave = $_POST['passwordVerificar'];
        if(password_verify($clave,$hash)){
            $_POST['passwordVerificar'] = '';
//            $_SESSION['mod'] = 'formulario';
            $zona = 'formulario';
        }
    }

    if(isset($_POST['addUsuario'])){
        header("Location: ../usuario/usuario_add.php");
        //Guardamos el rol en una variable de session para poder volver a esta pagina si insertamos usuarios nuevos.
        $_SESSION['rolUsuario'] = $rolUsuario;
    }



    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('usuario/usuario_listar.twig', compact(
            'usuario',
            'usuarios',
            'mensaje',
            'rol',
            'rolUsuario',
            'uri',
            'mensajeCambios',
            'zona'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}