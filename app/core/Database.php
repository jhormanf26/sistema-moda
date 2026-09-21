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
        $this->port = getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? '3307');
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
        } catch (PDOException $exception) {
            echo "Error de conexión: " . $exception->getMessage();
        }
        return $this->conn;
    }
}