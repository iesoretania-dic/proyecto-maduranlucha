<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';

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
    comprobarSesion();
    $mensaje = null;
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $fechaActual = date("Y-m-d H:i:s");
    $dniCliente = $_GET['dni'];

    if(isset($_SESSION['origen'])){
        $origen = $_SESSION['origen'];
    }

    //Consulta para obtener la informacion del cliente

    try {
        $datos = new Consulta();
        //Usamos uns transaccion para que en caso de error no ejecute ninguna sentencia.
        $datos->conexionDB->beginTransaction();

        //Consulta para obtener los datos de la incidencia
        $consulta = "SELECT dni, nombre, direccion, telefono, ciudad, provincia, ciudad, cp, routers, antenas, atas, fecha_alta as fecha_contrato, fecha_baja, (SELECT nombre from usuario WHERE dni = id_usuario) as id_usuario, (SELECT fecha_resolucion FROM incidencia WHERE id_cliente = :dni AND tipo = 'instalacion' ORDER BY incidencia.fecha_resolucion DESC LIMIT 1 ) as fecha_instalacion, (SELECT nombre from usuario WHERE dni = id_usuario) as id_usuario, (SELECT fecha_resolucion FROM incidencia WHERE id_cliente = :dni AND tipo = 'baja' ORDER BY incidencia.fecha_resolucion DESC LIMIT 1 ) as fecha_retirada, eliminado  FROM cliente WHERE cliente.dni = :dni";
        $parametros = array(":dni"=>$dniCliente);
        $cliente = $datos->get_conDatosUnica($consulta,$parametros);

        $datos->conexionDB->commit();

    } catch (PDOException $e) {
        $datos->conexionDB->rollBack();
        die('Error: ' . $e->getMessage());
    } finally {
        $datos->conexionDB = null;
    }




    if(isset($_POST['btnVolver'])){

        header('Location:'.$origen);

    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_info.twig', compact(
            'mensaje',
            'comentarios',
            'rol',
            'usuario',
            'cliente'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}