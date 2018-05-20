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
    $idUsuario= $datos->get_id();
    $mensaje = null;
    $mensajedos = null;

    if(isset($_POST['btnEnviar'])){
        $dni = $_POST['dni'];
        $nombre = $_POST['nombre'];
        $ciudad = $_POST['ciudad'];
        $provincia = $_POST['provincia'];
        $cp = $_POST['cp'];
        $telefono = $_POST['telefono'];
        $comentario = $_POST['comentario'];
        $direccionP = $_POST['direccion'];
        $direccionTipo = $_POST['tipoD'];

        if($direccionTipo != ""){
            $direccion = $direccionTipo . $direccionP;
        }else{
            $direccion = $direccionP;
        }

        //El administrador da de alta un usuario pero no crea una incidencia automaticamente.
        if($rol == '0'){
            $cadena = "INSERT INTO cliente(dni,nombre,direccion,provincia,cp,ciudad,telefono) VALUES (:dni,:nombre,:cp,:provincia,:direccion,:ciudad,:telefono)";
            $parametros = array(":dni"=>$dni,":nombre"=>$nombre,":direccion"=>$direccion,"provincia"=>$provincia,"cp"=>$cp,"ciudad"=>$ciudad,":telefono"=>$telefono);
            $datos = new Consulta();
            $resultados = $datos->get_sinDatos($cadena,$parametros);

            if ($resultados > 0){
                $mensaje = 'Ok';
                header('Location: cliente_listar.php');
            }else{
                $mensaje = 'Error';
            }
        }elseif($rol == '1'){
            $cadena = "INSERT INTO cliente(dni,id_usuario,nombre,direccion,provincia,cp,ciudad,telefono) VALUES (:dni,:usuario,:nombre,:cp,:provincia,:direccion,:ciudad,:telefono)";
            $parametros = array(":dni"=>$dni,":usuario"=>$idUsuario,":nombre"=>$nombre,":direccion"=>$direccion,"provincia"=>$provincia,"cp"=>$cp,":ciudad"=>$ciudad,":telefono"=>$telefono);
            $datos = new Consulta();
            $resultados = $datos->get_sinDatos($cadena,$parametros);
            if ($resultados > 0){
                $mensaje = 'Ok';

                $cadena = "INSERT INTO incidencia(id_usuario,id_cliente,otros,tipo,estado) values (:usuario,:cliente,:comentario,:tipo,:estado)";
                $parametros = array(":usuario"=>$idUsuario,":cliente"=>$dni,":comentario"=>$comentario,":tipo"=>'instalacion',":estado"=>'1');
                $datos = new Consulta();
                $resultados = $datos->get_sinDatos($cadena,$parametros);
                if ($resultados > 0) {
                    $mensaje = 'Ok';
                    header('Location: cliente_listar.php');
                }else{
                    $mensaje = 'Error';
                }

            }else{
                $mensajedos = 'Error';
            }
        }
    }
    //si pulsa cancelar redirigimos a la pagina del comercial.
    if(isset($_POST['btnCancelar'])){
        header('Location: cliente_listar.php');;
    }

    if(isset($_SESSION['dniCliente'])){
        $dniB = $_SESSION['dniCliente'];
    }





    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_add.twig', compact(
            'usuario',
            'clientes',
            'mensaje',
            'mensajedos',
            'nombre',
            'dniB',
            'rol'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}