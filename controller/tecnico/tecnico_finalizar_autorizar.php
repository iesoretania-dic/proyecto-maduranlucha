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
    $usuario  = $_SESSION['usuario'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();

    //Recuperamos los valores de la url
    if(isset($_GET['Id'])){
        $idIncidencia = $_GET['Id'];
    }
    if(isset($_GET['tipo'])){
        $tipo = $_GET['tipo'];
    }
    if(isset($_GET['cliente'])){
        $dniCliente = $_GET['cliente'];
    }
    if(isset($_GET['usuario'])){
        $dniUsuario= $_GET['usuario'];
    }
    if(isset($_GET['modo'])){
        $modo= $_GET['modo'];
    }

    //Obtenemos la informacion que necesitamos del cliente y del usuario

    $sentencia = "SELECT * FROM usuario WHERE dni= :dni";
    $parametros = (array(":dni"=>$dniUsuario));
    $datos = new Consulta();
    $datosUsuario = $datos->get_conDatosUnica($sentencia,$parametros);

    $sentencia = "SELECT * FROM cliente WHERE dni = :dni";
    $parametros = (array(":dni"=>$dniCliente));
    $datos = new Consulta();
    $cliente= $datos->get_conDatosUnica($sentencia,$parametros);




    if(isset($_POST['aceptarContinuar'])){
        $_SESSION['asignada'] = $idIncidencia;
        $_SESSION['tipo'] = $tipo;
        //Usamos esta variable de sesion para forzar la vuelta a la pagina del administrador cuando finalize la incidencia.
        if($datosUsuario['asignada'] == $idIncidencia){
            $_SESSION['forzado'] = '1';
        }else{
            $_SESSION['forzado'] = '0';
        }

        //Datos del usuario
        $_SESSION['dniUsuario'] = $datosUsuario['dni'];
        $_SESSION['antenas'] = $datosUsuario['antenas'];
        $_SESSION['routers'] = $datosUsuario['routers'];
        $_SESSION['atas'] = $datosUsuario['atas'];/**/


        //Datos del cliente
        $_SESSION['dniCliente'] = $cliente['dni'];
        $_SESSION['antenasCliente'] = $cliente['antenas'];
        $_SESSION['routersCliente'] = $cliente['routers'];
        $_SESSION['atasCliente'] = $cliente['atas'];/**/

        //Finalmente redirigimos a la pagina de finalizar incidencia.
        header("Location: tecnico_finalizar.php");
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('tecnico/tecnico_finalizar_autorizar.twig', compact(
            'mensaje',
            'incidencia',
            'rol',
            'usuario',
            'datosUsuario'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}