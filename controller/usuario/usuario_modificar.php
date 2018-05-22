<?php
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
    $rolUsuario = $_SESSION['rolUsuario'];
    $comprobarDni = true;
    $comprobarUsuario = true;
    $comprobarPassword = true;
    $claveCoinciden = true;

    //Obtenemos la infomacion del usuario
    $dniUsuario = $_GET['Id'];
    $_SESSION['rolUsuario'] = $_GET['rolUsuario']; //Guardamos en una sesion el rol del usuario que estamos tratando.
    $consulta = "SELECT dni,usuario,nombre,telefono,limite FROM usuario WHERE dni = :dni";
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

        //Consulta para comprobar si existe el nombre de usuario
        $datos = new Consulta();


        //Si el dni introducido es diferente al actual hacemos una consulta para comprobar si existe el dni usuario
        if($dniUsuario != $dni){
            $datos = new Consulta();
            if ($datos->comprobarDniExiste($dni)){
                $comprobarDni = false;
            }
        }
        //Si el usuario introducido es diferente al actual hacemos una consulta para comprobar si existe el usuario
        if($usuarioAntiguo != $nUsuario){
            $datos = new Consulta();
            if ($datos->comprobarUsuarioExiste($nUsuario)){
                $comprobarUsuario = false;
            }
        }

        //Comprobamos si el campo password introducido esta en blanco
        if(($password != "") or $passwordR != ""){
            $comprobarPassword = false;
        }



        if($comprobarDni and $comprobarUsuario){
            if($comprobarPassword){

                try{
                    $cadena = "UPDATE usuario SET dni = :dni, usuario = :usuario, nombre = :nombre, telefono = :telefono,limite = :limite WHERE dni = :dniAntiguo";
                    $parametros = array(":dni"=>$dni,":usuario"=>$nUsuario,":nombre"=>$nombre,":telefono"=>$telefono,":limite"=>$limite,":dniAntiguo"=>$dniUsuario);
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



            }elseif($password == $passwordR){
                $passwordCifrado = codificar($password);

                try{
                    $cadena = "UPDATE usuario SET dni = :dni, usuario = :usuario, nombre = :nombre, telefono = :telefono, clave = :clave, limite = :limite WHERE dni = :dniAntiguo";
                    $parametros = array(":dni"=>$dni,":usuario"=>$nUsuario,":nombre"=>$nombre,":telefono"=>$telefono, ":clave"=>$passwordCifrado,":limite"=>$limite,":dniAntiguo"=>$dniUsuario);
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
            }else{
                $claveCoinciden = false;
            }
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
            'mensajeExiste',
            'mensajeUsuarioExiste',
            'rol',
            'claveNoCoincide',
            'nUsuario',
            'dni',
            'comprobarDni',
            'comprobarUsuario',
            'claveCoinciden',
            'rolUsuario'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}