<?php
require '../../php/Consulta.php';
require_once '../../php/funciones.php';

session_start();
//var_dump($_POST);
//var_dump($_SESSION);

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
    $idUsuario= $datos->get_id();
    $idIncidencia= $_GET['Id'];

    //Obtenemos el autor actual de la incidencia
    $consulta = "SELECT (SELECT nombre from usuario WHERE dni = id_usuario) as actual  FROM incidencia WHERE id_incidencia = :incidencia";
    $parametros = array(":incidencia"=>$idIncidencia);
    $datos = new Consulta();
    $actual = $datos->get_conDatosUnica($consulta,$parametros);

    //Obtenemos una lista de todos los usuarios adminsitradores y comerciales
    $consulta = "SELECT dni, nombre FROM usuario WHERE rol = '0' OR rol = '1'";
    $parametros = array();
    $datos = new Consulta();
    $usuarios = $datos->get_conDatos($consulta,$parametros);

    if(isset($_POST['btnAceptar'])){

        $nuevoCreador = $_POST['nuevoCreador'];
        //Si el nuevo creador es introducido cambiamos el creador de la incidencia
        if($nuevoCreador != ''){
            $consulta = "UPDATE incidencia SET id_usuario = :usuario WHERE id_incidencia = :incidencia";
            $parametros = array("usuario"=>$nuevoCreador,":incidencia"=>$idIncidencia);
            $datos = new Consulta();
            $resultado = $datos->get_sinDatos($consulta,$parametros);

            if ($resultado > 0){
                $mensaje = 'ok';
                header("Location: ../cliente/cliente_incidencias.php?tipo=0");
            }else{
                $mensaje = 'error';
            }

        }else{
            header("Location: ../cliente/cliente_incidencias.php?tipo=0");
        }

    }



    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_creador.twig', compact(
            'mensaje',
            'usuario',
            'rol',
            'usuarios',
            'actual'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}