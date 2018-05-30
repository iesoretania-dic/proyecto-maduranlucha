<<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
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

    $usuario  = $_SESSION['usuario'];
    $rol = $_SESSION['rol'];

    if(isset($_SESSION['rolUsuario'])){
        $rolUsuario = $_SESSION['rolUsuario'];
    }

    //Obtenemos la infomacion del usuario
    $dniUsuario = $_GET['Id'];
    $_SESSION['rolUsuario'] = $_GET['rolUsuario']; //Guardamos en una sesion el rol del usuario que estamos tratando.
    $consulta = "SELECT dni,usuario,nombre,telefono,limite,antenas,routers,atas FROM usuario WHERE dni = :dni";
    $parametros = array(":dni" => $dniUsuario);
    $datos = new Consulta();
    $datosUsuario = $datos->get_conDatosUnica($consulta, $parametros);
    $dniAntiguo = $datosUsuario['dni'];
    $usuarioAntiguo = $datosUsuario['usuario'];

    if(isset($_POST['btnModificar'])) {

        $mensaje = null;
        $mensajeExiste = null;
        $mensajeUsuarioExiste = null;

        //Obtenemos los datos del formulario
        $dni = $_POST['dni'];
        $nUsuario = $_POST['nUsuario'];
        $nombre= $_POST['nombre'];
        $password = $_POST['password'];
        $passwordR = $_POST['passwordR'];
        $telefono = $_POST['telefono'];
        if(isset($_POST['limite'])){
            $limite = $_POST['limite'];
        }

        if(isset($_POST['antenas'])){
            if(strlen($_POST['antenas']) > 0){
                $antenas = $_POST['antenas'];
            }else{
                $antenas = 0;
            }
        }

        if(isset($_POST['routers'])){
            if(strlen($_POST['routers']) > 0){
                $routers = $_POST['routers'];
            }else{
                $routers = 0;
            }
        }

        if(isset($_POST['atas'])){
            if(strlen($_POST['atas']) > 0){
                $atas = $_POST['atas'];
            }else{
                $atas = 0;
            }
        }

        $claveCoincide = false;
        $comprobarPassword = false;
        $paswordCoincide = false;
        $passwordNoCambia= false;
        $comprobarDni = false;
        $dniMinimo = false;
        $usuarioMinimo = false;
        $telefoNoValido = false;


        //Consulta para comprobar si existe el nombre de usuario
        $datos = new Consulta();

        //VALIDACION DEL DNI*************************
        //Si el dni introducido es diferente al actual hacemos una consulta para comprobar si existe el dni usuario
        if($dniUsuario != $dni){
            $datos = new Consulta();
            //Comprobamos si el dni nuevo existe
            if ($datos->comprobarDniExiste($dni)){
                $mensajeDniExiste = 'error';
                $comprobarDni = false;
            }else{
                $comprobarDni = true;
            }
            //Comprobamos si el dni es valido
            //Comprobamos que el dni tenga 9 caracteres
            if(strlen($dni) != 9){
                $mensajeDniMinimo = 'error';
            }else{
                $dniMinimo = true;
            }
        }else{
            $comprobarDni = true;
            $dniMinimo = true;
        }
        //********************************************

        //VALIDACION DEL PASSWORD*************************
        //Comprobamos que la clave es valida
        if ($password != $passwordR){
            $claveNoCoincide = 'error';
        }else {
            $claveCoincide = true;
        }

        //Comprobamos que la clave tenga al menos 5 caracteres si se a cambiado
        if ($password == "" AND $passwordR == ""){
            $passwordNoCambia= true;
        }else{
            if (strlen($password) < 5 OR  strlen($passwordR) < 5){
                $mensajeClaveNoValida = 'error';
            }else{
                $comprobarPassword = true;
            }

            if($passwordR != $password){
                $mensajeClaveNoCoincide = 'error';
            }else{
                $paswordCoincide = true;
            }
        }
        //********************************************
        //VALIDACION DEL USUARIO**********************
        //Si el usuario introducido es diferente al actual hacemos una consulta para comprobar si existe el usuario
        if($usuarioAntiguo != $nUsuario){
            $datos = new Consulta();
            if ($datos->comprobarUsuarioExiste($nUsuario)){
                $comprobarUsuario = false;
                $mensajeUsuarioExiste = 'error';
            }else{
                $comprobarUsuario = true;
            }

            if(strlen($nUsuario) < 5){
                $mensajeUsuarioMinimo = 'error';
            }else{
                $usuarioMinimo = true;
            }

        }else{
            $comprobarUsuario = true;
            $usuarioMinimo = true;
        }

        //********************************************
        //VALIDACION DEL TELEFONO*********************

        //Comprobamos que el telefono tenga al menos 9 caracteres (no ponemos limite ni limitamos por numeros por que hay la posibidad de introducir mas de un teléfono)
        if(strlen($telefono) < 9){
            $mensajeTelefoNoValido = 'error';
        }else{
            $telefoNoValido = true;
        }

        //********************************************
        if($passwordNoCambia){

            if($comprobarDni AND $dniMinimo AND $comprobarUsuario AND $usuarioMinimo AND $telefoNoValido){
                try{
                    $cadena = "UPDATE usuario SET dni = :dni, usuario = :usuario, nombre = :nombre, telefono = :telefono,limite = :limite,antenas = :antenas, routers = :routers,atas = :atas WHERE dni = :dniAntiguo";
                    $parametros = array(":dni"=>$dni,":usuario"=>$nUsuario,":nombre"=>$nombre,":telefono"=>$telefono,":limite"=>$limite,":dniAntiguo"=>$dniUsuario,":antenas"=>$antenas,":routers"=>$routers,":atas"=>$atas);
                    $datos = new Consulta();
                    $resultados = $datos->get_sinDatos($cadena,$parametros);

                    if ($resultados > 0){
                        header('Location: ../usuario/usuario_listar.php?cambios=0');
                    }else{
                        header('Location: ../usuario/usuario_listar.php?cambios=1');
                    }
                }catch(Exception $e){
                    $mensaje = 'error';
                    die('Error: ' . $e->GetMessage());
                }
            }
        }

        if(!$passwordNoCambia AND $comprobarPassword AND $paswordCoincide){

            if($comprobarDni AND $dniMinimo AND $comprobarUsuario AND $usuarioMinimo AND $telefoNoValido){

                $passwordCifrado = codificar($password);

                try{
                    $cadena = "UPDATE usuario SET dni = :dni, usuario = :usuario, nombre = :nombre, telefono = :telefono, clave = :clave, limite = :limite ,antenas = :antenas, routers = :routers,atas = :atas WHERE dni = :dniAntiguo";
                    $parametros = array(":dni"=>$dni,":usuario"=>$nUsuario,":nombre"=>$nombre,":telefono"=>$telefono, ":clave"=>$passwordCifrado,":limite"=>$limite,":dniAntiguo"=>$dniUsuario,":antenas"=>$antenas,":routers"=>$routers,":atas"=>$atas);
                    $datos = new Consulta();
                    $resultados = $datos->get_sinDatos($cadena,$parametros);

                    if ($resultados > 0){
                        header('Location: ../usuario/usuario_listar.php?cambios=0');
                    }else{
                        header('Location: ../usuario/usuario_listar.php?cambios=1');
                    }

                }catch(Exception $e){
                    $mensaje = 'error';
                    die('Error: ' . $e->GetMessage());
                }
            }

        }else{
            $claveCoinciden = false;
        }
    }



    if(isset($_POST['cancelar'])){
        header('Location: ../usuario/usuario_listar.php');
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('usuario/usuario_modificar.twig', compact(
            'usuario',
            'datosUsuario',
            'comerciales',
            'mensaje',
            'rol',
            'claveNoCoincide',
            'nUsuario',
            'dni',
            'comprobarDni',
            'comprobarUsuario',
            'claveCoinciden',
            'rolUsuario',
            'mensajeDniExiste',
            'mensajeDniMinimo',
            'mensajeClaveNoValida',
            'mensajeClaveNoCoincide',
            'mensajeUsuarioExiste',
            'mensajeUsuarioMinimo',
            'mensajeTelefoNoValido'

        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}