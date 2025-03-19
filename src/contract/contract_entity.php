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
    private $id;

    /**
     * @var string $vehicleUid The unique identifier for the vehicle.
     */
    private $vehicleUid;

    /**
     * @var string $customerUid The unique identifier for the customer.
     */
    private $customerUid;

    /**
     * @var DateTime $signDatetime The date and time when the contract was signed.
     */
    private $signDatetime;

    /**
     * @var DateTime $locBeginDatetime The date and time when the location rental begins.
     */
    private $locBeginDatetime;

    /**
     * @var DateTime $locEndDatetime The date and time when the location rental ends.
     */
    private $locEndDatetime;

    /**
     * @var DateTime|null $returningDatetime The date and time when the vehicle is returned (nullable).
     */
    private $returningDatetime;

    /**
     * @var float $price The total price of the contract.
     */
    private $price;

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
    public function getReturningDatetime(): DateTime {
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
    public function setSignDatetime(DateTime $signDatetime): void {
        $this->signDatetime = $signDatetime;
    }

    /**
     * Set the date and time when the location rental begins.
     * 
     * @param DateTime $locBeginDatetime The date and time when the location rental begins.
     */
    public function setLocBeginDatetime(DateTime $locBeginDatetime): void {
        $this->locBeginDatetime = $locBeginDatetime;
    }

    /**
     * Set the date and time when the location rental ends.
     * 
     * @param DateTime $locEndDatetime The date and time when the location rental ends.
     */
    public function setLocEndDatetime(DateTime $locEndDatetime): void {
        $this->locEndDatetime = $locEndDatetime;
    }

    /**
     * Set the date and time when the vehicle is returned.
     * 
     * @param DateTime|null $returningDatetime The date and time when the vehicle is returned, or null if not set.
     */
    public function setReturningDatetime(?DateTime $returningDatetime): void {
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