<?php
class Connect
{
    private $driver, $host, $user, $pass, $database, $charset;

    public function __construct()
    {
        require_once __DIR__ . '/../config/database.php';
        $this->driver = DB_DRIVER;
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->pass = DB_PASS;
        $this->database = DB_DATABASE;
        $this->charset = DB_CHARSET;
    }

    /**
     * @return PDO
     * @throws Exception if connection failed
     */
    public function connecting(): PDO
    {
        $connStr = $this->driver . ':host=' . $this->host . ';dbname=' . $this->database . ';charset=' . $this->charset;
        try {
            $connection = new PDO($connStr, $this->user, $this->pass);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $connection;
        } catch (PDOException $e) {
            throw new Exception('Connection failed: ' . $e->getMessage());
        }
    }
}
