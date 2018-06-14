<?php
require '../../php/Consulta.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0' and ($_SESSION['rol'] != '1') and ($_SESSION['rol'] !='4')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{

    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $datos = new Consulta();
    $idUsuario= $datos->get_id();
    $fechaActual = date("Y-m-d H:i:s");
    $_SESSION['origen'] = $_SERVER['REQUEST_URI'];
    $uri =  $_SERVER['REQUEST_URI'];

    if(isset($_GET['tipo'])){
        $tipo = $_GET['tipo'];
        $_SESSION['tipo'] = $_GET['tipo'];
    }

    if(isset($_GET['dni'])){
        $dniCliente = $_GET['dni'];
        $_SESSION['dni'] = $_GET['dni'];
    }else{
        $_SESSION['dni'] ='';
    }

    if(isset($dniCliente)){
        $datos = new Consulta();
        $nombreCliente = $datos->get_nombreCliente($dniCliente);
    }

    //ZONA DE LOS ADMINISTRADORES
    if ($rol == '0'){
        //Consulta si viene desde el apartado de incidencias
        if($tipo == '0'){
            //Consulta para obtener todas las incidencias
            $consulta = "SELECT incidencia.*, usuario.nombre as tnombre,(SELECT nombre from usuario WHERE dni = incidencia.tecnico) as ntecnico,(SELECT nombre from cliente WHERE cliente.dni = incidencia.id_cliente) as ncliente,(SELECT ciudad FROM cliente WHERE dni = id_cliente) as poblacion FROM incidencia INNER JOIN usuario ON incidencia.id_usuario = usuario.dni ORDER BY  incidencia.estado = '0' DESC, incidencia.estado = '1' DESC, incidencia.estado = '2' DESC,incidencia.estado = '4' DESC, incidencia.estado = '3' DESC, incidencia.fecha_resolucion DESC, incidencia.fecha_creacion ASC";
            $parametros = array();
            $datos = new Consulta();
            $arrayFilas = $datos->get_conDatos($consulta,$parametros);

            if($arrayFilas){
                $mensaje = 'Si';
            }else{
                $mensaje = 'No';
            }
        }

        //Consulta si viene desde el apartado de los clientes, para un cliente en especifico.
        if($tipo == '1'){
            //CONSULTA PARA OBTENER Las incidencias de un cliente.
            $consulta = "SELECT incidencia.*, usuario.nombre as tnombre,(SELECT nombre from usuario WHERE dni = incidencia.tecnico) as ntecnico,(SELECT nombre from cliente WHERE cliente.dni = incidencia.id_cliente) as ncliente ,(SELECT ciudad FROM cliente WHERE dni = id_cliente) as poblacion FROM incidencia  INNER JOIN usuario ON incidencia.id_usuario = usuario.dni where incidencia.id_cliente = :dni ORDER BY  incidencia.estado = '0' DESC, incidencia.estado = '1' DESC, incidencia.estado = '2' DESC,incidencia.estado = '4' DESC, incidencia.estado = '3' DESC, incidencia.fecha_resolucion DESC, incidencia.fecha_creacion ASC";
            $parametros = array(":dni"=>$dniCliente);
            $datos = new Consulta();
            $arrayFilas = $datos->get_conDatos($consulta,$parametros);

            if($arrayFilas){
                $mensaje = 'Si';
            }else{
                $mensaje = 'No';
            }
        }

        if($tipo == '1'){
            //Accion si se pulsa el boton añadir incidencia
            if(isset($_POST['btnAdd'])){
                $_SESSION['dniCliente'] = $dniCliente;
                header('Location: cliente_incidencias_add.php?tipo=0');
            }
            //Accion si se pulsa el boton volver
            if(isset($_POST['btnVolver'])){
                header('Location: cliente_listar.php');
            }
        }
    }
    //ZONA DE LOS COMERCIALES
    if ($rol == '1'){
        //Consulta si viene desde el apartado de incidencias
        if($tipo == '0'){
            //Consulta para obtener todas las incidencias
            $consulta = "SELECT incidencia.*, usuario.nombre as tnombre,(SELECT nombre from usuario WHERE dni = incidencia.tecnico) as ntecnico,(SELECT nombre from cliente WHERE cliente.dni = incidencia.id_cliente) as ncliente ,(SELECT ciudad FROM cliente WHERE dni = id_cliente) as poblacion FROM incidencia INNER JOIN usuario ON incidencia.id_usuario = usuario.dni WHERE incidencia.id_usuario = :comercial ORDER BY  incidencia.estado = '0' DESC, incidencia.estado = '1' DESC, incidencia.estado = '2' DESC,incidencia.estado = '4' DESC, incidencia.estado = '3' DESC, incidencia.fecha_resolucion DESC, incidencia.fecha_creacion ASC";
            $parametros = array(":comercial"=>$idUsuario);
            $datos = new Consulta();
            $arrayFilas = $datos->get_conDatos($consulta,$parametros);

            if($arrayFilas){
                $mensaje = 'Si';
            }else{
                $mensaje = 'No';
            }
        }

        //Consulta si viene desde el apartado de los clientes, para un cliente en especifico.
        if($tipo == '1'){
            //CONSULTA PARA OBTENER Las incidencias de un cliente.
            $consulta = "SELECT incidencia.*, usuario.nombre as tnombre,(SELECT nombre from usuario WHERE dni = incidencia.tecnico) as ntecnico,(SELECT nombre from cliente WHERE cliente.dni = incidencia.id_cliente) as ncliente ,(SELECT ciudad FROM cliente WHERE dni = id_cliente) as poblacion FROM incidencia INNER JOIN usuario ON incidencia.id_usuario = usuario.dni WHERE incidencia.id_usuario = :comercial AND incidencia.id_cliente = :dni ORDER BY  incidencia.estado = '0' DESC, incidencia.estado = '1' DESC, incidencia.estado = '2' DESC,incidencia.estado = '4' DESC, incidencia.estado = '3' DESC, incidencia.fecha_creacion";
            $parametros = array(":comercial"=>$idUsuario,":dni"=>$dniCliente);
            $datos = new Consulta();
            $arrayFilas = $datos->get_conDatos($consulta,$parametros);

            if($arrayFilas){
                $mensaje = 'Si';
            }else{
                $mensaje = 'No';
            }
        }

        if($tipo == '1'){
            //Accion si se pulsa el boton añadir incidencia
            if(isset($_POST['btnAdd'])){
                $_SESSION['dniCliente'] = $dniCliente;
                header('Location: cliente_incidencias_add.php?tipo=0');
            }
            //Accion si se pulsa el boton volver
            if(isset($_POST['btnVolver'])){
                header('Location: cliente_listar.php');
            }
        }
    }
    //ZONA DE LOS CONTROLADORES
    if ($rol == '4'){
        //Consulta para devolver las incidencias de tipo averia a el controller.
        $sentencia = "SELECT incidencia.id_incidencia,incidencia.estado,incidencia.id_usuario,incidencia.id_cliente,incidencia.fecha_creacion,incidencia.otros,incidencia.urgente,cliente.nombre as nombreCliente, cliente.direccion, cliente.ciudad, cliente.telefono, usuario.usuario as usuarioUsuario, usuario.nombre as nombreUsuario FROM incidencia INNER JOIN cliente ON incidencia.id_cliente = cliente.dni INNER JOIN usuario ON incidencia.id_usuario = usuario.dni WHERE incidencia.tipo = :tipo ORDER BY incidencia.estado = '0' DESC, incidencia.estado = '1' DESC, incidencia.estado = '2' DESC, incidencia.estado = '4' DESC, incidencia.estado = '3' DESC, incidencia.fecha_resolucion DESC, incidencia.fecha_creacion ASC";
        $parametros = array(":tipo"=>'averia');
        $datos = new Consulta();
        $arrayFilas =  $datos->get_conDatos($sentencia,$parametros);

        if($arrayFilas){
            $mensaje = 'Si';
        }else{
            $mensaje = 'No';
        }
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias.twig', compact(
            'arrayFilas',
            'dniCliente',
            'mensaje',
            'rol',
            'usuario',
            'tipo',
            'nombreCliente',
            'fechaActual',
            'uri'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}