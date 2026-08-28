<?php
require_once '../app/core/Database.php';

class Proveedor {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function listar() {
        $sql = "SELECT * FROM proveedores ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($datos) {
        $sql = "INSERT INTO proveedores (ruc, razon_social, telefono, correo, direccion) 
                VALUES (:ruc, :rs, :tel, :mail, :dir)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':ruc' => $datos['ruc'],
            ':rs' => $datos['razon_social'],
            ':tel' => $datos['telefono'],
            ':mail' => $datos['correo'],
            ':dir' => $datos['direccion']
        ]);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM proveedores WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE proveedores SET ruc=:ruc, razon_social=:rs, telefono=:tel, correo=:mail, direccion=:dir WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':ruc' => $datos['ruc'],
            ':rs' => $datos['razon_social'],
            ':tel' => $datos['telefono'],
            ':mail' => $datos['correo'],
            ':dir' => $datos['direccion'],
            ':id' => $id
        ]);
    }
}