<?php

class Conexion{

    public $conexionDB;

    public function __construct($servidor = 'localhost',$basededatos='gestion_incidencias' ,$usuario = 'root',$clave = ''){

        $dns = 'mysql:host='.$servidor.'; dbname='.$basededatos;
        $usarname = $usuario;
        $password = $clave;

        try{
            $this->conexionDB = new PDO($dns,$usarname,$password,array(PDO::ATTR_PERSISTENT => true));
            $this->conexionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexionDB->exec("SET CHARACTER SET utf8");
            return $this->conexionDB;

        }catch(Exception $e){
            die('Error: ' . $e->getCode());
        }
    }
}