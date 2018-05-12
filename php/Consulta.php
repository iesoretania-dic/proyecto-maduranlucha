<?php

require "Conexion.php";

class Consulta extends Conexion{

    public function __DevolverDatos(){

        parent::__construct();

    }
    //Metodo que nos devuelve un array asociativo de filas de una consulta SELECT, se utiliza cuando esperamos que nos devuelva mas de una fila como resultado
    public function get_conDatos($sql,$parametros){

        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute($parametros);
        $resultado=$sentencia->fetchAll(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado;
    }
    //Metodo que nos devuelve la primera fila array asociativo de la consulta SELECT, se utiliza cuando esperamos que solo devuelva una fila.
    public function get_conDatosUnica($sql,$parametros){

        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute($parametros);
        $resultado=$sentencia->fetch(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado;
    }
    //Metodo que nos devuelve el numero de filas afectada de un INSERT, DELETE O UPDATE
    public function get_sinDatos($sql,$parametros){

        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute($parametros);
        $filasAfectadas = $sentencia->rowCount();
        $sentencia->closeCursor();
        return $filasAfectadas;
    }


}

