<?php

// Check if Database class already exists
if (class_exists('Database')) {
    return;
}

// Load environment variables using vlucas/phpdotenv
require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

// Create Database class
class Database
{
    // Properties
    public $conn;

    // Constructor
    function __construct()
    {
        $this->open_db_connection();
    }

    // Methods
    public function open_db_connection()
    {
        $SERVERNAME = $_ENV['DB_SERVERNAME'] ?? null;
        $DBNAME = $_ENV['DB_NAME'] ?? null;
        $DBUSER = $_ENV['DB_USER'] ?? null;
        $DBPASS = $_ENV['DB_PASS'] ?? null;

        // Debug: Print connection details (remove in production)
        error_log("Connecting to database: $SERVERNAME, $DBNAME, $DBUSER");

        if (!$SERVERNAME || !$DBNAME || !$DBUSER || !$DBPASS) {
            die("Database configuration is incomplete. Please check your .env file.");
        }

        $this->conn = new mysqli($SERVERNAME, $DBUSER, $DBPASS, $DBNAME);

        if ($this->conn->connect_errno) {
            error_log("Database connection failed: " . $this->conn->connect_error);
            die("Database connection failed: " . $this->conn->connect_error);
        }

        // Debug: Print connection success (remove in production)
        error_log("Database connection successful");
    }

    // close
    public function close_db_connection()
    {
        $this->conn->close();
    }

    // read
    public function read($sql)
    {
        $result = $this->conn->query($sql);
        return $result;
    }

    // prepared
    public function prepared($sql, $types, $params)
    {

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt;
    }

    public function prePareNoBind($sql)
    {

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }


    // fetch_array
    public function fetch_array($result)
    {
        $returnV = array();
        while ($row = $result->fetch_assoc()) {
            $returnV[] = $row;
        }

        return $returnV;
    }
}
