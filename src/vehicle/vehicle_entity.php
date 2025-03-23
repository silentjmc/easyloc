<?php
require_once __DIR__ . '/../vendor/autoload.php';

class Vehicle {
    private ?string $uid = null;

    private string $licencePlate;
        
    private string $informations;
    
    private int $km;
    

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
                if (str_contains($camelCaseKey, 'Datetime') && !is_null($value) && !($value instanceof DateTime)) {
                    $value = new DateTime($value);
                }
                $this->$method($value);
            }
        }
    }

    // Getters
    public function getUid() : ?string {
        return $this->uid ; 
    }

    public function getLicencePlate() : string { 
        return $this->licencePlate ; 
    }

    public function getInformations() : string { 
        return $this->informations ; 
    }

    public function getKm() : int { 
        return $this->km ; 
    }

    // Setters
    public function setUid(string $uid): self { 
        $this->uid = $uid;
        return $this;
    }

    public function setLicencePlate(string $licencePlate): self { 
        $this->licencePlate = $licencePlate;
        return $this;
    }

    public function setInformations(string $informations): self { 
        $this->informations = $informations;
        return $this;
    }

    public function setKm(int $km): self { 
        $this->km = $km;
        return $this;
    }

}