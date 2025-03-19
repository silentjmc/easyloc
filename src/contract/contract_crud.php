<?php
require_once __DIR__ . '/../database/mysql_connection.php';
require_once __DIR__ . '/contract_entity.php';

/**
 * Class ContractCrud
 * 
 * This class contains methods to interact with the contract table in the MySQL database. 
 * It provides functionality to create, read, update, and delete contracts.
 */
class ContractCrud {
    /**
     * @var PDO The PDO connection object.
     */
    private $pdo;

    /**
     * Constructor
     * 
     * @param PDO $pdo The PDO connection object.
     */
    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

     /**
     * Creates the contract table in the database if it does not already exist.
     * 
     * This method checks if the `contract` table exists in the database, 
     * and if not, it creates the table with the necessary columns.
     * 
     * @return void
     */
    public function createTable(): void {
        $sql = "
            CREATE TABLE IF NOT EXISTS contract (
                id INT PRIMARY KEY AUTO_INCREMENT,
                vehicle_uid CHAR(255) NOT NULL,
                customer_uid CHAR(255) NOT NULL,
                sign_datetime DATETIME NOT NULL,
                loc_begin_datetime DATETIME NOT NULL,
                loc_end_datetime DATETIME NOT NULL,
                returning_datetime DATETIME NULL,
                price DECIMAL(10,2) NOT NULL
            )
        ";
        $this->pdo->exec($sql);
    }

    /**
     * Creates a new contract in the database.
     * 
     * This method inserts a new contract into the `contract` table with the provided contract details.
     * 
     * @param Contract $contract The contract object to be created.
     * @return bool Returns true if the contract was successfully created, false otherwise.
     */
    public function createContract(Contract $contract):bool {
        $sql = '
            INSERT INTO contract (vehicle_uid, customer_uid, sign_datetime, loc_begin_datetime, loc_end_datetime, returning_datetime, price)
            VALUES (:vehicle_uid, :customer_uid, :sign_datetime, :loc_begin_datetime, :loc_end_datetime, :returning_datetime, :price)
        ';
        
        $stmt = $this->pdo->prepare($sql);

        $sign_datetime = $contract->getSignDatetime()->format('Y-m-d H:i:s');
        $loc_begin_datetime = $contract->getLocBeginDatetime()->format('Y-m-d H:i:s');
        $loc_end_datetime = $contract->getLocEndDatetime()->format('Y-m-d H:i:s');
        $returning_datetime = $contract->getReturningDatetime()->format('Y-m-d H:i:s');

        $vehicle_uid = $contract->getVehicleUid();
        $customer_uid = $contract->getCustomerUid();
        $stmt->bindParam(':sign_datetime', $sign_datetime);
        $stmt->bindParam(':loc_begin_datetime', $loc_begin_datetime);
        $stmt->bindParam(':loc_end_datetime', $loc_end_datetime);
        $stmt->bindParam(':returning_datetime', $returning_datetime);
        $price = $contract->getPrice(); 

        $stmt->bindParam(':vehicle_uid', $vehicle_uid);
        $stmt->bindParam(':customer_uid', $customer_uid);
        $stmt->bindParam(':sign_datetime', $sign_datetime);
        $stmt->bindParam(':loc_begin_datetime', $loc_begin_datetime);
        $stmt->bindParam(':loc_end_datetime', $loc_end_datetime);
        $stmt->bindParam(':returning_datetime', $returning_datetime);
        $stmt->bindParam(':price', $price);

        $result = $stmt->execute();
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            echo "Erreur SQL : " . $errorInfo[2] . "<br>";
        }
        return $result;
    }

    /**
     * Updates an existing contract in the database.
     * 
     * This method updates an existing contract in the `contract` table with the provided contract details.
     * 
     * @param Contract $contract The contract object to be updated.
     * @return bool Returns true if the contract was successfully updated, false otherwise.
     */
    public function updateContract(Contract $contract): bool {
        $sql = "
        UPDATE contract SET vehicle_uid = :vehicle_uid , customer_uid = :customer_uid, sign_datetime = :sign_datetime, loc_begin_datetime = :loc_begin_datetime, loc_end_datetime = :loc_end_datetime, returning_datetime = :returning_datetime, price = :price WHERE id = :id
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':vehicle_uid', $contract->getVehicleUid());
    $stmt->bindValue(':customer_uid', $contract->getCustomerUid());
    $stmt->bindValue(':sign_datetime', $contract->getSignDatetime()->format('Y-m-d H:i:s'));
    $stmt->bindValue(':loc_begin_datetime', $contract->getLocBeginDatetime()->format('Y-m-d H:i:s'));
    $stmt->bindValue(':loc_end_datetime', $contract->getLocEndDatetime()->format('Y-m-d H:i:s'));
    $stmt->bindValue(':returning_datetime', $contract->getReturningDatetime()->format('Y-m-d H:i:s'));
    $stmt->bindValue(':price', $contract->getPrice());
    $stmt->bindValue(':id', $contract->getId());
    $stmt->execute();

    // Verify if a row was affected (update successful)
    if ($stmt->rowCount() > 0) {
        return true; // Contract updated successfully
    } else {
        return false; // No Contract found
    }
    }

    /**
     * Deletes a contract from the database by its ID.
     * 
     * This method deletes a contract from the `contract` table based on the provided contract ID.
     * 
     * @param int $id The ID of the contract to be deleted.
     * @return bool Returns true if the contract was successfully deleted, false otherwise.
     */
    public function deleteContract(int $id): bool {
        $sql = "
            DELETE FROM contract WHERE id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);   
        $stmt->execute();
        
        // Verify if a row was deleted
        if ($stmt->rowCount() > 0) {
            return true;  // Contract deleted successfully
        } else {
            return false; // No Contract found
        }
    }

    /**
     * Finds a contract by its ID.
     * 
     * This method retrieves a contract from the `contract` table based on the provided contract ID.
     * 
     * @param int $id The ID of the contract to be retrieved.
     * @return ?Contract Returns the contract object if found, null otherwise.
     */
    public function findContractById(int $id): ?Contract {
        $sql = "
            SELECT * FROM Contract WHERE id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            // Récupérer les données du contrat
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Créer et remplir un objet Contract
            $contract = new Contract();
            $contract->setId($row['id']);
            $contract->setVehicleUid($row['vehicle_uid']);
            $contract->setCustomerUid($row['customer_uid']);
            $contract->setSignDatetime(new DateTime($row['sign_datetime']));
            $contract->setLocBeginDatetime(new DateTime($row['loc_begin_datetime']));
            $contract->setLocEndDatetime(new DateTime($row['loc_end_datetime']));
            $contract->setReturningDatetime(new DateTime($row['returning_datetime']));
            $contract->setPrice($row['price']);
    
            return $contract;
        }
    
        // Aucun contrat trouvé
        return null;
    }
}