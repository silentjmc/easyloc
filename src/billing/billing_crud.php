<?php
require_once __DIR__ . '/../database/mysql_connection.php';
require_once __DIR__ . '/billing_entity.php';
require_once __DIR__ . '/../contract/contract_entity.php';

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
     * Creates the billing table in the database if it does not already exist.
     * 
     * This method checks if the `billing` table exists in the database. 
     * If not, it creates the table with the necessary columns.
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
     * Creates a new billing record in the database.
     * 
     * This method inserts a new billing record into the `billing` table using the provided 
     * Billing object..
     * 
     * @param Billing $billing The billing object to be created.
     * @return bool Returns true if the billing record was successfully created, false otherwise.
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
     * Updates an existing billing in the database.
     * 
     * This method updates an existing billing in the `billing` table with the provided
     * Billing object, based on the record's ID.
     * 
     * @param Billing $billing The billing object to be updated.
     * @return bool Returns true if the billing record was successfully updated, false otherwise.
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
     *  Deletes a billing from the database by its ID.
     * 
     * This method deletes a billing from the `billing` table based on the provided billing ID.
     * 
     * @param int $id The ID of the billing record to be deleted.
     * @return bool Returns true if the billing was successfully deleted, false otherwise.
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
     * Retreive a billing by its ID.
     * 
     * This method retrieves a billing from the `billing` table based on the provided billing ID.
     * 
     * @param int $id The ID of the billing to be retrieved.
     * @return ?Billing Returns the billing object if found, null otherwise.
     */
    public function getBillingById(int $id): ?Billing {
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

    /**
     * Retrieves all payments associated with a specific contract.
     *
     * This method fetches all payments linked to a given contract ID 
     * and returns them as an array of `Billing` objects.
     * 
     * @param int $contract_id The unique identifier of the contract.
     * 
     * @return Billing[] An array of `Billing` objects representing the payments 
     *                   associated with the specified contract.
     *
     */
    public function getPaymentsByContractId (int $contract_id): array {
        $sql = "SELECT * FROM billing WHERE contract_id = :contract_id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':contract_id', $contract_id);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($results); 
        return array_map(fn($data) => new Billing($data), $results);
    }

    /**
     * Checks if a contract is fully paid.
     *
     * This method verifies whether the total payments made for a given contract 
     * are equal to or greater than the contract's price.
     *
     * @param int $contractId The unique identifier of the contract.
     * 
     * @return bool Returns `true` if the contract is fully paid, otherwise `false`.
     *
     */
    public function isContractFullyPaid(int $contractId): bool {
        $sql = "SELECT c.price, COALESCE(SUM(b.amount),0) AS total_payments 
                FROM contract c
                LEFT JOIN billing b ON c.id = b.contract_id
                WHERE c.id = :contract_id
                GROUP BY c.id";
    
        // Préparation de la requête
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':contract_id', $contractId);
        $stmt->execute();
        
        // Récupérer les résultats
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            // Si le contrat n'existe pas dans la base de données
            throw new Exception("Le contrat avec l'ID $contractId n'existe pas.");
        }
    
        // Vérification si la somme des paiements est égale ou supérieure au prix
        return $result['total_payments'] >= $result['price'];
    }

    /**
     * Retrieves a list of contracts that are not fully paid.
     *
     * This method returns all contracts where the total payments made are less than the contract price.
     *
     * @return Contract[] An array of `Contract` objects representing unpaid contracts.
     * 
     */
    public function getUnpaidContract(): array {
        $sql = "SELECT c.*, COALESCE(SUM(b.amount),0) AS total_payments
                FROM contract c
                LEFT JOIN billing b ON c.id = b.contract_id
                GROUP BY c.id
                HAVING total_payments < c.price";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Contract($data), $results);
    }
}