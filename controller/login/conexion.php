<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
require '../../php/Base.php';
session_start();
$_SESSION = array();
//var_dump($_POST);
//var_dump($_SESSION);

$vista = null;

if(isset($_POST['btnConectar'])){
    $vista = 'conectar';
}

if(isset($_POST['btnCrear'])){
    $vista = 'crear';
}

if(isset($_POST['btnConectarConfirmar'])){
    $servidor = $_POST['servidor'];
    $basededatos = $_POST['basedatos'];
    $usuario = $_POST['usuario'];
    $password = $_POST['clave'];

    $dns = 'mysql:host='.$servidor.'; dbname='.$basededatos;

    $datosConexion = [
        "dns"=>$dns,
        "usuario"=>$usuario,
        "clave"=>$password
    ];

    $conexionJson = json_encode($datosConexion,JSON_UNESCAPED_UNICODE);

    $file = fopen("../../php/parametros.txt", "w");
    fwrite($file, $conexionJson);
    fclose($file);

    header("Location: test.php");

}

if(isset($_POST['btnCrearConfirmar'])){

    $claveValida = false;
    $claveMinima = false;
    $datosPrueba = false;

    //DATOS DE LA BASE DE DATOS
    $servidor = trim($_POST['servidor']);
    $basededatos = trim($_POST['basedatos']);
    $usuario = trim($_POST['usuario']);
    $password = trim($_POST['clave']);

    if (isset($_POST['datosPrueba']) and ($_POST['datosPrueba'] == '1')) {
        $datosPrueba = true;
    }

    //DATOS DEL ADMIN
    $adminDni = trim($_POST['adminDni']);
    $adminUsuario = trim($_POST['adminUsuario']);
    $adminNombre = trim($_POST['adminNombre']);
    $adminTelefono = trim($_POST['adminTelefono']);
    $adminClave = trim($_POST['adminClave']);
    $adminClaveR = trim($_POST['adminClaveR']);

    if(strlen($adminClave) >= 5 and strlen($adminClaveR) >= 5){
        $claveMinima = true;
    }else{
        $errorClaveMinima = 'claveMinima';
    }

    if($adminClave == $adminClaveR){
        $claveValida = true;
    }else{
        $errorClaveValida = 'claveValida';
    }

    //CREAMOS LA BASE DE DATOS NUEVA
    if($claveMinima and $claveValida){
        //CIFRAMOS LA CLAVE CON BCRYP
        $claveCifrada = codificar($adminClave);

        $conexion = new Base($servidor,$usuario,$password);
        $filasAfectadas = $conexion->crearBaseDeDatos($basededatos);
    }

    if(!isset($filasAfectadas) or $filasAfectadas != '1'){
        $error = 'noBaseDatos';
        $vista = 'crear';
    }else{
        $dns = 'mysql:host='.$servidor.'; dbname='.$basededatos;

        $datosConexion = [
            "dns"=>$dns,
            "usuario"=>$usuario,
            "clave"=>$password
        ];
        //CONVERTIMOS EL ARRAY EN JSON PARA GUARDARLO EN EL FICHERO.
        $conexionJson = json_encode($datosConexion,JSON_UNESCAPED_UNICODE);

        //GUARDAMOS LOS DATOS DE CONEXION EN EL FICHERO DE PARAMETROS
        $file = fopen("../../php/parametros.txt", "w");
        fwrite($file, $conexionJson);
        fclose($file);

        //CARGAMOS LAS TABLAS DE LA BASE DE DATOS USANDO LA BASE DE DATOS CREADA.
        $datos = new PDO($dns,$usuario,$password);

        if($datosPrueba){
            $sql = file_get_contents('../../php/BD/bdPrueba.sql');
            $datos->exec($sql);
        }else{
            $sql = file_get_contents('../../php/BD/bdVacia.sql');
            $datos->exec($sql);
        }

        //INSERTAMOS EL ADMIN EN LA BASE DE DATOS
        $insert = new Consulta();
        $cadena = "INSERT INTO usuario(dni,usuario,nombre,telefono,clave,rol) values (:dni,:usuario,:nombre,:telefono,:clave,:rol)";
        $parametros = array(":dni"=>$adminDni,":usuario"=>$adminUsuario,":nombre"=>$adminNombre,":telefono"=>$adminTelefono,":clave"=>$claveCifrada,":rol"=>'0');
        $resultados = $insert->get_sinDatos($cadena,$parametros);

        if($resultados > 0){
            header("Location: test.php");
        }else{
            $error = 'sinAdmin';
        }
    }
}



////////////////////////Renderizado//////////////////////////
require_once '../../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../../views');
$twig = new Twig_Environment($loader, []);

try{
    echo $twig->render('login/conexion.twig', compact(
        'usuario',
        'prueba',
        'servidor',
        'basededatos',
        'usuario',
        'password',
        'vista',
        'error',
        'errorClaveValida',
        'errorClaveMinima',
        'adminDni',
        'adminNombre',
        'adminTelefono',
        'adminUsuario'
    ));
}catch (Exception $e){
    echo  'Excepción: ', $e->getMessage(), "\n";
}