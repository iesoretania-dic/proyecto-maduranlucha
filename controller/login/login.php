<?php
require '../../php/Consulta.php';
require '../../php/funciones.php';
session_start();
$_SESSION = array();


////////////////////////Renderizado//////////////////////////
require_once '../../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../../views');
$twig = new Twig_Environment($loader, []);

try{
    echo $twig->render('login/login.twig', compact(
        'usuario',
        'prueba'
    ));
}catch (Exception $e){
    echo  'Excepción: ', $e->getMessage(), "\n";
}
