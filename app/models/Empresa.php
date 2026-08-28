<?php
require_once '../app/core/Database.php';

class Empresa
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // OBTENER DATOS (Siempre ID 1)
    public function obtener()
    {
        $sql = "SELECT * FROM empresa WHERE id = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ACTUALIZAR DATOS
    public function actualizar($datos)
    {
        $actual = $this->obtener();
        $logo = isset($datos['logo']) && !empty($datos['logo']) ? $datos['logo'] : $actual['logo'];

        $sql = "UPDATE empresa SET 
                nombre = :nom, 
                ruc = :ruc, 
                direccion = :dir, 
                telefono = :tel, 
                email = :mail, 
                mensaje_ticket = :msg,
                moneda = :moneda,
                logo = :logo
                WHERE id = 1";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nom' => $datos['nombre'],
            ':ruc' => $datos['ruc'],
            ':dir' => $datos['direccion'],
            ':tel' => $datos['telefono'],
            ':mail' => $datos['email'],
            ':msg' => $datos['mensaje'],
            ':moneda' => $datos['moneda'],
            ':logo' => $logo
        ]);
    }
}