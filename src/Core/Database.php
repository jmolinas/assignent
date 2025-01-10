<?php
class Database
{
    private static $instance = null;
    private $pdo;

    private function __construct($host, $db, $user, $password)
    {
        $dsn = "pgsql:host=$host;dbname=$db";
        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public static function getInstance($host, $db, $user, $password)
    {
        if (self::$instance === null) {
            self::$instance = new Database($host, $db, $user, $password);
        }
        return self::$instance;
    }

    public function getPDO()
    {
        return $this->pdo;
    }
}
