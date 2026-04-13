<?php

class Medicament {
    public $id;
    public $nom;
    public $prixUnitaire;
    public $stockActuel;
    public $stockMinimum;
    public $surOrdonnance;

    public function __construct(string $nom, float $prixUnitaire, int $stockActuel, int $stockMinimum, bool $surOrdonnance) {
        $this->id = uniqid();
        $this->nom = $nom;
        $this->prixUnitaire = $prixUnitaire;
        $this->stockActuel = $stockActuel;
        $this->stockMinimum = $stockMinimum;
        $this->surOrdonnance = $surOrdonnance;
    }

    public function approvisionner(int $quantite): void {
        if ($quantite > 0) {
            $this->stockActuel += $quantite;
        }
    }

    public function estEnRupture(): bool {
        return $this->stockActuel === 0;
    }

    public function estEnStockCritique(): bool {
        return $this->stockActuel > 0 && $this->stockActuel <= $this->stockMinimum;
    }

    public function toString(): string {
        return sprintf("%s | Prix: %.2f DH | Stock: %d unites", $this->nom, $this->prixUnitaire, $this->stockActuel);
    }
}

class LignePrescription {
    public $medicament;
    public $quantitePrescrite;
    public $posologie;

    public function __construct(Medicament $medicament, int $quantitePrescrite, string $posologie) {
        $this->medicament = $medicament;
        $this->quantitePrescrite = $quantitePrescrite;
        $this->posologie = $posologie;
    }
}

class Ordonnance {
    public $id;
    public $nomPatient;
    public $medecin;
    public $date;
    public $lignes = [];
    public $dispensee = false;

    public function __construct(string $nomPatient, string $medecin) {
        $this->id = uniqid();
        $this->nomPatient = $nomPatient;
        $this->medecin = $medecin;
        $this->date = date('Y-m-d H:i:s');
    }

    public function ajouterMedicament(Medicament $med, int $quantite, string $posologie): void {
        foreach ($this->lignes as $ligne) {
            if ($ligne->medicament->id === $med->id) {
                throw new LogicException("Ce medicament est deja present dans l'ordonnance.");
            }
        }
        $this->lignes[] = new LignePrescription($med, $quantite, $posologie);
    }

    public function calculerCout(): float {
        $totalBrut = 0.0;
        foreach ($this->lignes as $ligne) {
            $totalBrut += $ligne->medicament->prixUnitaire * $ligne->quantitePrescrite;
        }
        return $totalBrut * 0.30;
    }

    public function dispenser(Pharmacie $pharmacie): array {
        if ($this->dispensee) {
            return ['succes' => false, 'message' => "L'ordonnance a deja ete dispensee.", 'manquants' => []];
        }

        $manquants = [];
        foreach ($this->lignes as $ligne) {
            $med = $ligne->medicament;
            if ($med->stockActuel < $ligne->quantitePrescrite) {
                $manquants[] = $med->nom;
            }
        }

        if (!empty($manquants)) {
            return [
                'succes' => false, 
                'message' => "Stock insuffisant pour certains medicaments.", 
                'manquants' => $manquants
            ];
        }

        foreach ($this->lignes as $ligne) {
            $ligne->medicament->stockActuel -= $ligne->quantitePrescrite;
        }

        $this->dispensee = true;
        return ['succes' => true, 'message' => "Ordonnance dispensee avec succes.", 'manquants' => []];
    }
}

class Pharmacie {
    public $nom;
    public $medicaments = [];

    public function __construct(string $nom) {
        $this->nom = $nom;
    }

    public function ajouterMedicament(Medicament $med): void {
        $this->medicaments[] = $med;
    }

    public function rechercherMedicament(string $nom): ?Medicament {
        foreach ($this->medicaments as $med) {
            if (strtolower($med->nom) === strtolower($nom)) {
                return $med;
            }
        }
        return null;
    }

    public function getMedicamentsEnRupture(): array {
        return array_filter($this->medicaments, function($med) {
            return $med->estEnRupture();
        });
    }

    public function getMedicamentsEnStockCritique(): array {
        return array_filter($this->medicaments, function($med) {
            return $med->estEnStockCritique();
        });
    }

    public function valeurStockTotal(): float {
        $total = 0.0;
        foreach ($this->medicaments as $med) {
            $total += $med->prixUnitaire * $med->stockActuel;
        }
        return $total;
    }
}

$pharmacie = new Pharmacie("Pharmacie Centrale");

$paracetamol = new Medicament("Paracetamol 500mg", 12.50, 80, 15, false);
$amoxicilline = new Medicament("Amoxicilline 500mg", 35.00, 8, 20, true);
$ibuprofene = new Medicament("Ibuprofene 400mg", 18.00, 0, 10, false);
$metformine = new Medicament("Metformine 850mg", 28.00, 3, 15, true);

$pharmacie->ajouterMedicament($paracetamol);
$pharmacie->ajouterMedicament($amoxicilline);
$pharmacie->ajouterMedicament($ibuprofene);
$pharmacie->ajouterMedicament($metformine);

echo "=== Etat du stock ===" . PHP_EOL;
echo "Ruptures : " . count($pharmacie->getMedicamentsEnRupture()) . PHP_EOL;
echo "Critiques : " . count($pharmacie->getMedicamentsEnStockCritique()) . PHP_EOL;
echo "Valeur stock : " . $pharmacie->valeurStockTotal() . " DH" . PHP_EOL;

$ordonnance = new Ordonnance("Alami Hassan", "Dr. Benali");
$ordonnance->ajouterMedicament($paracetamol, 2, "1 comprime toutes les 8h");
$ordonnance->ajouterMedicament($amoxicilline, 1, "1 gelule 3 fois par jour");

echo PHP_EOL . "=== Ordonnance ===" . PHP_EOL;
echo "Reste a charge : " . $ordonnance->calculerCout() . " DH" . PHP_EOL;

$resultat = $ordonnance->dispenser($pharmacie);
echo "Dispensation : " . ($resultat['succes'] ? 'OK' : 'ECHEC') . PHP_EOL;
if (!$resultat['succes']) echo "Message : " . $resultat['message'] . PHP_EOL;