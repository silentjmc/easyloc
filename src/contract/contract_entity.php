<?php
/**
 * Class Contract
 * 
 * This class represents a vehicle rental contract, including various details
 * such as the vehicle's unique ID, the customer's unique ID, the contract's signing date, 
 * rental dates, returning date, and the price of the contract.
 */
class Contract {
    /**
     * @var int|null $id The unique identifier for the contract.
     */
    private ?int $id = null;

    /**
     * @var string $vehicleUid The unique identifier for the vehicle.
     */
    private string $vehicleUid = '';

    /**
     * @var string $customerUid The unique identifier for the customer.
     */
    private string $customerUid = '';

    /**
     * @var DateTime $signDatetime The date and time when the contract was signed.
     */
    private ?DateTime $signDatetime = null;

    /**
     * @var DateTime $locBeginDatetime The date and time when the location rental begins.
     */
    private ?DateTime $locBeginDatetime = null;

    /**
     * @var DateTime $locEndDatetime The date and time when the location rental ends.
     */
    private ?DateTime $locEndDatetime = null;

    /**
     * @var DateTime|null $returningDatetime The date and time when the vehicle is returned (nullable).
     */
    private ?DateTime $returningDatetime = null;

    /**
     * @var float $price The total price of the contract.
     */
    private float $price = 0.0;

    /**
     * Contract class constructor.
     *
     * @param array $data (optional) An associative array containing values to populate the object.
     *                    The keys should match the database column names (snake_case).
     */
    public function __construct(array $data = []) {
        $this->hydrate($data);
    }

    /**
     * Populates the object with an associative array of data.
     *
     * @param array $data Associative array containing values to hydrate the object.
     *                    The keys must be in **snake_case** (e.g., `vehicle_uid`).
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
     * Get the unique identifier for the contract.
     * 
     * @return int|null The unique identifier of the contract, or null if not set.
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Get the unique identifier for the vehicle.
     * 
     * @return string The vehicle's unique identifier.
     */
    public function getVehicleUid(): string {
        return $this->vehicleUid;
    }

    /**
     * Get the unique identifier for the customer.
     * 
     * @return string The customer's unique identifier.
     */
    public function getCustomerUid(): string {
        return $this->customerUid;
    }

    /**
     * Get the date and time when the contract was signed.
     * 
     * @return DateTime The date and time when the contract was signed.
     */
    public function getSignDatetime(): DateTime {
        return $this->signDatetime;
    }

    /**
     * Get the date and time when the location rental begins.
     * 
     * @return DateTime The date and time when the location rental begins.
     */
    public function getLocBeginDatetime(): DateTime {
        return $this->locBeginDatetime;
    }

    /**
     * Get the date and time when the location rental ends.
     * 
     * @return DateTime The date and time when the location rental ends.
     */
    public function getLocEndDatetime(): DateTime {
        return $this->locEndDatetime;
    }

    /**
     * Get the date and time when the vehicle is returned.
     * 
     * @return DateTime|null The date and time when the vehicle is returned, or null if not set.
     */
    public function getReturningDatetime(): ?DateTime {
        return $this->returningDatetime;
    }

    /**
     * Get the total price of the contract.
     * 
     * @return float The total price of the contract.
     */
    public function getPrice(): float {
        return $this->price;
    }

    // Setters
    /**
     * Set the unique identifier for the contract.
     * 
     * @param int $id The unique identifier of the contract.
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Set the unique identifier for the vehicle.
     * 
     * @param string $vehicleUid The vehicle's unique identifier.
     */
    public function setVehicleUid(string $vehicleUid): void {
        $this->vehicleUid = $vehicleUid;
    }

    /**
     * Set the unique identifier for the customer.
     * 
     * @param string $customerUid The customer's unique identifier.
     */
    public function setCustomerUid(string $customerUid): void {
        $this->customerUid = $customerUid;
    }

    /**
     * Set the date and time when the contract was signed.
     * 
     * @param DateTime $signDatetime The date and time when the contract was signed.
     */
    public function setSignDatetime($signDatetime): void {
        $this->signDatetime = $signDatetime;
    }

    /**
     * Set the date and time when the location rental begins.
     * 
     * @param DateTime $locBeginDatetime The date and time when the location rental begins.
     */
    public function setLocBeginDatetime($locBeginDatetime): void {
        $this->locBeginDatetime = $locBeginDatetime;
    }

    /**
     * Set the date and time when the location rental ends.
     * 
     * @param DateTime $locEndDatetime The date and time when the location rental ends.
     */
    public function setLocEndDatetime($locEndDatetime): void {
        $this->locEndDatetime = $locEndDatetime;
    }

    /**
     * Set the date and time when the vehicle is returned.
     * 
     * @param DateTime|null $returningDatetime The date and time when the vehicle is returned, or null if not set.
     */
    public function setReturningDatetime($returningDatetime): void {
        $this->returningDatetime = $returningDatetime;
    }

    /**
     * Set the total price of the contract.
     * 
     * @param float $price The total price of the contract.
     */
    public function setPrice(float $price): void {
        $this->price = $price;
    }

}