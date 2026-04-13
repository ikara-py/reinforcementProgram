<?php

abstract class VehiculeLivraison {
    public string $id;
    public string $immatriculation;
    public string $chauffeur;
    public float $chargeMaxKg;
    public float $volumeMaxM3;
    public bool $disponible;
    public string $positionActuelle;

    public function __construct(string $immatriculation, string $chauffeur, float $chargeMaxKg, float $volumeMaxM3) {
        $this->id = uniqid();
        $this->immatriculation = $immatriculation;
        $this->chauffeur = $chauffeur;
        $this->chargeMaxKg = $chargeMaxKg;
        $this->volumeMaxM3 = $volumeMaxM3;
        $this->disponible = true;
        $this->positionActuelle = "Base";
    }

    abstract public function calculerCoutLivraison(float $distanceKm, float $poidsKg): float;
    abstract public function getTypevehicule(): string;

    public function peutTransporter(float $poidsKg, float $volumeM3): bool {
        return $poidsKg <= $this->chargeMaxKg && $volumeM3 <= $this->volumeMaxM3;
    }

    public function toString(): string {
        return sprintf(
            "%s [%s] - %s | Dispo: %s | Charge: %.2fkg/%.2fm3",
            $this->getTypevehicule(),
            $this->immatriculation,
            $this->chauffeur,
            $this->disponible ? 'Oui' : 'Non',
            $this->chargeMaxKg,
            $this->volumeMaxM3
        );
    }
}

class Scooter extends VehiculeLivraison {
    public function calculerCoutLivraison(float $distanceKm, float $poidsKg): float {
        $baseCout = max(25.0, $distanceKm * 5.0);
        if ($poidsKg > 15.0) {
            $baseCout *= 1.10;
        }
        return $baseCout;
    }

    public function getTypevehicule(): string {
        return 'Scooter';
    }
}

class Camionnette extends VehiculeLivraison {
    public function calculerCoutLivraison(float $distanceKm, float $poidsKg): float {
        $baseCout = max(50.0, $distanceKm * 3.5);
        if ($poidsKg > ($this->chargeMaxKg * 0.8)) {
            $baseCout *= 1.15;
        }
        return $baseCout;
    }

    public function getTypevehicule(): string {
        return 'Camionnette';
    }
}

class PoidLourd extends VehiculeLivraison {
    public function calculerCoutLivraison(float $distanceKm, float $poidsKg): float {
        $baseCout = max(200.0, $distanceKm * 2.2);
        $coutAvecCarburant = $baseCout * 1.08;
        
        if ($distanceKm > 100.0) {
            $coutAvecCarburant += 30.0;
        }
        return $coutAvecCarburant;
    }

    public function getTypevehicule(): string {
        return 'PoidLourd';
    }
}

class DispatcheurLivraisons {
    public array $vehicules = [];

    public function ajouterVehicule(VehiculeLivraison $v): void {
        $this->vehicules[] = $v;
    }

    public function trouverMeilleurVehicule(float $distanceKm, float $poidsKg, float $volumeM3): ?VehiculeLivraison {
        $meilleurVehicule = null;
        $meilleurCout = PHP_FLOAT_MAX;

        foreach ($this->vehicules as $vehicule) {
            if ($vehicule->disponible && $vehicule->peutTransporter($poidsKg, $volumeM3)) {
                $coutActuel = $vehicule->calculerCoutLivraison($distanceKm, $poidsKg);
                if ($coutActuel < $meilleurCout) {
                    $meilleurCout = $coutActuel;
                    $meilleurVehicule = $vehicule;
                }
            }
        }

        return $meilleurVehicule;
    }

    public function planifierLivraison(string $destination, float $distanceKm, float $poidsKg, float $volumeM3): ?array {
        $vehicule = $this->trouverMeilleurVehicule($distanceKm, $poidsKg, $volumeM3);
        
        if ($vehicule === null) {
            return null;
        }

        return [
            'vehicule' => $vehicule,
            'cout' => $vehicule->calculerCoutLivraison($distanceKm, $poidsKg),
            'type' => $vehicule->getTypevehicule()
        ];
    }

    public function getCoutsParType(): array {
        $couts = [];
        $typesVus = [];

        foreach ($this->vehicules as $vehicule) {
            $type = $vehicule->getTypevehicule();
            if (!in_array($type, $typesVus)) {
                if ($vehicule->peutTransporter(50.0, 0.5)) {
                    $couts[$type] = $vehicule->calculerCoutLivraison(100.0, 50.0);
                } else {
                    $couts[$type] = "Impossible";
                }
                $typesVus[] = $type;
            }
        }

        return $couts;
    }
}

$dispatcheur = new DispatcheurLivraisons();

$dispatcheur->ajouterVehicule(new Scooter("1-SC-001", "Ali K.", 25, 0.08));
$dispatcheur->ajouterVehicule(new Scooter("1-SC-002", "Omar M.", 25, 0.08));
$dispatcheur->ajouterVehicule(new Camionnette("2-CM-010", "Rachid B.", 800, 4.0));
$dispatcheur->ajouterVehicule(new Camionnette("2-CM-011", "Samir A.", 1000, 5.5));
$dispatcheur->ajouterVehicule(new PoidLourd("3-PL-100", "Hassan D.", 12000, 40.0));

$l1 = $dispatcheur->planifierLivraison("Hay Hassani", 8, 5, 0.03);
if ($l1) {
    echo "Livraison 1 : " . $l1['type'] . " | " . $l1['cout'] . " DH" . PHP_EOL;
}

$l2 = $dispatcheur->planifierLivraison("Marrakech", 200, 5000, 15.0);
if ($l2) {
    echo "Livraison 2 : " . $l2['type'] . " | " . $l2['cout'] . " DH" . PHP_EOL;
}

print_r($dispatcheur->getCoutsParType());