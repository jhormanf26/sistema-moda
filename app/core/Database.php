<?php
class Database
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        $this->host = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '127.0.0.1');
        $this->port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3306');
        $this->db_name = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? 'sistema_moda');
        $this->username = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? 'root');
        
        $envPass = getenv('DB_PASS');
        $this->password = ($envPass !== false) ? $envPass : ($_ENV['DB_PASS'] ?? 'root');
    }

    public function getConnection()
    {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";port=" . $this->port . ";dbname=" . $this->db_name;
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->exec("set names utf8");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Verificar si las tablas existen; si no, auto-importar bk_basededatos.sql
            $this->verificarEInicializarTablas();

        } catch (PDOException $exception) {
            // Si la base de datos no existe (error 1049 / Unknown database), crear e importar automáticamente
            if ($exception->getCode() == 1049 || strpos($exception->getMessage(), 'Unknown database') !== false) {
                if ($this->crearEImportarBaseDeDatos()) {
                    return $this->getConnection();
                }
            }
            echo "Error de conexión a la Base de Datos (" . $this->host . ":" . $this->port . "): " . $exception->getMessage();
        }
        return $this->conn;
    }

    private function crearEImportarBaseDeDatos()
    {
        try {
            $dsnNoDb = "mysql:host=" . $this->host . ";port=" . $this->port;
            $pdo = new PDO($dsnNoDb, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $pdo->exec("USE `" . $this->db_name . "`;");
            
            return $this->importarSql($pdo);
        } catch (Exception $e) {
            error_log("[Database] Error intentando crear la base de datos: " . $e->getMessage());
            return false;
        }
    }

    private function verificarEInicializarTablas()
    {
        try {
            $stmt = $this->conn->query("SHOW TABLES LIKE 'usuarios'");
            if ($stmt->rowCount() === 0) {
                $this->importarSql($this->conn);
            }
        } catch (Exception $e) {
            $this->importarSql($this->conn);
        }
    }

    private function importarSql($pdo)
    {
        $sqlFile = dirname(__DIR__, 2) . '/bk_basededatos.sql';
        if (!file_exists($sqlFile)) {
            $sqlFile = dirname(__DIR__) . '/bk_basededatos.sql';
        }

        if (!file_exists($sqlFile)) {
            error_log("[Database] Archivo bk_basededatos.sql no encontrado en " . $sqlFile);
            return false;
        }

        try {
            $sql = file_get_contents($sqlFile);
            $pdo->exec("SET FOREIGN_KEY_CHECKS=0;");
            $pdo->exec($sql);
            $pdo->exec("SET FOREIGN_KEY_CHECKS=1;");
            error_log("[Database] Importación y siembra de base de datos completada exitosamente.");
            return true;
        } catch (Exception $e) {
            error_log("[Database] Error durante la importación del SQL: " . $e->getMessage());
            return false;
        }
    }
}