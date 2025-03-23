<?php
require_once __DIR__ . '/../database/mongodb_connection.php';
require_once __DIR__ . '/vehicle_entity.php';

use MongoDB\Collection;

class VehicleCrud {
    private Collection $collection;

    public function __construct() {
        $connection = new MongodbConnection();
        $database = $connection->getDatabase();
        $this->collection = $database->selectCollection('Vehicle');
    }

    public function addVehicle(array $vehicleData): string {
            $result = $this->collection->insertOne($vehicleData);
            return (string) $result->getInsertedId();
        }
    
    private function toMongoDocument(Vehicle $vehicle): array {
        return [
            'uid' => $vehicle->getUid(),
            'licence_plate' => $vehicle->getLicencePlate(),
            'informations' => $vehicle->getInformations(),
            'km' => $vehicle->getKm()
        ];
    }

    public function createVehicle(Vehicle $vehicle): string {  
        $document = $this->toMongoDocument($vehicle);
        $result = $this->collection->insertOne($document);
        
        if ($result->getInsertedCount() === 0) {
            throw new Exception("Failed to create vehicle");
        }
        
        return $vehicle->getUid();
    }

    public function updateVehicle(Vehicle $vehicle): void {
        $document = $this->toMongoDocument($vehicle);
        $result = $this->collection->replaceOne(['uid' => $vehicle->getUid()], $document);
        
        if ($result->getModifiedCount() === 0) {
            throw new Exception("Failed to update vehicle");
        }
    }

    public function deleteVehicle(string $uid): void {
        $result = $this->collection->deleteOne(['uid' => $uid]);
        
        if ($result->getDeletedCount() === 0) {
            throw new Exception("Failed to delete vehicle");
        }
    }

    public function countVehiclesWithKmGreater(int $kilometrage): int {
        $filter = ['km' => ['$gt' => $kilometrage]];

        // Utilise la méthode countDocuments pour compter le nombre de véhicules correspondant au filtre
        $count = $this->collection->countDocuments($filter);
    
        return $count;
    }

    public function countVehiclesWithKmLesser(int $kilometrage): int {
        $filter = ['km' => ['$lt' => $kilometrage]];

        // Utilise la méthode countDocuments pour compter le nombre de véhicules correspondant au filtre
        $count = $this->collection->countDocuments($filter);
    
        return $count;
    }
}