<?php
// app/models/Gasto.php
require_once '../app/core/Database.php';

class Gasto {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // 1. Listar gastos (por sesión o filtrado por fechas)
    public function listarFiltrado($id_sesion, $fecha_inicio = null, $fecha_fin = null, $sucursal_id = null) {
        if ($fecha_inicio && $fecha_fin) {
            $sql = "SELECT g.*, u.nombre as usuario_nombre 
                    FROM gastos g
                    JOIN usuarios u ON g.usuario_id = u.id
                    WHERE DATE(g.fecha) BETWEEN :inicio AND :fin ";
            $params = [':inicio' => $fecha_inicio, ':fin' => $fecha_fin];
            
            if ($sucursal_id) {
                $sql .= " AND g.sucursal_id = :sucursal_id ";
                $params[':sucursal_id'] = $sucursal_id;
            }
            $sql .= " ORDER BY g.fecha DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $sql = "SELECT g.*, u.nombre as usuario_nombre 
                    FROM gastos g
                    JOIN usuarios u ON g.usuario_id = u.id
                    WHERE caja_sesion_id = :id ORDER BY g.id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id_sesion]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function listarPorSesion($id_sesion) {
        return $this->listarFiltrado($id_sesion);
    }

    // 2. Registrar nuevo gasto (Ahora recibe usuario_id y sucursal_id)
    public function registrar($id_sesion, $descripcion, $monto, $usuario_id, $sucursal_id = 1) {
        $sql = "INSERT INTO gastos (caja_sesion_id, descripcion, monto, fecha, usuario_id, sucursal_id) 
                VALUES (:id, :desc, :monto, NOW(), :uid, :sid)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':id' => $id_sesion,
            ':desc' => $descripcion,
            ':monto' => $monto,
            ':uid' => $usuario_id,
            ':sid' => $sucursal_id
        ]);
    }

    // ... (totalGastosSesion se mantiene igual) ...
    public function totalGastosFiltrado($id_sesion, $fecha_inicio = null, $fecha_fin = null, $sucursal_id = null) {
        if ($fecha_inicio && $fecha_fin) {
            $sql = "SELECT IFNULL(SUM(monto), 0) FROM gastos WHERE DATE(fecha) BETWEEN :inicio AND :fin";
            $params = [':inicio' => $fecha_inicio, ':fin' => $fecha_fin];
            if ($sucursal_id) {
                $sql .= " AND sucursal_id = :sucursal_id";
                $params[':sucursal_id'] = $sucursal_id;
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchColumn();
        } else {
            $sql = "SELECT IFNULL(SUM(monto), 0) FROM gastos WHERE caja_sesion_id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id_sesion]);
            return $stmt->fetchColumn();
        }
    }

    public function totalGastosSesion($id_sesion) {
        return $this->totalGastosFiltrado($id_sesion);
    }
}