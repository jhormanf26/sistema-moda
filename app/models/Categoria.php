<?php
// app/models/Categoria.php
require_once '../app/core/Database.php';

class Categoria {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function listarActivas() {
        $sql = "SELECT * FROM categorias WHERE activo = 1 ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarTodas() {
        $sql = "SELECT id, nombre, descripcion, activo,
                (SELECT COUNT(*) FROM productos WHERE categoria_id = categorias.id AND activo = 1) as total_productos 
                FROM categorias ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM categorias WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function registrar($datos) {
        $sql = "INSERT INTO categorias (nombre, descripcion, activo) VALUES (:nombre, :descripcion, 1)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'] ?? null
        ]);
    }

    public function actualizar($id, $datos) {
        $sql = "UPDATE categorias SET nombre = :nombre, descripcion = :descripcion WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':nombre' => $datos['nombre'],
            ':descripcion' => $datos['descripcion'] ?? null,
            ':id' => $id
        ]);
    }

    public function cambiarEstado($id, $nuevo_estado) {
        $sql = "UPDATE categorias SET activo = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':estado' => $nuevo_estado,
            ':id' => $id
        ]);
    }
}
