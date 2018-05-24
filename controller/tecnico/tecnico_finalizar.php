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
    $atasR = 0;
    $atasI = 0;
    $antenasIncidencia = 0;
    $routersIncidencia = 0;
    $atasIncidencia = 0;
    $antenasCliente = $_SESSION['antenasCliente']; //Antenas instaladas en el cliente
    $routersCliente = $_SESSION['routersCliente']; //Routers instalados en el cliente
    $atasCliente = $_SESSION['atasCliente']; //Atas instalados en el cliente
    $antenasDisponiblesTecnico = $_SESSION['antenas']; //Antenas disponibles por el tecnico
    $routersDisponiblesTecnico = $_SESSION['routers']; //Routers disponibles por el tecnico
    $atasDisponiblesTecnico = $_SESSION['atas']; //Atas disponibles por el tecnico
    $modo = 'retirada';

    if (isset($_POST['btnConfirmarFinalizar'])) {

        //Accion en caso de instalación
        if ($tipo == 'instalacion') {
            $arrayInstalacion = [];

            //en el caso de que el desplegable tenga una opcion diferente a otros se registra.
            if (isset($_POST['solucion']) and $_POST['solucion'] != 'otros') {
                $solucion = ($_POST['solucion']);
                array_push($arrayInstalacion, $solucion);
            }
            //si el desplegable tiene como seleccionado 'otros' se registrara como solucion el contenido del textarea.
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

            if (isset($_POST['atas']) and ($_POST['atas'] == '1')) {/**/
                $atasIncidencia = 1;
                array_push($arrayInstalacion, 'Instalacion de ata');
            }

            //recogemos todas las soluciones marcadas y lo pasamos a json para guardarlo en la base de datos.
            $listaInstalacion = json_encode($arrayInstalacion);

            $antenasResultado = $antenasDisponiblesTecnico;
            $routersResultado = $routersDisponiblesTecnico;
            $atasResultado = $atasDisponiblesTecnico;
            $mensajeFaltaMaterial = null;
            $mensajeBaja = null;
            $mensajeRouter = null;

            //Obtenemos el contador del cable  /**/
            $consulta = "SELECT contador FROM material WHERE id_usuario = :usuario and terminado = :terminado AND nombre = :nombre";
            $parametros = array(":usuario"=>$idUsuario,":terminado"=>'No',":nombre"=>'cajacable');
            $datos = new Consulta();
            $datosMaterialCable = $datos->get_conDatosUnica($consulta,$parametros);
            $contadorCable = $datosMaterialCable['contador'];

            if(!$datosMaterialCable){ //*//
                $mensajeFaltaCable = 'error';
            }

            //Obtenemos el contador del conector  /**/
            $consulta = "SELECT contador FROM material WHERE id_usuario = :usuario and terminado = :terminado AND nombre = :nombre";
            $parametros = array(":usuario"=>$idUsuario,":terminado"=>'No',":nombre"=>'bolsaconectores');
            $datos = new Consulta();
            $datosMaterialConector = $datos->get_conDatosUnica($consulta,$parametros);
            $contadorConector = $datosMaterialConector['contador'];

            if(!$datosMaterialConector){ //*//
                $mensajeFaltaConector = 'error';
            }

            //comprobamos que disponemos de suficiente material para la instalacion.
            if ($routersIncidencia <= $routersDisponiblesTecnico AND $antenasIncidencia <= $antenasDisponiblesTecnico AND $atasIncidencia <= $atasDisponiblesTecnico  AND $datosMaterialCable AND $datosMaterialConector) {
                //Comprobamos que se instaló el router
                if ($routersIncidencia == 1) {
                    $routersResultado -= $routersIncidencia;
                    $antenasResultado -= $antenasIncidencia;
                    $atasResultado -= $atasIncidencia;

                    $datos = new Consulta();

                    try {
                        //Usamos una transacción para que en caso de error no ejecute ninguna sentencia.
                        $datos->conexionDB->beginTransaction();

                        //Consulta para insertar el material a la incidencia, establecer el estado a finalizado y incluir la fecha de resolucion ****
                        $sentencia = "UPDATE incidencia SET estado=:estado, fecha_resolucion= :fechaRes, antenas = :antenas, routers = :routers,atas = :atas ,disponible = NULL, urgente = :urgente WHERE id_incidencia = :id ";
                        $parametros = array(":estado" => '3', ":fechaRes" => date("Y-m-d H:i:s"), ":antenas" => $antenasIncidencia, ":routers" => $routersIncidencia,":atas"=>$atasIncidencia, ":id" => $asignada,":urgente"=>'No');
                        $datos->get_sinDatos($sentencia, $parametros);

                        //Consulta para actualizar el material del tecnico y asignarle la instalacion ****
                        $sentencia = "UPDATE usuario SET asignada = :asignada, antenas = :antenas, routers = :routers, atas = :atas WHERE dni = :dni ";
                        $parametros = array(":asignada" => NULL, ":antenas" => $antenasResultado, ":routers" => $routersResultado,":atas"=>$atasResultado, ":dni" => $idUsuario);
                        $datos->get_sinDatos($sentencia, $parametros);

                        //Consulta para actualizar el material del cliente ****
                        $sentencia = "UPDATE cliente SET antenas = :antenas, routers = :routers, atas = :atas WHERE dni = :dni ";
                        $parametros = array(":antenas" => $antenasIncidencia, ":routers" => $routersIncidencia,":atas"=>$atasIncidencia, ":dni" => $dniCliente);
                        $datos->get_sinDatos($sentencia, $parametros);

                        //consulta para insertar la solucion
                        $sentencia = "INSERT INTO solucion (id_incidencia, solucion,tecnico) VALUES (:incidencia, :solucion, :tecnico)";
                        $parametros = array(":incidencia" => $asignada, ":solucion" => $listaInstalacion, ":tecnico" => $idUsuario);
                        $datos->get_sinDatos($sentencia, $parametros);

                        //consulta para añadir el cable  /**/
                        $contadorCable++;
                        $sentencia = "UPDATE material SET contador = :contador WHERE id_usuario = :usuario AND terminado = :terminado AND nombre = :nombre ";
                        $parametros = array(":contador" => $contadorCable, ":usuario" => $idUsuario,":nombre"=>'cajacable',":terminado"=>'No');
                        $datos->get_sinDatos($sentencia, $parametros);

                        //consulta para añadir el conector  /**/
                        $contadorConector++;
                        $sentencia = "UPDATE material SET contador = :contador  WHERE id_usuario = :usuario AND terminado = :terminado AND nombre = :nombre ";
                        $parametros = array(":contador" => $contadorConector, ":usuario" => $idUsuario,":nombre"=>'bolsaconectores',":terminado"=>'No');
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
            $ataCliente = $atasCliente;/**/

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

            if (isset($_POST['atas']) and ($_POST['atas'] == '1')) {
                $atasIncidencia = 1;
                $ataCliente--;
                array_push($arrayBaja, 'Retirada de ata');
            }

            $listaBaja = json_encode($arrayBaja);

            //comprobamos si se a recogido el router
            if ($routersIncidencia == 1) {

                $routersDisponiblesTecnico++;
                if ($antenasIncidencia == 1) {
                    $antenasDisponiblesTecnico++;
                }

                if ($atasIncidencia == 1) {
                    $atasDisponiblesTecnico++;
                }

                $datos = new Consulta();

                try {
                    //Usamos una transacción para que en caso de error no ejecute ninguna sentencia.
                    $datos->conexionDB->beginTransaction();
                    //Consulta para insertar el material a la incidencia ****
                    $sentencia = "UPDATE incidencia SET estado=:estado, fecha_resolucion= :fechaRes,antenas = :antenas, routers = :routers,atas = :atas, disponible = NULL WHERE id_incidencia = :id ";
                    $parametros = array(":estado" => '3', ":fechaRes" => date("Y-m-d H:i:s"), ":antenas" => $antenasIncidencia * (-1), ":routers" => $routersIncidencia * (-1),":atas"=>$atasIncidencia * (-1),":id" => $asignada);
                    $datos->get_sinDatos($sentencia, $parametros);

                    //Consulta para actualizar el material del tecnico ****
                    $sentencia = "UPDATE usuario SET asignada = :asignada, antenas = :antenas, routers = :routers,atas = :atas WHERE dni = :dni ";
                    $parametros = array(":asignada" => NULL, ":antenas" => $antenasDisponiblesTecnico, ":routers" => $routersDisponiblesTecnico,":atas"=>$atasDisponiblesTecnico, ":dni" => $idUsuario);
                    $datos->get_sinDatos($sentencia, $parametros);

                    //Consulta para actualizar el material del cliente ***
                    $sentencia = "UPDATE cliente SET antenas = :antenas, routers = :routers,atas = :atas, fecha_baja = :fBaja WHERE dni = :dni ";
                    $parametros = array(":antenas" => $antenaCliente, ":routers" => $routerCliente,":atas"=>$ataCliente, ":dni" => $dniCliente,":fBaja"=> date("Y-m-d H:i:s"));
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

        //Accion en caso de averia
        if ($tipo == 'averia') {

            //Obtenemos el contador del conector  /**/
            $consulta = "SELECT contador FROM material WHERE id_usuario = :usuario and terminado = :terminado AND nombre = :nombre";
            $parametros = array(":usuario"=>$idUsuario,":terminado"=>'No',":nombre"=>'bolsaconectores');
            $datos = new Consulta();
            $datosMaterialConector = $datos->get_conDatosUnica($consulta,$parametros);
            $contadorConector = $datosMaterialConector['contador'];

            //Obtenemos el contador del cable  /**/
            $consulta = "SELECT contador FROM material WHERE id_usuario = :usuario and terminado = :terminado AND nombre = :nombre";
            $parametros = array(":usuario"=>$idUsuario,":terminado"=>'No',":nombre"=>'cajacable');
            $datos = new Consulta();
            $datosMaterialCable = $datos->get_conDatosUnica($consulta,$parametros);
            $contadorCable = $datosMaterialCable['contador'];

            $solucion = false;

            $arrayAveria = [];

            if (isset($_POST['antenas']) and ($_POST['antenas'] == '1')) {
                $cambioAntena = 'Cambio de Antena.';
                $antenasDisponiblesTecnico--;
                $antenasIncidencia = 1;
                array_push($arrayAveria, $cambioAntena);
                $solucion = true;
            }


            if (isset($_POST['routers']) and ($_POST['routers'] == '1')) {
                $cambioRouter = 'Cambio de router';
                $routersDisponiblesTecnico--;
                $routersIncidencia = 1;
                array_push($arrayAveria, $cambioRouter);
                $solucion = true;
            }

            if (isset($_POST['atas']) and ($_POST['atas'] == '1')) {
                $cambioAta = 'Cambio de ata';
                $atasDisponiblesTecnico--;
                $atasIncidencia = 1;
                array_push($arrayAveria, $cambioAta);
                $solucion = true;
            }

            if (isset($_POST['orientacion']) and ($_POST['orientacion'] == '1')) {
                $orientacionAntena = 'Orientacion de Antena';
                array_push($arrayAveria, $orientacionAntena);
                $solucion = true;
            }

            if (isset($_POST['conectores']) and ($_POST['conectores'] == '1')) {
                $cambioConectores = 'Cambio de conectores';
                $contadorConector++;
                array_push($arrayAveria, $cambioConectores);
                $solucion = true;
            }

            if (isset($_POST['cables']) and ($_POST['cables'] == '1')) {
                $cambioCables = 'Cambio de cable';
                $contadorCable++;
                array_push($arrayAveria, $cambioCables);
                $solucion = true;
            }

            if (isset($_POST['comentario']) and ($_POST['comentario'] != '')) {
                $otros = ($_POST['comentario']);
                array_push($arrayAveria, $otros);
                $solucion = true;
            }
            /*Usamos json_encode() para convertir a string y poder guardarlo en la base de datos para volverlo un
            array utilizamos json_decode()*/
            $listaAverias = json_encode($arrayAveria);

            $datos = new Consulta();
            //insertamos la solucion en la base de datos

            if($datosMaterialConector){
                if($datosMaterialCable){
                    if($solucion){
                        try {
                            //Usamos uns transaccion para que en caso de error no ejecute ninguna sentencia.
                            $datos->conexionDB->beginTransaction();
                            $sentencia = "INSERT INTO solucion (id_incidencia, solucion,tecnico) VALUES (:incidencia, :solucion, :tecnico)";
                            $parametros = array(":incidencia" => $asignada, ":solucion" => $listaAverias, ":tecnico" => $idUsuario);
                            $datos->get_sinDatos($sentencia, $parametros);

                            //Consulta para actualizar el estado del tecnico ****
                            $sentencia = "UPDATE usuario SET asignada = :asignada,antenas = :antenas, routers = :routers, atas = :atas WHERE dni = :dni ";
                            $parametros = array(":asignada" => NULL, ":dni" => $idUsuario,":antenas"=>$antenasDisponiblesTecnico,":routers"=>$routersDisponiblesTecnico,":atas"=>$atasDisponiblesTecnico);
                            $datos->get_sinDatos($sentencia, $parametros);

                            //Consulta para actualizar la incidencia
                            $sentencia = "UPDATE incidencia SET estado=:estado, fecha_resolucion= :fechaRes, disponible = NULL, antenas = :antenas, routers = :routers,atas = :atas WHERE id_incidencia = :id ";
                            $parametros = array(":estado" => '3', ":fechaRes" => date("Y-m-d H:i:s"),":antenas" => $antenasIncidencia * (-1), ":routers" => $routersIncidencia * (-1),":atas"=>$atasIncidencia * (-1), ":id" => $asignada);
                            $datos->get_sinDatos($sentencia, $parametros);

                            //consulta para añadir el conector  /**/
                            $sentencia = "UPDATE material SET contador = :contador  WHERE id_usuario = :usuario AND terminado = :terminado AND nombre = :nombre ";
                            $parametros = array(":contador" => $contadorConector, ":usuario" => $idUsuario,":nombre"=>'bolsaconectores',":terminado"=>'No');
                            $datos->get_sinDatos($sentencia, $parametros);

                            //consulta para añadir el cable  /**/
                            $sentencia = "UPDATE material SET contador = :contador  WHERE id_usuario = :usuario AND terminado = :terminado AND nombre = :nombre ";
                            $parametros = array(":contador" => $contadorCable, ":usuario" => $idUsuario,":nombre"=>'cajacable',":terminado"=>'No');
                            $datos->get_sinDatos($sentencia, $parametros);

                            $datos->conexionDB->commit();
                            header("Location: tecnico.php");
                        } catch (PDOException $e) {
                            $datos->conexionDB->rollBack();

                            if($e->getCode() == '23000'){
                                die('Ya existe una solucion para esta incidencia');
                            }

                        } finally {
                            $datos->conexionDB = null;
                        }
                    }else{
                        $mensajeSolucionAveria = 'error';
                    }
                }else{
                    $mensajeFaltaCable = 'error';
                }
            }else{
                $mensajeFaltaConector = 'error';
            }
        }

        //Accion en caso de cambio de domicilio
        if ($tipo == 'cambiodomicilio') {

            //Obtenemos el contador del conector  /**/
            $consulta = "SELECT contador FROM material WHERE id_usuario = :usuario and terminado = :terminado AND nombre = :nombre";
            $parametros = array(":usuario"=>$idUsuario,":terminado"=>'No',":nombre"=>'bolsaconectores');
            $datos = new Consulta();
            $datosMaterialConector = $datos->get_conDatosUnica($consulta,$parametros);
            $contadorConector = $datosMaterialConector['contador'];

            //Obtenemos el contador del cable  /**/
            $consulta = "SELECT contador FROM material WHERE id_usuario = :usuario and terminado = :terminado AND nombre = :nombre";
            $parametros = array(":usuario"=>$idUsuario,":terminado"=>'No',":nombre"=>'cajacable');
            $datos = new Consulta();
            $datosMaterialCable = $datos->get_conDatosUnica($consulta,$parametros);
            $contadorCable = $datosMaterialCable['contador'];

            $router = false;

            $arrayCambiodomicilio = [];

            if (isset($_POST['solucion']) and $_POST['solucion'] != 'otros') {
                $solucion = ($_POST['solucion']);
                array_push($arrayCambiodomicilio, $solucion);
            }
            if (isset($_POST['otros']) and ($_POST['otros'] != '')) {
                $otros = ($_POST['otros']);
                array_push($arrayCambiodomicilio, $otros);
            }
            //Si el checbox esta marcado establecemos el valor de la variable a 1.
            if (isset($_POST['router']) and ($_POST['router'] == '1')) {
                $router = true;
                array_push($arrayCambiodomicilio, 'Router cambiado de domicilio');
            }
            if (isset($_POST['antenaR']) and ($_POST['antenaR'] == '1')) {
                $antenasDisponiblesTecnico++;
                $antenasCliente--;
                $antenaR = 1;
                array_push($arrayCambiodomicilio, 'Retirada de antena');
            }
            if (isset($_POST['antenaI']) and ($_POST['antenaI'] == '1')) {
                $antenasDisponiblesTecnico--;
                $antenasCliente++;
                $antenaI= 1;
                array_push($arrayCambiodomicilio, 'Instalacion Antena');
            }

            if (isset($_POST['conectores']) and ($_POST['conectores'] == '1')) {
                $contadorConector++;
                array_push($arrayCambiodomicilio, 'Conectores nuevos');
            }

            if (isset($_POST['cable']) and ($_POST['cable'] == '1')) {
                $contadorCable++;
                array_push($arrayCambiodomicilio, 'Cable nuevo');
            }

            $listaCambioDomilicio = json_encode($arrayCambiodomicilio);

            //comprobamos si se a recogido el router y se instalo en el nuevo domicilio
            if ($router) {
                if ($antenasCliente >= 0 AND $routersCliente >= 0) {
                    if ($antenasDisponiblesTecnico >= 0 and $routersDisponiblesTecnico >= 0) {

                        try {
                            $datos = new Consulta();
                            //Usamos uns transaccion para que en caso de error no ejecute ninguna sentencia.
                            $datos->conexionDB->beginTransaction();

                            //Consulta para insertar el material a la incidencia, establecer el estado a finalizado y incluir la fecha de resolucion
                            $sentencia = "UPDATE incidencia SET estado=:estado, fecha_resolucion= :fechaRes,disponible = NULL ,antenas = :antenas WHERE id_incidencia = :id ";
                            $parametros = array(":estado" => '3', ":fechaRes" => date("Y-m-d H:i:s"), ":antenas" => $antenaI - $antenaR,":id" => $asignada);
                            $datos->get_sinDatos($sentencia, $parametros);

                            //Consulta para actualizar el material del tecnico y asignarle la instalacion
                            $sentencia = "UPDATE usuario SET asignada = :asignada, antenas = :antenas, routers = :routers WHERE dni = :dni ";
                            $parametros = array(":asignada" => NULL, ":antenas" => $antenasDisponiblesTecnico, ":routers" => $routersDisponiblesTecnico, ":dni" => $idUsuario);
                            $datos->get_sinDatos($sentencia, $parametros);

                            //Consulta para actualizar el material del cliente
                            $sentencia = "UPDATE cliente SET antenas = :antenas, routers = :routers WHERE dni = :dni ";
                            $parametros = array(":antenas" => $antenasCliente, ":routers" => $routersCliente, ":dni" => $dniCliente);
                            $datos->get_sinDatos($sentencia, $parametros);

                            //consulta para insertar la solucion
                            $sentencia = "INSERT INTO solucion (id_incidencia, solucion,tecnico) VALUES (:incidencia, :solucion, :tecnico)";
                            $parametros = array(":incidencia" => $asignada, ":solucion" => $listaCambioDomilicio, ":tecnico" => $idUsuario);
                            $datos->get_sinDatos($sentencia, $parametros);

                            //consulta para añadir el conector  /**/
                            $sentencia = "UPDATE material SET contador = :contador  WHERE id_usuario = :usuario AND terminado = :terminado AND nombre = :nombre ";
                            $parametros = array(":contador" => $contadorConector, ":usuario" => $idUsuario,":nombre"=>'bolsaconectores',":terminado"=>'No');
                            $datos->get_sinDatos($sentencia, $parametros);

                            //consulta para añadir el cable  /**/
                            $sentencia = "UPDATE material SET contador = :contador  WHERE id_usuario = :usuario AND terminado = :terminado AND nombre = :nombre ";
                            $parametros = array(":contador" => $contadorCable, ":usuario" => $idUsuario,":nombre"=>'cajacable',":terminado"=>'No');
                            $datos->get_sinDatos($sentencia, $parametros);

                            $datos->conexionDB->commit();
                            header("Location: ../tecnico/tecnico.php");
                        } catch (PDOException $e) {
                            $datos->conexionDB->rollBack();
                            die('Error: ' . $e->getMessage());
                        } finally {
                            $datos->conexionDB = null;
                        }

                    } else {
                        $mensajeCambioDomicilio = "materialfalta";
                    }

                } else {
                    $mensajeCambioDomicilio = "materialnegativo";
                }

            } else {
                $mensajeCambioDomicilio = 'materialRecoger';
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
            'modo',
            'mensajeFaltaCable',
            'mensajeFaltaConector',
            'mensajeSolucionAveria'
        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}