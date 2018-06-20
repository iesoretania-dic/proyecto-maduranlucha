<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0'){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../../index.php');
}else{
    comprobarSesion();
    $usuario  = $_SESSION['usuario'];
    $rol = $_SESSION['rol'];
    $rolUsuario = $_SESSION['rolUsuario']; //Guardamos el rol del usuario añadido en una variable de sesion para poder volver a la zona indicada de los usuarios.

    if(isset($_POST['btnEnviar'])){

        //Recuperamos los datos del formulario
        $dni = trim($_POST['dni']);
        $nUsuario = trim($_POST['nUsuario']);
        $password = trim($_POST['password']);
        $passwordR = trim($_POST['passwordR']);
        $nombre = trim($_POST['nombre']);
        $telefono = trim($_POST['telefono']);

        if(isset($_POST['limite'])){
            $limite = $_POST['limite'];
        }

        if($rolUsuario == '2'){
            $antenas = 0;
            $routers = 0;
            $atas = 0;
        }else{
            $antenas = null;
            $routers = null;
            $atas = null;
        }

        $existe = null;
        $usuarioExiste = null;
        $mensaje = null;
        $mensajeExiste = null;
        $mensajeUsuarioExiste = null;

        $dniValido = false;
        $nombreValido = false;
        $claveCoincide = false;
        $claveNoVacia = false;
        $camposNoVacios = false;
        $dniMinimo = false;
        $usuarioMinimo = false;
        $telefonoValido = false;

        //Consulta para comprobar si existe el dni
        $datos = new Consulta();

        if($dni != "" ){
            $existe = $datos->comprobarDniExiste($dni);
            //comprobamos que el dni no exista
            if($existe == $dni){
                $mensajeExiste = 'error';
            }else{
                $dniValido = true;
            }
        }
        //Consulta para comprobar si existe el usuario
        $datos = new Consulta();
        if($nUsuario != "" ){
            $usuarioExiste = $datos->comprobarUsuarioExiste($nUsuario);
            if($usuarioExiste == $nUsuario) {
                $mensajeUsuarioExiste = 'error';
            }else{
                $nombreValido = true;
            }
        }

        //Comprobamos que la clave es valida
        if ($password != $passwordR){
            $claveNoCoincide = 'error';
        }else {
            $claveCoincide = true;
        }

        //Comprobamos que la clave no este vacia y su longitud minima se de 5 caracetres
        if ($password == "" OR  $passwordR == "" OR strlen($password) < 5 OR  strlen($passwordR) < 5){
            $mensajeClaveNoValida = 'error';
        }else{
            $claveNoVacia = true;
        }

        //Comprobamos que los campos no vengan vacios
        if($dni == "" OR $usuario == "" OR $nombre == "" OR $telefono == ""){
            $mensajeNoVacios = 'error';
        }else{
            $camposNoVacios = true;
        }

        //Comprobamos que el dni tenga 9 caracteres
        if(strlen($dni) != 9){
            $mensajeDniMinimo = 'error';
        }else{
            $dniMinimo = true;
        }

        //Comprobamos que el nombre de usuario tenga al menos 5 caracteres
        if(strlen($nUsuario) < 4){
            $mensajeUsuarioMinimo = 'error';
        }else{
            $usuarioMinimo = true;
        }

        //Comprobamos que el telefono tenga al menos 9 caracteres (no ponemos limite ni limitamos por numeros por que hay la posibidad de introducir mas de un teléfono)
        if(strlen($telefono) < 9){
            $mensajeTelefonoValido = 'error';
        }else{
            $telefonoValido = true;
        }

        if($dniValido AND $nombreValido AND $claveCoincide AND $claveNoVacia AND $camposNoVacios AND $dniMinimo AND $usuarioMinimo AND $telefonoValido) {
            $passwordCifrado = codificar($password);

            try{
                $cadena = "INSERT INTO usuario(dni,usuario,nombre,telefono,clave,rol,limite,antenas,routers,atas) values (:dni,:usuario,:nombre,:telefono,:clave,:rol,:limite,:antenas,:routers,:atas)";
                $parametros = array(":dni"=>$dni,":usuario"=>$nUsuario,":nombre"=>$nombre,":telefono"=>$telefono,":clave"=>$passwordCifrado,":rol"=>$rolUsuario,":limite"=>$limite,":antenas"=>$antenas,":routers"=>$routers,":atas"=>$atas);
                $datos = new Consulta();
                $resultados = $datos->get_sinDatos($cadena,$parametros);

                if ($resultados > 0){

                    header('Location: ../usuario/usuario_listar.php?cambios=0');
                }else{
                    header('Location: ../usuario/usuario_listar.php?cambios=1');
                }


            }catch (Exception $e){
                $mensaje = 'error';
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
        echo $twig->render('usuario/usuario_add.twig', compact(
            'usuario',
            'comerciales',
            'mensaje',
            'mensajeExiste',
            'mensajeUsuarioExiste',
            'mensajeClaveNoValida',
            'mensajeNoVacios',
            'rol',
            'claveNoCoincide',
            'nUsuario',
            'nombre',
            'telefono',
            'dni',
            'rolUsuario',
            'mensajeDniMinimo',
            'mensajeUsuarioMinimo',
            'mensajeTelefonoValido'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}