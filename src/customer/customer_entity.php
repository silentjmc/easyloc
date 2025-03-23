<?php
require_once __DIR__ . '/../vendor/autoload.php';

class Customer {
    /**
     * Class Customer
     * 
     * This class represents a customer with various attributes such as UID, first name, second name, address, and permit number.
     * It includes getter and setter methods for each attribute, as well as a hydration method to populate the class with data.
     */

    /**
     * @var ?string The unique identifier (UID) of the customer. Can be null.
     */
    private ?string $uid = null;

    /**
     * @var string The first name of the customer.
     */
    private string $firstName;

    /**
     * @var string The second name of the customer.
     */   
    private string $secondName;
    
    /**
     * @var string The address of the customer.
     */
    private string $address;
    
    /**
     * @var string The permit number of the customer.
     */
    private string $permitNumber;

    /**
     * Customer constructor.
     * 
     * Initializes the customer object by hydrating it with the provided data array.
     * 
     * @param array $data The data array to populate the object. Each key should correspond to a setter method.
     */
    public function __construct(array $data = []) {
        $this->hydrate($data);
    }

    /**
     * Hydrates the customer object with the given data.
     * This method iterates through the data array and calls the corresponding setter method for each key.
     * 
     * @param array $data The data to hydrate the object with.
     */
    public function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            // Convert snake_case to camelCase (e.g., vehicle_uid -> vehicleUid)
            $camelCaseKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
    
            // Find the setter
            $method = 'set' . ucfirst($camelCaseKey); 
            
            if (method_exists($this, $method)) {
                if (str_contains($camelCaseKey, 'Datetime') && !is_null($value) && !($value instanceof DateTime)) {
                    $value = new DateTime($value);
                }
                $this->$method($value);
            }
        }
    }

    // Getters
    /**
     * Gets the UID of the customer.
     * 
     * @return string|null The UID of the customer, or null if not set.
     */
    public function getUid() : ?string {
        return $this->uid ; 
    }

    /**
     * Gets the first name of the customer.
     * 
     * @return string The first name of the customer.
     */
    public function getFirstName() : string { 
        return $this->firstName ; 
    }

    /**
     * Gets the second name of the customer.
     * 
     * @return string The second name of the customer.
     */
    public function getSecondName() : string { 
        return $this->secondName ; 
    }

    /**
     * Gets the address of the customer.
     * 
     * @return string The address of the customer.
     */
    public function getAddress() : string { 
        return $this->address ; 
    }
 
    /**
     * Gets the permit number of the customer.
     * 
     * @return string The permit number of the customer.
     */
    public function getPermitNumber() : string { 
        return $this->permitNumber ; 
    }

    // Setters
    /**
     * Sets the UID of the customer.
     * 
     * @param string $uid The UID to set for the customer.
     * @return self The current instance of the class for method chaining.
     */
    public function setUid(string $uid): self { 
        $this->uid = $uid;
        return $this;
    }

    /**
     * Sets the first name of the customer.
     * 
     * @param string $firstName The first name to set for the customer.
     * @return self The current instance of the class for method chaining.
     */
    public function setFirstName(string $firstName): self { 
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Sets the second name of the customer.
     * 
     * @param string $secondName The second name to set for the customer.
     * @return self The current instance of the class for method chaining.
     */
    public function setSecondName(string $secondName): self { 
        $this->secondName = $secondName;
        return $this;
    }

    /**
     * Sets the address of the customer.
     * 
     * @param string $address The address to set for the customer.
     * @return self The current instance of the class for method chaining.
     */
    public function setAddress(string $address): self { 
        $this->address = $address;
        return $this;
    }

    /**
     * Sets the permit number of the customer.
     * 
     * @param string $permitNumber The permit number to set for the customer.
     * @return self The current instance of the class for method chaining.
     */
    public function setPermitNumber(string $permitNumber): self { 
        $this->permitNumber = $permitNumber;
        return $this;
    }
}