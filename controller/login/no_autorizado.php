<?php
require '../../php/Consulta.php';
session_start();

////////////////////////Renderizado//////////////////////////
require_once '../../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../../views');
$twig = new Twig_Environment($loader, []);

try{
    echo $twig->render('login/no_autorizado.twig', compact(
        ''
    ));
}catch (Exception $e){
    echo  'Excepción: ', $e->getMessage(), "\n";
}