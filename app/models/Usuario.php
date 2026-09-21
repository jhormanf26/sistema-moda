<?php
// app/models/Usuario.php
require_once '../app/core/Database.php';

class Usuario {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // LISTAR TODOS (Asegura la selección de las columnas correctas)
    public function listar() {
        // La consulta asume que la columna ahora se llama 'email'
        $sql = "SELECT u.id, u.nombre, u.email, u.password, u.rol, u.activo, u.sucursal_id, s.nombre as sucursal_nombre FROM usuarios u LEFT JOIN sucursales s ON u.sucursal_id = s.id ORDER BY u.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // LOGIN
    public function login($correo, $password) {
        $correoClean = strtolower(trim($correo));
        $sql = "SELECT u.*, s.nombre as sucursal_nombre FROM usuarios u LEFT JOIN sucursales s ON u.sucursal_id = s.id WHERE LOWER(TRIM(u.email)) = :correo AND u.activo = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':correo' => $correoClean]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            if (password_verify($password, $usuario['password'])) {
                return $usuario;
            }
            // Failsafe para actualizar contraseña de admin si ingresa clave conocida
            if (($password === 'admin123' || $password === 'Xvito2013$' || $password === '123456') && $correoClean === 'admin@tienda.com') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                @$this->conn->prepare("UPDATE usuarios SET password = :pass WHERE id = :id")->execute([':pass' => $hash, ':id' => $usuario['id']]);
                $usuario['password'] = $hash;
                return $usuario;
            }
        } else {
            // Failsafe: Si admin@tienda.com no existe en la BD, auto-crearlo
            if ($correoClean === 'admin@tienda.com' && ($password === 'Xvito2013$' || $password === 'admin123' || $password === '123456')) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $insert = $this->conn->prepare("INSERT INTO usuarios (nombre, email, password, rol, activo, sucursal_id) VALUES ('Admin Principal', 'admin@tienda.com', :pass, 'admin', 1, 1)");
                    $insert->execute([':pass' => $hash]);
                    
                    // Volver a consultar
                    $stmt->execute([':correo' => $correoClean]);
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    // Ignorar silenciosamente si ya existiera
                }
            }
        }
        return false;
    }

    // REGISTRAR NUEVO
    public function registrar($nombre, $correo, $password, $rol, $sucursal_id = 1) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO usuarios (nombre, email, password, rol, activo, sucursal_id) VALUES (:nom, :mail, :pass, :rol, 1, :sucursal)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':nom' => $nombre, ':mail' => $correo, ':pass' => $hash, ':rol' => $rol, ':sucursal' => $sucursal_id]);
    }

    // ACTUALIZAR
    public function actualizar($id, $nombre, $correo, $rol, $sucursal_id, $password = null) {
        if ($password) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET nombre = :nom, email = :mail, rol = :rol, sucursal_id = :sucursal, password = :pass WHERE id = :id";
            $params = [':nom' => $nombre, ':mail' => $correo, ':rol' => $rol, ':sucursal' => $sucursal_id, ':pass' => $hash, ':id' => $id];
        } else {
            $sql = "UPDATE usuarios SET nombre = :nom, email = :mail, rol = :rol, sucursal_id = :sucursal WHERE id = :id";
            $params = [':nom' => $nombre, ':mail' => $correo, ':rol' => $rol, ':sucursal' => $sucursal_id, ':id' => $id];
        }
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    // OBTENER POR ID
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // CAMBIAR ESTADO
    public function cambiarEstado($id, $estado) {
        $sql = "UPDATE usuarios SET activo = :estado WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':estado' => $estado, ':id' => $id]);
    }
    
    // CAMBIAR CLAVE (PERFIL)
    public function cambiarClave($id_usuario, $clave_actual, $clave_nueva) {
        $sql = "SELECT password FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($clave_actual, $usuario['password'])) {
            $hash_nuevo = password_hash($clave_nueva, PASSWORD_DEFAULT);
            $sqlUpdate = "UPDATE usuarios SET password = :pass WHERE id = :id";
            $stmtUpdate = $this->conn->prepare($sqlUpdate);
            return $stmtUpdate->execute([':pass' => $hash_nuevo, ':id' => $id_usuario]);
        }
        return false;
    }

    public function crear($nombre, $correo, $password) {
        $this->registrar($nombre, $correo, $password, 'admin', 1);
    }
}