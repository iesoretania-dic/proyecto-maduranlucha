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
    $idUsuario = $datos->get_id();
    $comerciales =[];
    $mensaje = null;

    // Recuperamos la informacion del cliente
    if(isset($_GET['Id'])){
        $idCliente = $_GET['Id'];

        $consulta = "SELECT cliente.*, usuario.usuario as comercial, usuario.nombre as nombreComercial FROM cliente LEFT OUTER JOIN usuario ON  cliente.id_usuario = usuario.dni WHERE cliente.dni = :cliente";
        $parametros = array(":cliente"=>$idCliente);
        $datos = new Consulta();
        $cliente = $datos->get_conDatosUnica($consulta,$parametros);
        if ($rol == '0'){
            //Obtemos una lista de comerciales
            $consulta = "SELECT * FROM usuario WHERE rol = :rol";
            $parametros = array(":rol"=>'1');
            $datos = new Consulta();
            $comerciales = $datos->get_conDatos($consulta,$parametros);
        }
    }

    if(isset($_POST['btnModificar'])){
        $dniAntiguo =  $_GET['Id'];
        $dni = $_POST['dni'];
        $nombre = $_POST['nombre'];
        $direccion = $_POST['direccion'];
        $ciudad = $_POST['ciudad'];
        $telefono = $_POST['telefono'];

        if($rol == '1'){
            $consulta = "UPDATE cliente SET dni = :dni, nombre = :nombre, direccion = :direccion, ciudad = :ciudad, telefono = :telefono WHERE dni = :dniAntiguo";
            $parametros = array(":dni"=>$dni,":nombre"=>$nombre,":direccion"=>$direccion,":ciudad"=>$ciudad,":telefono"=>$telefono,":dniAntiguo"=>$dniAntiguo);
            $datos = new Consulta();
            $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

            if($filasAfectadas > 0){
                header('Location: cliente_listar.php');
                $mensaje = 'Ok';
            }else{
                $mensaje = 'error';
            }
        }
    }

    //si pulsa cancelar redirigimos a la pagina del comercial_cliente.
    if(isset($_POST['btnCancelar'])){
        header('Location: cliente_listar.php');
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_modificar.twig', compact(
            'usuario',
            'clientes',
            'mensaje',
            'cliente',
            'dni',
            'nombre',
            'comerciales',
            'rol'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}