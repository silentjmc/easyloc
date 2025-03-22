<?php
/**
 * Class Billing
 * 
 * Represents a payment associated with a contract.
 * A contract can have multiple payments.
 */
class Billing {
    /**
     * @var int|null $id The unique identifier of the payment.
     */
    private $id;

    /**
     * @var int $contractId The unique identifier of the associated contract for payment.
     */
    private $contractId;

    /**
     * @var float $amount The amount paid for the contract.
     */
    private $amount;

    public function __construct(array $data = []) {
        $this->hydrate($data);
    }

    public function hydrate(array $data): void {
        foreach ($data as $key => $value) {
            // Convert snake_case to camelCase (e.g., vehicle_uid -> vehicleUid)
            $camelCaseKey = lcfirst(str_replace('_', '', ucwords($key, '_')));
    
            // Find the setter
            $method = 'set' . ucfirst($camelCaseKey); 
            
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }


    // Getters
    /**
     * Get the unique identifier of the payment.
     * 
     * @return int|null The payment ID.
     */
    public function getId(): ?int {
        return $this->id;
    }

    /**
     * Get the contract ID associated with the payment.
     * 
     * @return int The contract ID.
     */
    public function getContractId(): int {
        return $this->contractId;
    }

    /**
     * Get the amount paid for the contract.
     * 
     * @return float The amount paid.
     */
    public function getAmount(): float {
        return $this->amount;
    }

    // Setters
    /**
     * Set the unique identifier of the payment.
     * 
     * @param int $id The payment ID.
     */
    public function setId(int $id): void {
        $this->id = $id;
    }

    /**
     * Set the contract ID associated with the payment.
     * 
     * @param int $contractId The contract ID.
     */
    public function setContractId(int $contractId): void {
        $this->contractId = $contractId;
    }

    /**
     * Set the amount paid for the contract.
     * 
     * @param float $amount The payment amount.
     */
    public function setAmount(float $amount): void {
        $this->amount = $amount;
    }

}