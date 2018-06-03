<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();
$_SESSION = array();
//var_dump($_POST);
//var_dump($_SESSION);


//$file = fopen("../../php/parametros.txt", "r");
//$datosFile = fgets($file);
//$datosConexion = json_decode($datosFile, $assoc = true);



if(isset($_POST['enviar'])){
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

    $conexionJson = json_encode($datosConexion);

    $file = fopen("../../php/parametros.txt", "w");
    fwrite($file, $conexionJson);
    fclose($file);

    header("Location: test.php");

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
        'password'
    ));
}catch (Exception $e){
    echo  'Excepción: ', $e->getMessage(), "\n";
}