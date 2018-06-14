<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();

if(!isset($_SESSION['usuario'])){
    header('Location: ../../index.php');
}elseif($_SESSION['rol'] != '0' and ($_SESSION['rol'] != '4')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: ../login/no_autorizado.php');
}else{

    $mensaje = null;
    $nombreTecnico = null;
    $rol = $_SESSION['rol'];
    $usuario  = $_SESSION['usuario'];
    $idIncidencia = $_GET['Id'];

    if(isset($_SESSION['tipo'])){
        $tipo = $_SESSION['tipo'];
    }

    if(isset($_SESSION['dni'])){
        $dni= $_SESSION['dni'];
    }

    //primero miramos si esa incidencia no la tiene como principal un tecnico
    $datos = new Consulta();
    $consulta = "SELECT nombre FROM usuario WHERE asignada = :idIncidencia";
    $parametros = array(":idIncidencia"=>$idIncidencia);
    $principal = $datos->get_conDatosUnica($consulta,$parametros);

    if($principal){
       $mensaje = 'principal';
       $nombreTecnico = $principal['nombre'];
    }else{
        //Obtenemos una lista de tecnicos disponibles
        $datos = new Consulta();
        $consulta = "SELECT dni, nombre FROM usuario WHERE rol = :rol";
        $parametros = array(":rol"=>'2');
        $tecnicos = $datos->get_conDatos($consulta,$parametros);
    }




    if(isset($_POST['btnAceptar'])){

        $nuevoTenico = $_POST['nuevoTecnico'];

        //Volvemos a comprobar si esta como principal por algun tecnico
        $datos = new Consulta();
        $consulta = "SELECT nombre FROM usuario WHERE asignada = :idIncidencia";
        $parametros = array(":idIncidencia"=>$idIncidencia);
        $nprincipal = $datos->get_conDatosUnica($consulta,$parametros);

        if(!$nprincipal){

            if($nuevoTenico != ''){

                $consulta = "UPDATE incidencia SET tecnico = :tecnico, estado = '2', fecha_inicio = :fecha, llamada_obligatoria = 'No' WHERE id_incidencia = :incidencia";
                $parametros = array(":tecnico"=>$nuevoTenico,":incidencia"=>$idIncidencia,":fecha"=>date("Y-m-d H:i:s"));
                $datos = new Consulta();
                $resultado = $datos->get_sinDatos($consulta,$parametros);

                if ($resultado > 0){
                    $mensaje = 'ok';
                    if(isset($_SESSION['dni']) and $_SESSION['dni'] != ""){
                        header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo."&dni=".$dni);
                    }else{
                        header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo);
                    }
                }else{
                    $mensaje = 'error';
                }


            }else{

                $consulta = "UPDATE incidencia SET tecnico = :tecnico, fecha_inicio = NULL, estado = '1', llamada_obligatoria = 'No' WHERE id_incidencia = :incidencia";
                $parametros = array(":tecnico"=>NULL,":incidencia"=>$idIncidencia);
                $datos = new Consulta();
                $resultado = $datos->get_sinDatos($consulta,$parametros);

                if ($resultado > 0){
                    $mensaje = 'ok';
                    if(isset($_SESSION['dni']) and $_SESSION['dni'] != ""){
                        header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo."&dni=".$dni);
                    }else{
                        header("Location: ../cliente/cliente_incidencias.php?tipo=".$tipo);
                    }
                }else{
                    $mensaje = 'error';
                }

            }

        }

    }



    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('cliente/cliente_incidencias_reasignar.twig', compact(
            'mensaje',
            'comentarios',
            'rol',
            'usuario',
            'tecnicos',
            'nombreTecnico'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}