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

    //Metodo que nos devuelve el id del usuario actual
    public function get_id(){

        $usuario = $_SESSION['usuario'];
        $sql= "SELECT dni FROM usuario WHERE usuario = :usuario";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$usuario));
        $resultado=$sentencia->fetch(PDO::FETCH_ASSOC);
        $idUsuario = $resultado['dni'];
        $sentencia->closeCursor();
        return $idUsuario;
    }
    //Metodo que nos devuelve el rol del usuario actual
    public function get_rol(){

        $sql= "SELECT rol FROM usuario WHERE dni = :dni";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":dni"=>$this->get_id()));
        $resultado=$sentencia->fetch(PDO::FETCH_ASSOC);
        $rol = $resultado['rol'];
        $sentencia->closeCursor();
        return $rol;
    }

    //Metodo para insertar un registro en la tabla de noautorizados
    public function set_noautorizado(){

        $sql= "INSERT INTO noautorizados (usuario) VALUES (:usuario)";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$this->get_id()));
        $sentencia->closeCursor();
    }

    //Metodo que comprueba si un usuario y clave son correctos en la base de datos
    public function comprobarUsuario($usuario,$clave){

        $sql= "SELECT dni FROM usuario WHERE usuario = :usuario AND clave = :clave";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$usuario,":clave"=>$clave));
        $filasAfectadas = $sentencia->rowCount();
        $sentencia->closeCursor();
        return $filasAfectadas;
    }

    //Metodo que inserta un registros en la tabla de conexion
    public function conexion(){

        $sql= "INSERT INTO conexiones (usuario) VALUES (:usuario)";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$this->get_id()));
        $sentencia->closeCursor();
    }


    //Metodo para obtener el hash de la clave del usuario
    public function get_clave($usuario){

        $sql= "SELECT clave FROM usuario WHERE usuario = :usuario";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$usuario));
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado['clave'];
    }


}

