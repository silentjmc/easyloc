<?php
require_once __DIR__ . '/../database/mongodb_connection.php';
require_once __DIR__ . '/customer_entity.php';

use MongoDB\Collection;

/**
 * Class CustomerCrud
 * 
 * This class handles CRUD operations for the Customer collection in a MongoDB database.
 * It allows adding, updating, deleting, and retrieving customer records from the database.
 */

class CustomerCrud {
    /**
     * @var Collection The MongoDB collection for the 'Customer' data.
     */
    private Collection $collection;

    /**
     * CustomerCrud constructor.
     * 
     * Initializes a connection to the MongoDB database and selects the 'Customer' collection.
     */
    public function __construct() {
        $connection = new MongodbConnection();
        $database = $connection->getDatabase();
        $this->collection = $database->selectCollection('Customer');
    }

    /**
     * Adds a new customer to the database.
     * 
     * @param array $customerData The customer data to insert.
     * @return string The UID of the newly added customer.
     */
    public function addCustomer(array $customerData): string {
            $result = $this->collection->insertOne($customerData);
            return (string) $result->getInsertedId();
        }
    
    /**
     * Converts a Customer object into an associative array that can be inserted into MongoDB.
     * 
     * @param Customer $customer The Customer object to convert.
     * @return array The customer data as an associative array.
     */    
    private function toMongoDocument(Customer $customer): array {
        return [
            'uid' => $customer->getUid(),
            'first_name' => $customer->getFirstName(),
            'second_name' => $customer->getSecondName(),
            'address' => $customer->getAddress(),
            'permit_number' => $customer->getPermitNumber()
        ];
    }

    /**
     * Creates a new customer in the MongoDB collection.
     * 
     * @param Customer $customer The customer object to insert.
     * @return string The UID of the newly created customer.
     * @throws Exception If the customer cannot be created.
     */
    public function createCustomer(Customer $customer): string {  
        $document = $this->toMongoDocument($customer);
        $result = $this->collection->insertOne($document);
        
        if ($result->getInsertedCount() === 0) {
            throw new Exception("Failed to create customer");
        }
        
        return $customer->getUid();
    }

    /**
     * Updates an existing customer in the MongoDB collection.
     * 
     * @param Customer $customer The customer object containing the updated data.
     * @throws Exception If the customer cannot be updated.
     */
    public function updateCustomer(Customer $customer): void {
        $document = $this->toMongoDocument($customer);
        $result = $this->collection->replaceOne(['uid' => $customer->getUid()], $document);
        
        if ($result->getModifiedCount() === 0) {
            throw new Exception("Failed to update customer");
        }
    }

    /**
     * Deletes a customer from the MongoDB collection.
     * 
     * @param string $uid The UID of the customer to delete.
     * @throws Exception If the customer cannot be deleted.
     */
    public function deleteCustomer(string $uid): void {
        $result = $this->collection->deleteOne(['uid' => $uid]);
        
        if ($result->getDeletedCount() === 0) {
            throw new Exception("Failed to delete customer");
        }
    }

    /**
     * Retrieves a customer from the MongoDB collection by first name and second name.
     * 
     * @param string $first_name The first name of the customer.
     * @param string $second_name The second name of the customer.
     * @return Customer|null The customer object if found, or null if no matching customer is found.
     */
    public function getCustomer(string $first_name, string $second_name): ?Customer {
        $document = $this->collection->findOne(['first_name' => $first_name, 'second_name' => $second_name]);
        
        if (is_null($document)) {
            return null;
        }
        // Converts BSONDocument to an array
        $documentArray = $document->getArrayCopy(); 

        return new Customer($documentArray);
    }
}