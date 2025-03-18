<?php

/**
 * Databases configuration
 * 
 * This file contains the connection parameters to the databases used by the application
 */

/**
 * MYSQL Configuration
 * 
 * @var array $mysql_config Array contains the connection parameters to the MySQL database
 * 
 * @var string $host The database server host (e.g., 'localhost' or an IP address)
 * @var int $port The port on which MySQL is listening
 * @var string $user The username to authenticate to MySQL
 * @var string $password The password for the MySQL user
 * @var string $database The name of the MySQL database to connect to
 * 
 * @see mysql_connection.php to use the configuration
 */
$mysql_config = [
    'host' => 'localhost',
    'port' => 3306,
    'user' => 'root',
    'password' => '',
    'database' => 'easyloc'
];
