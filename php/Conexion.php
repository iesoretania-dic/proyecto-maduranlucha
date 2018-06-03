<?php
class Conexion{

    public $conexionDB;

    public function __construct(){

        $file = fopen("../../php/parametros.txt", "r");
        $datosFile = fgets($file);
        $datosConexion = json_decode($datosFile, $assoc = true);

        $dns = $datosConexion['dns'];
        $usarname = $datosConexion['usuario'];
        $password = $datosConexion['clave'];

        try{
            $this->conexionDB = new PDO($dns,$usarname,$password,array(PDO::ATTR_PERSISTENT => true));
            $this->conexionDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conexionDB->exec("SET CHARACTER SET utf8");
            return $this->conexionDB;

        }catch(Exception $e){
//            die('Error: ' . $e->getLine());

        }
    }
}