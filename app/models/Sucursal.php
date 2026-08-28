<?php
require_once '../app/core/Database.php';

class Sucursal {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function listar() {
        $sql = "SELECT * FROM sucursales ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarActivas() {
        $sql = "SELECT * FROM sucursales WHERE activo = 1 ORDER BY id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($datos) {
        $sql = "INSERT INTO sucursales (nombre, direccion, telefono, activo) 
                VALUES (:nom, :dir, :tel, :act)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nom' => $datos['nombre'],
            ':dir' => $datos['direccion'],
            ':tel' => $datos['telefono'],
            ':act' => $datos['activo'] ?? 1
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE sucursales SET nombre=:nom, direccion=:dir, telefono=:tel, activo=:act 
                WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nom' => $datos['nombre'],
            ':dir' => $datos['direccion'],
            ':tel' => $datos['telefono'],
            ':act' => $datos['activo'] ?? 1,
            ':id' => $id
        ]);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM sucursales WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
