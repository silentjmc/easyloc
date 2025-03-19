<?php
require_once __DIR__ . '/../database/mysql_connection.php';
require_once __DIR__ . '/billing_entity.php';

/**
 * Class BillingCrud
 * 
 * This class contains methods to interact with the billing table in the MySQL database. 
 * It provides functionality to create, read, update, and delete billing records.
 */
class BillingCrud {
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
            CREATE TABLE IF NOT EXISTS billing (
                id INT PRIMARY KEY AUTO_INCREMENT,
                contract_id INT NOT NULL,
                amount DECIMAL(10,2) NOT NULL
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
    public function createBilling(Billing $billing):bool {
        $sql = '
            INSERT INTO billing (contract_id, amount)
            VALUES (:contract_id, :amount)
        ';
        
        $stmt = $this->pdo->prepare($sql);

        $contract_id = $billing->getContractId();
        $amount = $billing->getAmount();
        $stmt->bindParam(':contract_id', $contract_id);
        $stmt->bindParam(':amount', $amount);

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
    public function updateBilling(Billing $billing): bool {
        $sql = "
        UPDATE billing SET contract_id = :contract_id, amount = :amount WHERE id = :id
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':contract_id', $billing->getContractId());
    $stmt->bindValue(':amount', $billing->getAmount());
    $stmt->bindValue(':id', $billing->getId());
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
    public function deleteBilling(int $id): bool {
        $sql = "
            DELETE FROM billing WHERE id = :id
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
     * @return ?Billing Returns the contract object if found, null otherwise.
     */
    public function findBillingById(int $id): ?Billing {
        $sql = "
            SELECT * FROM billing WHERE id = :id
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            // Retrieve contract data
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Create and populate a Billing object
            $billing = new Billing();
            $billing->setId($row['id']);
            $billing->setContractId($row['contract_id']);
            $billing->setAmount($row['amount']);
            
            return $billing;
        }
    
        // No billing found
        return null;
    }
}