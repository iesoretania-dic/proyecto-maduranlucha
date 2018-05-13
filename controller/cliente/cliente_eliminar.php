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
    $dni = $_GET['Id'];
    $mensaje = null;

    //El administrador si elimina el cliente
    if($rol == '0'){ /**/
        if(isset($_POST['btnEliminar'])){
            $consulta ="DELETE FROM cliente WHERE dni = :dni";
            $parametros = array(":dni"=> $dni);
            $datos = new Consulta();
            $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

            if($filasAfectadas > 0){
                $mensaje = 'ok';
                header('Location: cliente_listar.php');
            }else{
                $mensaje = "error";
            }
        }
    }elseif($rol == '1' ){
        //El comercial no elimina el cliente solo cambia el estado de eliminado.
        if(isset($_POST['btnEliminar'])){
            $consulta = "UPDATE cliente SET eliminado = :eliminado WHERE dni = :dni";
            $parametros = array(":eliminado"=>'Si',":dni"=> $dni);
            $datos = new Consulta();
            $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

            if($filasAfectadas > 0){
                $mensaje = 'ok';
                header('Location: ../cliente/cliente_listar.php');
            }else{
                $mensaje = "error";
            }
        }
    }


    //si pulsa cancelar redirigimos a la pagina del comercial.
    if(isset($_POST['btnCancelar'])){
        header('Location: ../cliente/cliente_listar.php');
    }


    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_eliminar.twig', compact(
            'usuario',
            'clientes',
            'mensaje',
            'fila',
            'cliente',
            'dni',
            'nombre',
            'rol'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}