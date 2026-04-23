<?php

class Medication {
    private string $name;
    private string $reference;
    private float $unit_price;
    private int $stock;
    private bool $prescription_required;
    private DateTime $expiration_date;

    public function __construct(string $name, string $reference, float $unit_price, int $stock, bool $prescription_required, DateTime $expiration_date) {
        $this->setName($name);
        $this->setReference($reference);
        $this->setUnitPrice($unit_price);
        $this->setStock($stock);
        $this->setPrescriptionRequired($prescription_required);
        $this->setExpirationDate($expiration_date);
    }

    public function getName(): string {
        return $this->name;
    }

    public function setName(string $name): void {
        $this->name = $name;
    }

    public function getReference(): string {
        return $this->reference;
    }

    public function setReference(string $reference): void {
        $this->reference = $reference;
    }

    public function getUnitPrice(): float {
        return $this->unit_price;
    }

    public function setUnitPrice(float $unit_price): void {
        if ($unit_price <= 0) {
            throw new Exception("Price must be positive.");
        }
        $this->unit_price = $unit_price;
    }

    public function getStock(): int {
        return $this->stock;
    }

    public function setStock(int $stock): void {
        if ($stock < 0) {
            throw new Exception("Stock cannot be negative.");
        }
        $this->stock = $stock;
    }

    public function isPrescriptionRequired(): bool {
        return $this->prescription_required;
    }

    public function setPrescriptionRequired(bool $prescription_required): void {
        $this->prescription_required = $prescription_required;
    }

    public function getExpirationDate(): DateTime {
        return $this->expiration_date;
    }

    public function setExpirationDate(DateTime $expiration_date): void {
        $now = new DateTime();
        if ($expiration_date <= $now) {
            throw new Exception("Expiration date must be in the future.");
        }
        $this->expiration_date = $expiration_date;
    }

    public function isExpired(): bool {
        $now = new DateTime();
        return $this->expiration_date <= $now;
    }

    public function dispense(int $quantity, bool $has_prescription = false): void {
        if ($this->isExpired()) {
            throw new Exception("Medication is expired.");
        }
        if ($this->prescription_required && !$has_prescription) {
            throw new Exception("Prescription required for this medication.");
        }
        if ($this->stock < $quantity) {
            throw new Exception("Insufficient stock.");
        }
        
        $this->stock -= $quantity;
    }
}

class Prescription {
    private string $number;
    private string $doctor;
    private string $patient;
    private DateTime $date;
    private array $prescribed_medications;

    public function __construct(string $number, string $doctor, string $patient, DateTime $date) {
        $this->number = $number;
        $this->doctor = $doctor;
        $this->patient = $patient;
        $this->date = $date;
        $this->prescribed_medications = [];
    }

    public function addMedication(Medication $medication, int $quantity): void {
        $this->prescribed_medications[] = [
            'medication' => $medication,
            'quantity' => $quantity
        ];
    }

    public function getPrescribedMedications(): array {
        return $this->prescribed_medications;
    }
}

class Pharmacy {
    private array $medication_stock = [];

    public function addMedication(Medication $medication): void {
        $this->medication_stock[$medication->getReference()] = $medication;
    }

    public function preparePrescription(Prescription $p): void {
        $requested_medications = $p->getPrescribedMedications();
        
        foreach ($requested_medications as $item) {
            $med = $item['medication'];
            $qty = $item['quantity'];
            
            if (!isset($this->medication_stock[$med->getReference()])) {
                throw new Exception("Medication not available in pharmacy.");
            }
            
            $medInStock = $this->medication_stock[$med->getReference()];
            
            if ($medInStock->getStock() < $qty) {
                throw new Exception("Insufficient stock for item: " . $med->getName());
            }
            
            if ($medInStock->isExpired()) {
                throw new Exception("Item " . $med->getName() . " is expired.");
            }
        }

        foreach ($requested_medications as $item) {
            $medInStock = $this->medication_stock[$item['medication']->getReference()];
            $medInStock->dispense($item['quantity'], true);
        }
    }
}