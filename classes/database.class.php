<?php

class Database
{
    // These are the properties that store the database connection details.
    private $host = 'localhost';      // The hostname of the database server.
    private $username = 'root';       // The username used to connect to the database.
    private $password = '';           // The password used to connect to the database (empty string means no password).
    private $dbname = 'system';    // The name of the database to connect to.

    protected $connection;            // This property will hold the PDO connection object once connected.

    public function connect()
    {
        if ($this->connection === null) {
            try {
                // Create a new PDO instance with the provided database details.
                $this->connection = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);

                // Set PDO attributes for error handling and fetching mode.
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                // Handle any connection errors.
                die("Connection failed: " . $e->getMessage());
            }
        }

        // Return the established connection.
        return $this->connection;
    }
}
?>