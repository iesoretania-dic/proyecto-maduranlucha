<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0' and ($_SESSION['rol'] != '1')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{
    comprobarSesion();
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
        $dni = strtoupper(trim($_POST['dni']));
        $nombre = strtoupper(trim($_POST['nombre']));
        $ciudad = strtoupper(trim($_POST['ciudad']));
        $provincia = strtoupper(trim($_POST['provincia']));
        $cp = trim($_POST['cp']);
        $telefono = trim($_POST['telefono']);
        $direccionP = strtoupper(trim($_POST['direccion']));
        $direccionTipo = $_POST['tipoD'];

        if($direccionTipo != ""){
            $direccion = $direccionTipo . $direccionP;
        }else{
            $direccion = $direccionP;
        }

        $comprobarDNI = false;
        $dniMinimo = false;
        $comprobarVacios = false;
        $comprobarTelefono = false;
        $comprobarComentario = false;

        //Comprobamos no tengamos campos vacios
        if ($nombre == "" or $direccionP == "" or $telefono == "" or $dni == ""){
            $mensajeValidacionVacios = 'vacios';
        }else{
            $comprobarVacios = true;
        }

        //Validamos el dni
        if($dniAntiguo != $dni){
            $datos = new Consulta();
            //Comprobamos si el dni nuevo existe
            if ($datos->comprobarDniClienteExiste($dni)){
                $mensajeDniExiste = 'error';
                $comprobarDni = false;
            }else{
                $comprobarDni = true;
            }
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

        //Comprobamos la longitud del telefono
        if (strlen($telefono) < 9 ){
            $mensajeValidacionTelefono = 'telefonoNoValido';
        }else{
            $comprobarTelefono = true;
        }

        if ($rol == '0'){
            $comercial = $_POST['comercial'];
            $eliminado = $_POST['eliminado'];
            $antenas = $_POST['antena'];
            $routers = $_POST['router'];
            $atas = $_POST['ata'];
            $alta = $_POST['alta'];
            $baja = $_POST['baja'];

            if(strlen($alta) == 0){
                $alta = null;
            }

            if(strlen($baja) == 0){
                $baja = null;
            }

            if($comprobarVacios  AND $comprobarDni AND $dniMinimo AND  $comprobarTelefono){
                if($comercial == ""){
                    $comercial = null;
                }

                $consulta = "UPDATE cliente SET dni = :dni, id_usuario = :usuario, nombre = :nombre, direccion = :direccion, cp = :cp, provincia = :provincia, ciudad = :ciudad, telefono = :telefono, eliminado = :eliminado,antenas = :antenas, routers = :routers, atas = :atas, fecha_alta = :alta, fecha_baja = :baja WHERE dni = :dniAntiguo";
                $parametros = array(":dni"=>$dni, "usuario"=>$comercial ,":nombre"=>$nombre,":direccion"=>$direccion,":cp"=>$cp,":provincia"=>$provincia,":ciudad"=>$ciudad,":telefono"=>$telefono, ":eliminado"=>$eliminado ,":dniAntiguo"=>$dniAntiguo,":antenas"=>$antenas,":routers"=>$routers,":atas"=>$atas,"alta"=>$alta,":baja"=>$baja);
                $datos = new Consulta();
                $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

                if($filasAfectadas > 0){
                    header('Location: ../cliente/cliente_listar.php?cambios=0');
                }else{
                    header('Location: ../cliente/cliente_listar.php?cambios=1');
                }
            }
        }

        if($rol == '1'){
            if($comprobarVacios  AND $comprobarDni AND $dniMinimo AND $comprobarTelefono){
                $consulta = "UPDATE cliente SET dni = :dni, nombre = :nombre, direccion = :direccion, cp = :cp, provincia = :provincia, ciudad = :ciudad, telefono = :telefono WHERE dni = :dniAntiguo";
                $parametros = array(":dni"=>$dni,":nombre"=>$nombre,":direccion"=>$direccion,":cp"=>$cp,":provincia"=>$provincia,":ciudad"=>$ciudad,":telefono"=>$telefono,":dniAntiguo"=>$dniAntiguo);
                $datos = new Consulta();
                $filasAfectadas = $datos->get_sinDatos($consulta,$parametros);

                if($filasAfectadas > 0){
                    header('Location: ../cliente/cliente_listar.php?cambios=0');
                }else{
                    header('Location: ../cliente/cliente_listar.php?cambios=1');
                }
            }
        }
    }

    //si pulsa cancelar redirigimos a la pagina del comercial_cliente.
    if(isset($_POST['btnCancelar'])){
        header('Location: ../cliente/cliente_listar.php');
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
            'rol',
            'mensajeValidacionVacios',
            'mensajeDniMinimo',
            'mensajeValidacionTelefono',
            'mensajeDniExiste'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}