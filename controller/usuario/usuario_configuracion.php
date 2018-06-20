<?php
require_once '../../php/Consulta.php';
require_once '../../php/funciones.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: index.php');
}else{
    comprobarSesion();
    $usuario  = $_SESSION['usuario'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $datos = new Consulta();
    $rol = $datos->get_rol();
    $arrayFilas = [];
    $datos = new Consulta();
    $nombre = $datos-> get_nombre();
    $comprobarUsuario = true;
    $comprobarPassword = true;
    $claveCoinciden = true;

    //Comprobamos si hay cambios al modificar o añadir datos
    if(isset($_GET['cambios']) and $_GET['cambios'] == '0'){
        $mensajeCambios = 'Si';
    }

    if(isset($_GET['cambios']) and $_GET['cambios'] == '1'){
        $mensajeCambios = 'No';
    }

    if(!isset($_SESSION['mod'])){
        $zona = 'password';
    }

    if(isset($_SESSION['mod']) and $_SESSION['mod'] == 'formulario'){
        $zona = 'formulario';
    }else{
        $zona = 'password';
    }


    if(isset($_POST['aceptar'])){

        $datos = new Consulta();
        $hash = $datos->get_hash();
        $clave = $_POST['passwordVerificar'];
        if(password_verify($clave,$hash)){
            $_POST['passwordVerificar'] = '';
            $_SESSION['mod'] = 'formulario';
            $zona = 'formulario';
        }
    }

    if(isset($_POST['btnModificar'])){

        $mensaje = null;
        $mensajeExiste = null;
        $mensajeUsuarioExiste = null;

        //Obtenemos los datos del formulario
        $nUsuario = $_POST['nUsuario'];
        $nombre= $_POST['nombre'];
        $password = $_POST['password'];
        $passwordR = $_POST['passwordR'];


        //Si el usuario introducido es diferente al actual hacemos una consulta para comprobar si existe el usuario
        if($usuario != $nUsuario){
            $datos = new Consulta();
            if ($datos->comprobarUsuarioExiste($nUsuario)){
                $comprobarUsuario = false;
            }
        }

        //Comprobamos si el campo password introducido esta en blanco
        if(($password != "") or $passwordR != ""){
            $comprobarPassword = false;
        }

        if($comprobarUsuario){
            if($comprobarPassword){
                $cadena = "UPDATE usuario SET  usuario = :usuario, nombre = :nombre WHERE dni = :dni";
                $parametros = array(":usuario"=>$nUsuario,":nombre"=>$nombre,":dni"=>$idUsuario);
                $datos = new Consulta();
                $resultados = $datos->get_sinDatos($cadena,$parametros);

                if ($resultados > 0){
                    $mensaje = 'Ok';
                    $zona = 'password';
                    $_SESSION['mod'] = 'password';
                    header("Location: ../usuario/usuario_configuracion.php?cambios=0");
                }else{
                    $mensaje = 'Ok';
                    $zona = 'password';
                    $_SESSION['mod'] = 'password';
                    header("Location: ../usuario/usuario_configuracion.php?cambios=1");
                }
            }elseif($password == $passwordR){
                $passwordCifrado = codificar($password);
                $cadena = "UPDATE usuario SET usuario = :usuario, nombre = :nombre, clave = :clave WHERE dni = :dni";
                $parametros = array(":usuario"=>$nUsuario,":nombre"=>$nombre,":clave"=>$passwordCifrado,":dni"=>$idUsuario);
                $datos = new Consulta();
                $resultados = $datos->get_sinDatos($cadena,$parametros);

                if ($resultados > 0){
                    $mensaje = 'Ok';
                    $zona = 'password';
                    $_SESSION['mod'] = 'password';
                    header("Location: ../usuario/usuario_configuracion.php?cambios=0");
                }else{
                    $mensaje = 'Ok';
                    $zona = 'password';
                    $_SESSION['mod'] = 'password';
                    header("Location: ../usuario/usuario_configuracion.php?cambios=1");
                }
            }else{
                $claveCoinciden = false;
            }
        }
    }

    if(isset($_POST['cancelar'])){
        $zona = 'password';
        $_SESSION['mod'] = 'password';
        header("Location: ../usuario/usuario_configuracion.php");
    }


    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('usuario/usuario_configuracion.twig', compact(
            'rol',
            'extend',
            'zona',
            'usuario',
            'mensaje',
            'mensajeUsuarioExiste',
            'claveNoCoincide',
            'comprobarUsuario',
            'claveCoinciden',
            'nombre',
            'mensajeCambios'

        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}