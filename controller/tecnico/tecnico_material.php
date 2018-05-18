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
}else{
    $rol = $_SESSION['rol'];
    $retirar = null;
    $depositar = null;
    $usuario  = $_SESSION['usuario'];
    $datos = new Consulta();
    $idUsuario = $datos->get_id();

    $consulta = "SELECT antenas,routers,limite FROM usuario WHERE dni = :dni";
    $parametros = array(":dni"=>$idUsuario);
    $datos = new Consulta();
    $materialTecnico = $datos->get_conDatosUnica($consulta,$parametros);
    $antenasDisponiblesTecnico = $materialTecnico['antenas'];
    $routersDisponiblesTecnico = $materialTecnico['routers'];
    $limite = $materialTecnico['limite'];

    $datos = new Consulta();
    $materialAlmacen = $datos->get_stock();
    $antenasDisponiblesAlmacen = $materialAlmacen['antenas'];
    $routersDisponiblesAlmacen = $materialAlmacen['routers'];

    if(isset($_POST['retirarMaterial'])){
        $retirar = "Si";
        $depositar = "No";
    }


    if(isset($_POST['depositarMaterial'])){
        $retirar = "No";
        $depositar = "Si";
    }
    //Accion al pulsar el boton de aceptar (retirar)
    if(isset($_POST['aceptarRetirar'])){

        $mensajeAntenas = null;
        $mensajeRouters = null;
        $mensajeResultado = null;
        $mensajeValores = null;

        $antenas = false;
        $routers = false;
        $positivos = false;
        $sinNegativos = false;

        $cantidadAntenas = $_POST['numeroAntenas'];
        $cantidadRouters = $_POST['numeroRouters'];
        $cantidadAtas = $_POST['numeroAtas'];
        $conectores = $_POST['numeroConectores']; /**/
        $cable = $_POST['numeroCables']; /**/

        //Actualizamos el stock por si otro tecnico retiro o deposito material
        $datos = new Consulta();
        $materialAlmacen = $datos->get_stock();
        $antenasDisponiblesAlmacen = $materialAlmacen['antenas'];
        $routersDisponiblesAlmacen = $materialAlmacen['routers'];

        //Comprobamos que ningun campo tenga valores negativos
        if($cantidadAntenas < 0 OR $cantidadRouters < 0){
            $mensajeValores = 'error';
        }else{
            $sinNegativos = true;
        }

        //Comprobamos que al menos algun campo tenga valores positivos
        if ($cantidadAntenas == 0 AND $cantidadRouters == 0 AND $cantidadAtas == 0 AND $cable == 0 AND $conectores == 0 ) {
            $mensajeValoresCero = 'error';
        }else{
            $positivos = true;
        }

        //Comprobamos que la cantidad solicitada no sea mayor a la disponible
        if($antenasDisponiblesAlmacen >= $cantidadAntenas){
            $antenas = true;
            $mensajeAntenas = 'Ok';
        }else{
            $mensajeAntenas = 'error';
        }

        if($routersDisponiblesAlmacen >= $cantidadRouters){
            $routers = true;
            $mensajeRouters= 'Ok';
        }else{
            $mensajeRouters= 'error';
        }

        if($cable == '1'){  /**/

            $datos = new Consulta();

            try {
                //Usamos una transacción para que en caso de error no ejecute ninguna sentencia.
                $datos->conexionDB->beginTransaction();

                $consulta = "UPDATE material SET material.terminado = :terminadoS WHERE material.terminado = :terminadoC AND material.nombre = :material and material.id_usuario = :usuario";
                $parametros = array(":terminadoS"=>'Si',":terminadoC"=>'No',":material"=>'cajacable',":usuario"=>$idUsuario);
                $materialTecnico = $datos->get_sinDatos($consulta,$parametros);

                $consulta = "INSERT INTO material (id_usuario, nombre) VALUES (:id_usuario, :nombre)";
                $parametros = array(":id_usuario"=>$idUsuario,":nombre"=>'cajacable');
                $resultado = $datos->get_sinDatos($consulta,$parametros);

                $positivos = true;

                $datos->conexionDB->commit();

            } catch (PDOException $e) {
                $datos->conexionDB->rollBack();
                die('Error: ' . $e->getMessage());
            } finally {
                $datos->conexionDB = null;
            }

        }

        if($conectores == '1'){  /**/

            $datos = new Consulta();

            try {
                //Usamos una transacción para que en caso de error no ejecute ninguna sentencia.
                $datos->conexionDB->beginTransaction();

                $consulta = "UPDATE material SET material.terminado = :terminadoS WHERE material.terminado = :terminadoC AND material.nombre = :material and material.id_usuario = :usuario";
                $parametros = array(":terminadoS"=>'Si',":terminadoC"=>'No',":material"=>'bolsaconectores',":usuario"=>$idUsuario);
                $materialTecnico = $datos->get_sinDatos($consulta,$parametros);

                $consulta = "INSERT INTO material (id_usuario, nombre) VALUES (:id_usuario, :nombre)";
                $parametros = array(":id_usuario"=>$idUsuario,":nombre"=>'bolsaconectores');
                $resultado = $datos->get_sinDatos($consulta,$parametros);

                $positivos = true;

                $datos->conexionDB->commit();

            } catch (PDOException $e) {
                $datos->conexionDB->rollBack();
                die('Error: ' . $e->getMessage());
            } finally {
                $datos->conexionDB = null;
            }

        }



        //Si las cantidades estan disponibles se realiza la operacion de lo contrario dara un error

        if($cantidadRouters > '0' OR $cantidadAntenas > '0'){
            if($routers AND $antenas AND $positivos AND $sinNegativos){
                $antenasRestantes = $antenasDisponiblesAlmacen - $cantidadAntenas;
                $routersRestantes = $routersDisponiblesAlmacen - $cantidadRouters;

                $consulta = "INSERT INTO stock (antenas,routers,ultimousuario,antenasM,routersM) VALUES (:antenas, :routers, :ultimo, :antenasM, :routersM)";
                $parametros = array(":antenas"=>$antenasRestantes,":routers"=>$routersRestantes,":ultimo"=>$idUsuario,":antenasM"=>$cantidadAntenas * (-1),":routersM"=>$cantidadRouters * (-1));
                $datos = new Consulta();
                $resultado = $datos->get_sinDatos($consulta,$parametros);

                $antenasUsuarioActualizado = $antenasDisponiblesTecnico + $cantidadAntenas;
                $routersUsuarioActualizado = $routersDisponiblesTecnico + $cantidadRouters;

                $consulta = "UPDATE usuario SET antenas= :antenas, routers= :routers WHERE dni= :dni";
                $parametros = array(":antenas"=>$antenasUsuarioActualizado,":routers"=>$routersUsuarioActualizado,":dni"=>$idUsuario);
                $datos = new Consulta();
                $resultadoUsuario = $datos->get_sinDatos($consulta,$parametros);

                if($resultado > 0){
                    $mensajeResultado = 'Ok';
                    header("Location: tecnico_material.php");
                }else{
                    $mensajeResultado = 'error';
                }

                //Comprobamos que al menos algun campo tenga valores positivos
                if ($cantidadAntenas == 0 AND $cantidadRouters == 0 ) {
                    $mensajeValoresCero = 'error';
                }else{
                    $positivos = true;
                }

                //Comprobamos que la cantidad solicitada no sea mayor a la disponible
                if($cantidadAntenas <= $antenasDisponiblesTecnico ){
                    $antenas = true;
                    $mensajeAntenas = 'Ok';
                }else{
                    $mensajeAntenas = 'error';
                }

                if($cantidadRouters <= $routersDisponiblesTecnico){
                    $routers = true;
                    $mensajeRouters= 'Ok';
                }else{
                    $mensajeRouters= 'error';
                }
            }
        }
    }

    //Accion al pulsar el boton de aceptar (depositar)
    if(isset($_POST['aceptarDepositar'])){

        $mensajeAntenas = null;
        $mensajeRouters = null;
        $mensajeResultado = null;
        $mensajeValores = null;

        $antenas = false;
        $routers = false;
        $positivos = false;
        $sinNegativos = false;

        $cantidadAntenas = $_POST['numeroAntenas'];
        $cantidadRouters = $_POST['numeroRouters'];

        //Actualizamos el stock por si otro tecnico retiro o deposito material
        $datos = new Consulta();
        $materialAlmacen = $datos->get_stock();
        $antenasDisponiblesAlmacen = $materialAlmacen['antenas'];
        $routersDisponiblesAlmacen = $materialAlmacen['routers'];

        //Comprobamos que ningun campo tenga valores negativos
        if($cantidadAntenas < 0 OR $cantidadRouters < 0){
            $mensajeValores = 'error';
        }else{
            $sinNegativos = true;
        }

        //Comprobamos que al menos algun campo tenga valores positivos
        if ($cantidadAntenas == 0 AND $cantidadRouters == 0 ) {
            $mensajeValoresCero = 'error';
        }else{
            $positivos = true;
        }

        //Comprobamos que la cantidad solicitada no sea mayor a la disponible
        if($cantidadAntenas <= $antenasDisponiblesTecnico ){
            $antenas = true;
            $mensajeAntenas = 'Ok';
        }else{
            $mensajeAntenas = 'error';
        }

        if($cantidadRouters <= $routersDisponiblesTecnico){
            $routers = true;
            $mensajeRouters= 'Ok';
        }else{
            $mensajeRouters= 'error';
        }

        //Si las cantidades estan disponibles se realiza la operacion de lo contrario dara un error
        if($routers AND $antenas AND $positivos AND $sinNegativos){
            $antenasRestantes = $antenasDisponiblesAlmacen + $cantidadAntenas;
            $routersRestantes = $routersDisponiblesAlmacen + $cantidadRouters;

            $consulta = "INSERT INTO stock (antenas,routers,ultimousuario,antenasM,routersM) VALUES (:antenas, :routers, :ultimo, :antenasM, :routersM)";
            $parametros = array(":antenas"=>$antenasRestantes,":routers"=>$routersRestantes,":ultimo"=>$idUsuario,":antenasM"=>$cantidadAntenas ,":routersM"=>$cantidadRouters);
            $datos = new Consulta();
            $resultado = $datos->get_sinDatos($consulta,$parametros);

            $antenasUsuarioActualizado = $antenasDisponiblesTecnico - $cantidadAntenas;
            $routersUsuarioActualizado = $routersDisponiblesTecnico - $cantidadRouters;

            $consulta = "UPDATE usuario SET antenas= :antenas, routers= :routers WHERE dni= :dni";
            $parametros = array(":antenas"=>$antenasUsuarioActualizado,":routers"=>$routersUsuarioActualizado,":dni"=>$idUsuario);
            $datos = new Consulta();
            $resultadoUsuario = $datos->get_sinDatos($consulta,$parametros);

            if($resultado > 0){
                $mensajeResultado = 'Ok';
                header("Location: tecnico_material.php");
            }else{
                $mensajeResultado = 'error';
            }
        }
    }

    ////////////////////////Renderizado//////////////////////////
    require_once '../../vendor/autoload.php';
    $loader = new Twig_Loader_Filesystem('../../views');
    $twig = new Twig_Environment($loader, []);

    try{
        echo $twig->render('tecnico/tecnico_material.twig', compact(
            'materialAlmacen',
            'materialTecnico',
            'mensajeResultado',
            'mensajeRouters',
            'mensajeAntenas',
            'mensajeValores',
            'mensajeValoresCero',
            'depositar',
            'retirar',
            'limite',
            'rol',
            'usuario'

        ));
    }catch (Exception $e){
        echo  'Excepción: ', $e->getMessage(), "\n";
    }
}