<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;
use MongoDB\Database;

class MongodbConnection {
    private Client $client;
    private Database $database;
    public function __construct() {
        global $mongodb_config;
        $uri = "mongodb+srv://{$mongodb_config['user']}:{$mongodb_config['password']}@{$mongodb_config['instance']}?retryWrites=true&w=majority&appName=EasyLoc";

        try {
            $this->client = new Client($uri);
            //$client->selectDatabase('easyloc')->command(['ping' => 1]);
            $this->database = $this->client->selectDatabase($mongodb_config['database']);
            $this->database->command(['ping' => 1]);
            echo "Pinged your deployment. You successfully connected to MongoDB!\n";
        } catch (Exception $e) {
            die("Erreur de connexion à MongoDB : " . $e->getMessage());
        }
    }

    public function getDatabase(): Database {
        return $this->database;
    }
}