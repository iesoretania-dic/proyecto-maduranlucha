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

        $sql= "INSERT INTO conexiones (usuario,tipo) VALUES (:usuario,:tipo)";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$this->get_id(),":tipo"=>'conexion'));
        $sentencia->closeCursor();
    }

    //Metodo que inserta un registros en la tabla de conexion
    public function desconexion(){

        $sql= "INSERT INTO conexiones (usuario,tipo) VALUES (:usuario,:tipo)";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$this->get_id(),":tipo"=>'desconexion'));
        $sentencia->closeCursor();
    }

    //Metodo para la clave del usuario
    public function get_clave($usuario){

        $sql= "SELECT clave FROM usuario WHERE usuario = :usuario";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$usuario));
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado['clave'];
    }

    //Metodo que obtiene el hash de la clave del usuario
    public function get_hash(){
        $sql= "SELECT clave FROM usuario where dni = :dni";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":dni"=>$this->get_id()));
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado['clave'];
    }

    //Metodo que obtiene el nombre del usuario
    public function get_nombre(){
        $sql= "SELECT nombre FROM usuario where dni = :dni";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":dni"=>$this->get_id()));
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado['nombre'];
    }

    //Metodo que comprueba si existe el nombre de usuario
    public function comprobarUsuarioExiste($usuario){

        $sql= "SELECT usuario FROM usuario WHERE usuario = :usuario";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":usuario"=>$usuario));
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado['usuario'];
    }

    public function get_stock(){

        $sql= "SELECT * FROM stock ORDER BY fecha DESC LIMIT 1";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute();
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado;
    }

    public function get_nombreCliente($dni){
        $sql= "SELECT nombre FROM cliente where dni = :dni";
        $sentencia=$this->conexionDB->prepare($sql);
        $sentencia->execute(array(":dni"=>$dni));
        $resultado = $sentencia->fetch(PDO::FETCH_ASSOC);
        $sentencia->closeCursor();
        return $resultado['nombre'];
    }


}

