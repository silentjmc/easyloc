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
        $returning_datetime = $contract->getReturningDatetime() ? $contract->getReturningDatetime()->format('Y-m-d H:i:s') : null;

        $vehicle_uid = $contract->getVehicleUid();
        $customer_uid = $contract->getCustomerUid();
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
        $sql = "UPDATE contract SET vehicle_uid = :vehicle_uid , customer_uid = :customer_uid, sign_datetime = :sign_datetime, loc_begin_datetime = :loc_begin_datetime, loc_end_datetime = :loc_end_datetime, returning_datetime = :returning_datetime, price = :price WHERE id = :id";
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
        $sql = "DELETE FROM contract WHERE id = :id";
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
        $sql = "SELECT * FROM Contract WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? new Contract($result) : null;
    }

    /**
     * Retrieves all contracts associated with a specific customer ID.
     * 
     * This method fetches contracts from the database based on the given customer UID.
     * It then converts each result into a `Contract` object.
     * 
     * @param string $customerId The unique identifier of the customer whose contracts are to be retrieved.
     * 
     * @return Contract[] An array of `Contract` objects that are associated with the given `customerId`.
     */
    public function getContractsByCustomerUid(string $customerUid): array {
        $sql = "SELECT * FROM contract WHERE customer_uid = :customer_uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':customer_uid', $customerUid);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Contract($data), $results);
    }

    /**
     * Retrieves all contracts associated with a specific vehicle.
     *
     * This method fetches contracts from the database based on the given vehicle UID
     * It then converts each result into a `Contract` object.
     *
     * @param string $vehicleUid The unique identifier of the vehicle whose contracts are to be retrieved.
     * 
     * @return Contract[] An array of `Contract` objects that are associated with the give 'vehicleUID'
     */
    public function getContractsByVehicleUid(string $vehicleUid): array {
        $sql = "SELECT * FROM contract WHERE vehicle_uid = :vehicle_uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':vehicle_uid', $vehicleUid);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Contract($data), $results);
    }

    /**
     * Retrieves all contracts and groups them by vehicle.
     *
     * This method fetches contracts from the database, groups them by `vehicle_uid`,
     * and converts each contract entry into a `Contract` object.
     *
     * @return array, Contract[]> An associative array where keys are vehicle UIDs
     * and values are arrays of Contract objects.
     */
    public function getContractsGroupedByVehicleUid(): array {
        $sql = "SELECT vehicle_uid, JSON_ARRAYAGG(JSON_OBJECT('id', id, 'customer_uid', customer_uid, 'sign_datetime', sign_datetime, 'loc_begin_datetime', loc_begin_datetime, 'loc_end_datetime', loc_end_datetime, 'returning_datetime', returning_datetime, 'price', price)) AS contracts FROM contract GROUP BY vehicle_uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
        //return $results;
        $groupedContracts = [];

        foreach ($results as $row) {
            $vehicleUid = $row['vehicle_uid'];
            $contractsData = json_decode($row['contracts'], true);
    
            $contracts = array_map(fn($data) => new Contract($data), $contractsData);
    
            $groupedContracts[$vehicleUid] = $contracts;
        }
    
        return $groupedContracts;
    }

    /**
     * Retrieves all contracts and groups them by customer.
     *
     * This method fetches contracts from the database, groups them by `customer_uid`,
     * and converts each contract entry into a `Contract` object.
     *
     * @return array, Contract[]> An associative array where keys are customer UIDs
     * and values are arrays of Contract objects.
     */
    public function getContractsGroupedBycustomerUid (): array {
        $sql = "SELECT customer_uid, JSON_ARRAYAGG(JSON_OBJECT('id', id, 'vehicle_uid', vehicle_uid, 'sign_datetime', sign_datetime, 'loc_begin_datetime', loc_begin_datetime, 'loc_end_datetime', loc_end_datetime, 'returning_datetime', returning_datetime, 'price', price)) AS contracts FROM contract GROUP BY customer_uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results=$stmt->fetchAll(PDO::FETCH_ASSOC);
        //return $results;
        $groupedContracts = [];

        foreach ($results as $row) {
            $vehicleUid = $row['customer_uid'];
            $contractsData = json_decode($row['contracts'], true);
    
            $contracts = array_map(fn($data) => new Contract($data), $contractsData);
    
            $groupedContracts[$vehicleUid] = $contracts;
        }
    
        return $groupedContracts;
    }

    /**
     * Retrieves the ongoing rents for a specific customer.
     *
     * This method fetches contracts where The current date (`NOW()`) is between `loc_begin_datetime` and `loc_end_datetime`
     * and `returning_datetime` is NULL, meaning the vehicle has not been returned yet.
     *
     * @param string $customerUid The unique identifier of the customer.
     * @return Contract[] An array of ongoing rental Contract objects.
     */
    public function getOngoingRentsbyCustomerUid(string $customerUid): array {
        $sql = "SELECT * FROM contract WHERE customer_uid = :customer_uid  AND loc_begin_datetime <= NOW() AND loc_end_datetime >= NOW() AND returning_datetime IS NULL";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':customer_uid', $customerUid);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Contract($data), $results);
    }

    /**
     * Retrieves all overdue rents.
     *
     * A rent is considered overdue if:
     * - The vehicle has NOT been returned (`returning_datetime IS NULL`)
     *   AND `loc_end_datetime` was more than 1 hour ago.
     * - OR the vehicle was returned (`returning_datetime IS NOT NULL`),
     *   but the return time is more than 1 hour after `loc_end_datetime`.
     *
     * @return Contract[] An array of overdue rental Contract objects.
     */
    public function getOverdueRents(): array {
        $sql = "SELECT * FROM contract WHERE (returning_datetime IS NULL AND loc_end_datetime < NOW() - INTERVAL 1 HOUR) OR (returning_datetime IS NOT NULL AND returning_datetime > loc_end_datetime + INTERVAL 1 HOUR)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($data) => new Contract($data), $results);
    }

    /**
     * Gets the total number of overdue rents between two specified dates.
     *
     * This method retrieves the total number of contracts that are overdue between 
     * the specified start and end dates. A contract is considered overdue if:
     * - The `returning_datetime` is NULL, and the `loc_end_datetime` is more than 
     *   one hour ago.
     * - The `returning_datetime` is not NULL, and the `returning_datetime` is 
     *   more than one hour after the `loc_end_datetime`.
     *
     * @param DateTime $start The start date of the range to check.
     * @param DateTime $end The end date of the range to check.
     * 
     * @return int The total number of overdue rents between the specified dates.
     * 
     */
    public function getTotalOverdueRentsBetweenDates(DateTime $start, DateTime $end): int {
        $sql = "SELECT COUNT(*) FROM contract WHERE (returning_datetime IS NULL AND loc_end_datetime < NOW() - INTERVAL 1 HOUR) OR (returning_datetime IS NOT NULL AND returning_datetime > loc_end_datetime + INTERVAL 1 HOUR) AND loc_end_datetime BETWEEN :start AND :end";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':start', $start->format('Y-m-d H:i:s'));
        $stmt->bindValue(':end', $end->format('Y-m-d H:i:s'));
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Retrieves the average number of overdue rentals per customer.
     *
     * This method calculates the average count of overdue rentals across all customers.
     * A rental is considered overdue if:
     * - The vehicle has not been returned and the expected end time has passed by at least one hour.
     * - The vehicle has been returned, but the return time exceeds the expected end time by more than one hour.
     *
     * The result is a single scalar value representing the average number of overdue rentals.
     *
     * @return float|null The average number of overdue rentals per customer, or null if there are no overdue rentals.
     *
     * Example output:
     * 2.3
     */
    public function getAverageOverdueRentsByCustomer(): float {
        $sql = "SELECT AVG(overdue_count) AS average_overdue_count FROM (SELECT customer_uid, count(*) AS overdue_count FROM contract WHERE (returning_datetime IS NULL AND loc_end_datetime < NOW() - INTERVAL 1 HOUR) OR (returning_datetime IS NOT NULL AND returning_datetime > loc_end_datetime + INTERVAL 1 HOUR) group by customer_uid) AS overdue_data";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * Retrieves the average overdue time (in minutes) for each vehicle.
     *
     * This method calculates the average delay time for vehicles that were returned late.
     * It considers only contracts where the returning time is greater than the expected end time by at least one hour.
     *
     * @return array An associative array containing 'vehicle_uid' and 'average_time_overdue' (in minutes) for each vehicle.
     * 
     */
    public function getAverageTimeOverdueByVehicle(): array {
        $sql = "SELECT vehicle_uid, AVG(TIMESTAMPDIFF(MINUTE, loc_end_datetime, returning_datetime)) AS average_time_overdue FROM contract WHERE returning_datetime IS NOT NULL AND returning_datetime > loc_end_datetime + INTERVAL 1 HOUR GROUP BY vehicle_uid";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}