<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();

if(isset($_SESSION['login']) and $_SESSION['login'] == 'error'){
    $login = 'error';
}

if(isset($_SESSION['sesionFinalziada']) and $_SESSION['sesionFinalziada'] = 'tiempoSesion'){
    $login = 'tiempo';
}

$_SESSION = array();

////////////////////////Renderizado//////////////////////////
require_once '../../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../../views');
$twig = new Twig_Environment($loader, []);

try{
    echo $twig->render('login/login.twig', compact(
        'usuario',
        'prueba',
        'login'
    ));
}catch (Exception $e){
    echo  'Excepción: ', $e->getMessage(), "\n";
}
