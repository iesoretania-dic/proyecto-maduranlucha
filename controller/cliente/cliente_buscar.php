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
    $mensajeInstalacion = null;
    $mensajeUpdate = null;

    if(isset($_POST['btnBuscar'])){
        
        $dni = $_POST['dni'];
        if(strlen($dni) == 9){
            $_SESSION['dniCliente'] = $_POST['dni']; //Lo guardamos para recordar el dni del cliente para añadirlo

            $consulta = "SELECT * FROM cliente WHERE dni = :dni";
            $parametros = array(":dni"=>$dni);
            $datos = new Consulta();
            $cliente = $datos->get_conDatosUnica($consulta,$parametros);

            if($cliente){
                $mensaje = "ok";
            }else{
                $mensaje = "error";
                header('Location: cliente_add.php');
            }
        }else{
            $mensajeDniNoValido = 'error';
        }
    }

    //si pulsa cancelar redirigimos a la pagina del comercial.
    if(isset($_POST['btnCancelar'])){
        header('Location: cliente_listar.php');
    }
    if($rol == '1'){
        // si pulsa si añadimos el cliente al comercial.
        if(isset($_POST['btnAddSi'])){
            $dni = $_SESSION['dniCliente'];
            $comentario = $_POST['comentario'];

            //Consulta para añadir el cliente al comercial
            $consulta = "UPDATE cliente SET id_usuario = :usuario WHERE dni = :dni and id_usuario IS NULL";
            $parametros = array(":usuario"=>$idUsuario,":dni"=>$dni);
            $datos = new Consulta();
            $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

            if($filasAfectadas > 0){
                $mensajeUpdate = "ok";

                //Consulta para añadir una incidencia de tipo instalacion
                $cadena = "INSERT INTO incidencia(id_usuario,id_cliente,otros,tipo,estado) values (:usuario,:cliente,:comentario,:tipo,:estado)";
                $parametros = array(":usuario"=>$idUsuario,":cliente"=>$dni,":comentario"=>$comentario,":tipo"=>'instalacion',":estado"=>'1');
                $datos = new Consulta();
                $resultados = $datos->get_sinDatos($cadena,$parametros);

                if ($resultados > 0) {
                    $mensaje = 'Ok';
                    header('Location: cliente_listar.php');
                }else{
                    $mensajeInstalacion = 'error';
                }

            }else{
                $mensajeUpdate = "error";
            }
        }
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_buscar.twig', compact(
            'usuario',
            'clientes',
            'mensaje',
            'mensajeUpdate',
            'cliente',
            'dni',
            'nombre',
            'rol',
            'mensajeInstalacion',
            'mensajeDniNoValido'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}