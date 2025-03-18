<?php

require_once __DIR__ . '/config.php';

/**
 * Class MySqlConnection
 * 
 * This class manages the connection to the MySQL database using PDO.
 * It establishes the connection with the parameters provided in the configuration file
 * and allows access to the PDO connection as well as closing it when necessary.
 */

class MySqlConnection {
    /**
     * @var PDO $pdo Instance of the PDO connection used to interact with the MySQL database.
     */
    private $pdo;

    /**
     * MySqlConnection constructor.
     * 
     * This method creates a PDO connection to the MySQL database using the
     * configuration settings contained in the `config.php` file.
     * @see config.php
     * 
     * @throws PDOException If the connection fails, an exception is thrown and the error message is displayed.
     */
    public function __construct() {
        global $mysql_config;
        // Create the DSN string
        $dsn = "mysql:host={$mysql_config['host']};port={$mysql_config['port']};dbname={$mysql_config['database']}";
        
        try {
            // Create the PDO connection
            $this->pdo = new PDO($dsn, $mysql_config['user'], $mysql_config['password']);
            // Set PDO to throw exceptions on errors
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Set the default fetch mode to associative arrays
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Display a clear error message in case of connection failure
            die("Erreur de connexion MySQL: " . $e->getMessage());
        }
    }

    /**
     * Returns the PDO instance.
     * 
     * This method allows access to the PDO instance, which is used to interact with the database.
     * 
     * @return PDO The PDO connection instance.
     */
    public function getPdo() {
        return $this->pdo;
    }

    /**
     * Closes the database connection.
     * 
     * This method sets the PDO instance to null, effectively closing the connection to the database.
     * It is recommended to call this method after all database operations are complete.
     */
    public function close() {
        $this->pdo = null;
    }
}