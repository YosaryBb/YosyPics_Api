<?php

namespace connection;

require_once dirname(__DIR__, 1) . '/config/database.php';

use mysqli;

class Connection
{
    protected $db_host = DB_HOST;
    protected $db_user = DB_USER;
    protected $db_pass = DB_PASS;
    protected $db_name = DB_NAME;
    protected $db_port = DB_PORT;
    protected $connection;

    public function __construct()
    {
        $this->connect();
    }

    function connect()
    {
        $this->connection = new mysqli(
            $this->db_host,
            $this->db_user,
            $this->db_pass,
            $this->db_name,
            $this->db_port
        );

        if ($this->connection->connect_error) {
            die("Connection failed: " . $this->connection->connect_error);
        }
    }

    function disconnect()
    {
        $this->connection->close();
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
