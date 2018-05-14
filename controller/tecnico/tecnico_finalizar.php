<?php
require_once '../../php/Consulta.php';

session_start();
//var_dump($_POST);
//var_dump($_SESSION);

if(!isset($_SESSION['usuario'])){
    header('Location: index.php');
}elseif(($_SESSION['rol'] != '2') and ($_SESSION['rol'] != '0')){
    $datos = new Consulta();
    $datos->set_noautorizado();
    header('Location: index.php');
}else {
    $rol = $_SESSION['rol'];
    $usuario = $_SESSION['usuario'];
    $tipo = $_SESSION['tipo'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();
    $asignada = $_SESSION['asignada'];
    $dniCliente = $_SESSION['dniCliente'];
    $antenaR = 0;
    $antenaI = 0;
    $routerR = 0;
    $routerI = 0;
    $antenasIncidencia = 0;
    $routersIncidencia = 0;
    $antenasCliente = $_SESSION['antenasCliente']; //Antenas instaladas en el cliente
    $routersCliente = $_SESSION['routersCliente']; //Routers instalados en el cliente
    $antenasDisponiblesTecnico = $_SESSION['antenas']; //Antenas disponibles por el tecnico
    $routersDisponiblesTecnico = $_SESSION['routers']; //Routers disponibles por el tecnico
    $modo = 'retirada';

    if (isset($_POST['btnConfirmarFinalizar'])) {

        //Accion en caso de instalación
        if ($tipo == 'instalacion') {
            $arrayInstalacion = [];

            if (isset($_POST['solucion']) and $_POST['solucion'] != 'otros') {
                $solucion = ($_POST['solucion']);
                array_push($arrayInstalacion, $solucion);
            }

            if (isset($_POST['otros']) and ($_POST['otros'] != '')) {
                $otros = ($_POST['otros']);
                array_push($arrayInstalacion, $otros);
            }

            //Si el checbox esta marcado establecemos el valor de la variable a 1.
            if (isset($_POST['antenas']) and ($_POST['antenas'] == '1')) {
                $antenasIncidencia = 1;
                array_push($arrayInstalacion, 'Instalacion de antena');
            }

            if (isset($_POST['routers']) and ($_POST['routers'] == '1')) {
                $routersIncidencia = 1;
                array_push($arrayInstalacion, 'Instalacion de router');
            }


            $listaInstalacion = json_encode($arrayInstalacion);

            $antenasResultado = $antenasDisponiblesTecnico;
            $routersResultado = $routersDisponiblesTecnico;
            $mensajeFaltaMaterial = null;
            $mensajeBaja = null;
            $mensajeRouter = null;

            //comprobamos que disponemos de suficiente material para la instalacion.
            if ($routersIncidencia <= $routersDisponiblesTecnico AND $antenasIncidencia <= $antenasDisponiblesTecnico) {
                //Comprobamos que se instaló el router
                if ($routersIncidencia == 1) {
                    $routersResultado -= $routersIncidencia;
                    $antenasResultado -= $antenasIncidencia;

                    $datos = new Consulta();

                    try {
                        //Usamos una transacción para que en caso de error no ejecute ninguna sentencia.
                        $datos->conexionDB->beginTransaction();

                        //Consulta para insertar el material a la incidencia, establecer el estado a finalizado y incluir la fecha de resolucion
                        $sentencia = "UPDATE incidencia SET estado=:estado, fecha_resolucion= :fechaRes, antenas = :antenas, routers = :routers, disponible = NULL WHERE id_incidencia = :id ";
                        $parametros = array(":estado" => '3', ":fechaRes" => date("Y-m-d H:i:s"), ":antenas" => $antenasIncidencia, ":routers" => $routersIncidencia, ":id" => $asignada);
                        $datos->get_sinDatos($sentencia, $parametros);

                        //Consulta para actualizar el material del tecnico y asignarle la instalacion
                        $sentencia = "UPDATE usuario SET asignada = :asignada, antenas = :antenas, routers = :routers WHERE dni = :dni ";
                        $parametros = array(":asignada" => NULL, ":antenas" => $antenasResultado, ":routers" => $routersResultado, ":dni" => $idUsuario);
                        $datos->get_sinDatos($sentencia, $parametros);

                        //Consulta para actualizar el material del cliente
                        $sentencia = "UPDATE cliente SET antenas = :antenas, routers = :routers WHERE dni = :dni ";
                        $parametros = array(":antenas" => $antenasIncidencia, ":routers" => $routersIncidencia, ":dni" => $dniCliente);
                        $datos->get_sinDatos($sentencia, $parametros);

                        //consulta para insertar la solucion
                        $sentencia = "INSERT INTO solucion (id_incidencia, solucion,tecnico) VALUES (:incidencia, :solucion, :tecnico)";
                        $parametros = array(":incidencia" => $asignada, ":solucion" => $listaInstalacion, ":tecnico" => $idUsuario);
                        $datos->get_sinDatos($sentencia, $parametros);

                        $datos->conexionDB->commit();
                        header("Location: tecnico.php");
                    } catch (PDOException $e) {
                        $datos->conexionDB->rollBack();
                        die('Error: ' . $e->getMessage());
                    } finally {
                        $datos->conexionDB = null;
                    }

                } else {
                    $mensajeRouter = 'error';
                }
            } else {
                $mensajeFaltaMaterial = 'error';
            }
        }

        //Accion en caso de baja
        if ($tipo == 'baja') {

            $antenaCliente = $antenasCliente;
            $routerCliente = $routersCliente;

            $arrayBaja = [];

            if (isset($_POST['solucion']) and $_POST['solucion'] != 'otros') {
                $solucion = ($_POST['solucion']);
                array_push($arrayBaja, $solucion);
            }

            if (isset($_POST['otros']) and ($_POST['otros'] != '')) {
                $otros = ($_POST['otros']);
                array_push($arrayBaja, $otros);
            }

            //Si el checbox esta marcado establecemos el valor de la variable a 1.
            if (isset($_POST['antenas']) and ($_POST['antenas'] == '1')) {
                $antenasIncidencia = 1;
                $antenaCliente--;
                array_push($arrayBaja, 'Retirada de antena');
            }

            if (isset($_POST['routers']) and ($_POST['routers'] == '1')) {
                $routersIncidencia = 1;
                $routerCliente--;
                array_push($arrayBaja, 'Retirada de router');
            }

            $listaBaja = json_encode($arrayBaja);

            //comprobamos si se a recogido el router
            if ($routersIncidencia == 1) {

                $routersDisponiblesTecnico++;
                if ($antenasIncidencia == 1) {
                    $antenasDisponiblesTecnico++;
                }

                $datos = new Consulta();

                try {
                    //Usamos una transacción para que en caso de error no ejecute ninguna sentencia.
                    $datos->conexionDB->beginTransaction();
                    //Consulta para insertar el material a la incidencia
                    $sentencia = "UPDATE incidencia SET estado=:estado, fecha_resolucion= :fechaRes,antenas = :antenas, routers = :routers, disponible = NULL WHERE id_incidencia = :id ";
                    $parametros = array(":estado" => '3', ":fechaRes" => date("Y-m-d H:i:s"), ":antenas" => $antenasIncidencia * (-1), ":routers" => $routersIncidencia * (-1), ":id" => $asignada);
                    $datos->get_sinDatos($sentencia, $parametros);

                    //Consulta para actualizar el material del tecnico
                    $sentencia = "UPDATE usuario SET asignada = :asignada, antenas = :antenas, routers = :routers WHERE dni = :dni ";
                    $parametros = array(":asignada" => NULL, ":antenas" => $antenasDisponiblesTecnico, ":routers" => $routersDisponiblesTecnico, ":dni" => $idUsuario);
                    $datos->get_sinDatos($sentencia, $parametros);

                    //Consulta para actualizar el material del cliente
                    $sentencia = "UPDATE cliente SET antenas = :antenas, routers = :routers WHERE dni = :dni ";
                    $parametros = array(":antenas" => $antenaCliente, ":routers" => $routerCliente, ":dni" => $dniCliente);
                    $datos->get_sinDatos($sentencia, $parametros);

                    //consulta para insertar la solucion
                    $sentencia = "INSERT INTO solucion (id_incidencia, solucion,tecnico) VALUES (:incidencia, :solucion, :tecnico)";
                    $parametros = array(":incidencia" => $asignada, ":solucion" => $listaBaja, ":tecnico" => $idUsuario);
                    $datos->get_sinDatos($sentencia, $parametros);

                    $datos->conexionDB->commit();
                    header("Location: tecnico.php");
                } catch (PDOException $e) {
                    $datos->conexionDB->rollBack();
                    die('Error: ' . $e->getMessage());
                } finally {
                    $datos->conexionDB = null;
                }

            } else {
                $mensajeBaja = 'materialRecoger';
            }
        }



    }

    //Accion si pulsa el boton cancelar
    if(isset($_POST['cancelarFinalizar'])){
        header("Location: ../tecnico/tecnico.php");
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('tecnico/tecnico_finalizar.twig', compact(
            'mensaje',
            'asignada',
            'cliente',
            'otros',
            'tipo',
            'llamada',
            'mensajeLlamada',
            'datosUsuario',
            'mensajeFaltaMaterial',
            'mensajeBaja',
            'usuario',
            'rol',
            'mensajeRouter',
            'mensajeAveria',
            'mensajeUsuario',
            'mensajeIncidencia',
            'mensajeFaltaRouter',
            'mensajeInstalacionRouter',
            'mensajeGeneralRouter',
            'mensajeCambioDomicilio',
            'modo'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}