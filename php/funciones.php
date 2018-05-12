<?php

//Funcion para validar un DNI de 8 digitos y una letra
function validar_dni($dni){
    $letra = substr($dni, -1);
    $numeros = substr($dni, 0, -1);
    if ( substr("TRWAGMYFPDXBNJZSQVHLCKE", $numeros%23, 1) == $letra && strlen($letra) == 1 && strlen ($numeros) == 8 ){
        echo 'valido';
    }else{
        echo 'no valido';
    }
}

//Funcion para cifrar una contraseña con bcrypt
function codificar($clave){

    $opciones = ['cost' => 12];
    $claveCifrada = password_hash($clave,PASSWORD_BCRYPT, $opciones);
    return $claveCifrada;

}

//Funcion para comprobar una clave y un hash
function comprobarClave($clave,$hash){

    $resultado = password_verify($clave,$hash);
    return $resultado;
}

//Funcion para restar dos fechas nos devuelve el resultado en segundos
function restarfechas($inicio, $fin){

    $fechauno = strtotime($inicio);
    $fechados = strtotime($fin);
    return $resultado = $fechados - $fechauno;

}

