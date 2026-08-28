<?php
// app/models/Caja.php
require_once '../app/core/Database.php';

class Caja {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // 1. VERIFICAR SI HAY CAJA ABIERTA
    public function obtenerCajaAbierta($usuario_id, $sucursal_id = 1) {
        $sql = "SELECT * FROM caja_sesiones WHERE usuario_id = :uid AND sucursal_id = :sid AND estado = 'abierta'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $usuario_id, ':sid' => $sucursal_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 2. ABRIR CAJA
    public function abrir($usuario_id, $monto, $sucursal_id = 1) {
        $sql = "INSERT INTO caja_sesiones (usuario_id, monto_apertura, fecha_apertura, estado, sucursal_id) 
                VALUES (:uid, :monto, NOW(), 'abierta', :sid)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':uid' => $usuario_id, ':monto' => $monto, ':sid' => $sucursal_id]);
    }

    // 3. CALCULAR VENTAS DE LA SESIÓN
    public function calcularVentasSesion($usuario_id, $fecha_apertura, $sucursal_id = 1) {
        $sql = "SELECT IFNULL(SUM(total), 0) FROM ventas 
                WHERE usuario_id = :uid 
                AND sucursal_id = :sid
                AND estado = 'completada' 
                AND fecha >= :f_apertura";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $usuario_id, ':sid' => $sucursal_id, ':f_apertura' => $fecha_apertura]);
        return $stmt->fetchColumn();
    }

    // 4. CERRAR CAJA (ARQUEO CON GASTOS)
    public function cerrar($id_sesion, $monto_fisico, $total_ventas, $total_gastos) {
        
        // Obtener monto apertura original
        $sqlInfo = "SELECT monto_apertura FROM caja_sesiones WHERE id = :id";
        $stmtInfo = $this->conn->prepare($sqlInfo);
        $stmtInfo->execute([':id' => $id_sesion]);
        $monto_apertura = $stmtInfo->fetchColumn();

        // FÓRMULA: (Base + Ventas) - Gastos
        $total_teorico = ($monto_apertura + $total_ventas) - $total_gastos;
        
        // Diferencia: Positiva (Sobra dinero), Negativa (Falta dinero), Cero (Cuadra)
        $diferencia = $monto_fisico - $total_teorico;

        $sql = "UPDATE caja_sesiones SET 
                monto_cierre = :fisico, 
                total_ventas_sistema = :ventas, 
                diferencia = :dif,
                fecha_cierre = NOW(), 
                estado = 'cerrada' 
                WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':fisico' => $monto_fisico,
            ':ventas' => $total_ventas,
            ':dif' => $diferencia,
            ':id' => $id_sesion
        ]);
    }
}