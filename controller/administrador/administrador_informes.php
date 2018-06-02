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
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $uri =  $_SERVER['REQUEST_URI'];

    $mensaje = null;
    $vista = 'botones';
    $consulta = null;

    //Lista de clientes
    $sentencia = "SELECT dni, nombre from cliente";
    $parametros = array();
    $datos = new Consulta();
    $clientes = $datos->get_conDatos($sentencia,$parametros);

    if(isset($_POST['bajaMaterial'])){

        $sentencia = "SELECT cliente.dni, cliente.nombre, cliente.antenas,cliente.telefono, cliente.routers, incidencia.tipo FROM cliente INNER JOIN incidencia ON cliente.dni = incidencia.id_cliente WHERE incidencia.tipo ='baja' and (cliente.routers  or cliente.antenas)";
        $parametros = array();
        $datos = new Consulta();
        $clientes = $datos->get_conDatos($sentencia,$parametros);

        if($clientes){
            $vista = 'consulta';
            $consulta = '1';
        }else{
            $mensaje = 'error';
        }
    }

    if(isset($_POST['incidenciasCliente'])){
        $_SESSION['origen'] = $_SERVER['REQUEST_URI'];
        $dniCliente = $_POST['dniCliente'];
        $datos = new Consulta();
        $nombreCliente = $datos->get_nombreCliente($dniCliente);

        $sentencia = "SELECT id_incidencia, (SELECT nombre FROM usuario WHERE dni= id_usuario) AS id_usuario, (SELECT nombre FROM usuario WHERE dni= tecnico) AS tecnico,fecha_creacion,fecha_inicio,fecha_resolucion,disponible,tipo,estado from incidencia where id_cliente= :dni ORDER BY estado = '0' DESC, estado = '1' DESC,estado = '2' DESC,estado = '3' DESC,estado = '4' DESC, fecha_resolucion DESC, fecha_creacion ASC";
        $parametros = array(":dni"=>$dniCliente);
        $datos = new Consulta();
        $incidencias = $datos->get_conDatos($sentencia,$parametros);

        if($incidencias){
            $vista = 'consulta';
            $consulta = '2';
        }else{
            $mensaje = 'error';
        }
    }

    if(isset($_POST['clientesBajaTiempo'])){

        $limite = $_POST['limite'];

        if($limite == 0){
            $limite = 120;
        }

        $sentencia = "SELECT fecha_alta,fecha_baja,nombre, (SELECT nombre from usuario WHERE usuario.dni = id_usuario) AS id_usuario,telefono,dni FROM cliente WHERE fecha_baja is NOT NULL";
        $parametros = array();
        $datos = new Consulta();
        $fechas = $datos->get_conDatos($sentencia,$parametros);
        $arraymeses = [];

        //Definimos el array
        for ($i = 1; $i < $limite+1; $i++) {
            $arraymeses[] = array('mes'=>$i,'valor'=>0,'clientes'=>null);
        }

        foreach ($fechas as $fecha){
            $tiempo_seg = restarfechas($fecha['fecha_alta'],$fecha['fecha_baja']);
            $cliente = array("nombre"=>$fecha['nombre'],"fecha_alta"=>$fecha['fecha_alta'],"fecha_baja"=>$fecha['fecha_baja'],"id_usuario"=>$fecha['id_usuario'],"telefono"=>$fecha['telefono']);
            $valor = round($tiempo_seg / 2592000);

            for ($i = 0; $i < $limite; $i++) {
                if($valor == $arraymeses[$i]['mes']){
                    $arraymeses[$i]['valor']++ ;
                    $arraymeses[$i]['clientes'][]=$cliente;
                }
            }
        }

        $vista = 'consulta';
        $consulta = '3';
    }

    if(isset($_POST['btnVolver'])){
        header('Location: ../administrador/administrador_informes.php');
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('administrador/administrador_informes.twig', compact(
            'usuario',
            'tecnicos',
            'mensaje',
            'rol',
            'vista',
            'clientes',
            'incidencias',
            'mensaje',
            'consulta',
            'tiempo_resultado',
            'arraymeses',
            'incidenciasTecnico',
            'nombreTecnico',
            'mensajeTecnicoResueltas',
            'nombreCliente',
            'uri'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}