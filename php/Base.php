<?php

class Base
{

    public $conexion;

    public function __construct($host,$username,$password){

        try{
            $this->conexion = new mysqli($host,$username,$password);
            return $this->conexion;
        }catch(Exception $e){
            die('Error: ' . $e->getLine());

        }

    }


    public function crearBaseDeDatos ($nombre){

        $sentencia = "CREATE DATABASE ".$nombre." CHARACTER SET utf8 COLLATE utf8_unicode_ci";

        try{
            mysqli_query($this->conexion, $sentencia);
            mysqli_set_charset($this->conexion,"utf8_unicode_ci");
        }catch(Exception $e){
            die('Error: ' . $e->getLine());

        }finally{
            return $this->conexion->affected_rows;
        }
    }
}