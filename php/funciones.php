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
//Funcion que nos permite enviar correos
function enviarCorreo($tipo,$nombreUsuario,$nombreCliente,$comentario){
    try{
        $phpmailer = new PHPMailer();
        $phpmailer->Username = ""; //Correo de origen
        $phpmailer->Password = ""; //Contraseña del correo
        $phpmailer->IsSMTP();
        $phpmailer->Host = ""; //servidor de correo
        $phpmailer->Port = 25; //puerto SMTP
        $phpmailer->SMTPAuth = true;
        $phpmailer->From = ""; //Correo de origen
        $phpmailer->FromName = "Nueva incidencia";
        $phpmailer->AddAddress(""); //Email destino
        $phpmailer->Subject = "Nueva incidencia"; //Asunto del correo
        $phpmailer->Body .="<h1 style='color:#3498db;'>Nuev@ $tipo</h1>";
        $phpmailer->Body .= "<p>Comercial: $nombreUsuario</p>";
        $phpmailer->Body .= "<p>Cliente: $nombreCliente</p>";
        $phpmailer->Body .= "<p>Comenatario del comercial: $comentario</p>";
        $phpmailer->Body .= "<p>Fecha de creacion: ".date("d/m/Y h:i:s")."</p>";
        $phpmailer->IsHTML(true);
        if(!$phpmailer->Send()) {
            return "Error al enviar: " . $phpmailer->ErrorInfo;
        } else {
            return "Enviado..";
        }
    }catch (Exception $e){
        die('Error: ' . $e->getMessage());
    }
}

function comprobarSesion(){
    if(!isset($_SESSION['usuario'])){
        header('Location: ../../index.php');
    }else{
        $limiteTiempo = 1800; //Establecemos el tiempo que dura la sesion
        $tiempo = time();
        $_SESSION['sesionFinalziada'] = 'tiempoSesion';
        $tiempoSesion = $tiempo - $_SESSION['tiempoSesion'];
        if($tiempoSesion > $limiteTiempo){
            $datos = new Consulta();
            $datos->desconexion();
            header('Location: ../../index.php');
        }
    }
}

