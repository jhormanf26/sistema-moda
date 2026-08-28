<?php
require_once '../app/core/Database.php';

class Cliente {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function listarFiltrado($termino = '') {
        if (!empty($termino)) {
            $sql = "SELECT * FROM clientes WHERE nombre LIKE :t OR documento LIKE :t ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':t' => "%$termino%"]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            return $this->listar();
        }
    }

    public function listar() {
        $sql = "SELECT * FROM clientes ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function registrar($datos) {
        $sql = "INSERT INTO clientes (nombre, documento, telefono, correo, direccion) 
                VALUES (:nom, :doc, :tel, :mail, :dir)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nom' => $datos['nombre'],
            ':doc' => $datos['documento'],
            ':tel' => $datos['telefono'],
            ':mail' => $datos['correo'],
            ':dir' => $datos['direccion']
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE clientes SET nombre=:nom, documento=:doc, telefono=:tel, correo=:mail, direccion=:dir 
                WHERE id=:id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nom' => $datos['nombre'],
            ':doc' => $datos['documento'],
            ':tel' => $datos['telefono'],
            ':mail' => $datos['correo'],
            ':dir' => $datos['direccion'],
            ':id' => $id
        ]);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM clientes WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Buscar cliente por DNI/Nombre para el TPV
    public function buscar($termino) {
        $sql = "SELECT * FROM clientes WHERE (nombre LIKE :t OR documento LIKE :t) AND activo = 1 LIMIT 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':t' => "%$termino%"]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}