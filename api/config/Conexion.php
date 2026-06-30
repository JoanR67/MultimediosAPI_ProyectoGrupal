<?php

class Conexion
{

    private $host = "localhost";
    private $dbname = "multimedios_tickets";
    private $user = "root";
    private $password = "";

    public function Conectar()
    {
        try {
            //code...
            $conexion = new PDO(
                "mysql:host={$this->host};dbname={$this->dbname};charset=utf8mb4",
                $this->user,
                $this->password
            );

            $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $conexion;
        } catch (PDOException $th) {
            die($th->getMessage());
        }
    }
}
