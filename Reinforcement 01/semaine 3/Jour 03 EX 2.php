<?php

abstract class Document {
    public string $id;
    public string $titre;
    public string $contenu;
    public string $auteur;
    public string $dateCreation;
    public string $dateDerniereModif;
    public string $statut;
    public int $version;

    public function __construct(string $titre = '', string $contenu = '', string $auteur = '') {
        $this->id = uniqid();
        $this->titre = $titre;
        $this->contenu = $contenu;
        $this->auteur = $auteur;
        $this->dateCreation = date('Y-m-d H:i:s');
        $this->dateDerniereModif = date('Y-m-d H:i:s');
        $this->statut = 'brouillon';
        $this->version = 1;
    }

    abstract public function valider(): bool;
    abstract public function getTypeDocument(): string;

    public function soumettre(): void {
        if ($this->statut === 'brouillon' && $this->valider()) {
            $this->statut = 'soumis';
            $this->dateDerniereModif = date('Y-m-d H:i:s');
        } else {
            throw new LogicException("Le document ne peut pas etre soumis. Il n'est pas valide ou n'est pas un brouillon.");
        }
    }

    public function approuver(string $approbateur): void {
        if ($this->statut === 'soumis') {
            $this->statut = 'approuve';
            $this->dateDerniereModif = date('Y-m-d H:i:s');
        }
    }

    public function rejeter(string $motif): void {
        if ($this->statut === 'soumis') {
            $this->statut = 'brouillon';
            $this->dateDerniereModif = date('Y-m-d H:i:s');
        }
    }

    public function archiver(): void {
        if ($this->statut === 'approuve') {
            $this->statut = 'archive';
            $this->dateDerniereModif = date('Y-m-d H:i:s');
        }
    }

    protected function incrementerVersion(): void {
        $this->version++;
        $this->dateDerniereModif = date('Y-m-d H:i:s');
    }

    public function getStatut(): string {
        return $this->statut;
    }

    public function setTitre(string $titre): void {
        $this->titre = $titre;
        $this->incrementerVersion();
    }
}

class Contrat extends Document {
    public string $partieA;
    public string $partieB;
    public string $dateDebut;
    public string $dateFin;
    public float $montant;
    public string $typeContrat;

    public function __construct(string $titre, string $contenu, string $auteur, string $partieA, string $partieB, string $dateDebut, string $dateFin, float $montant, string $typeContrat) {
        parent::__construct($titre, $contenu, $auteur);
        $this->partieA = $partieA;
        $this->partieB = $partieB;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->montant = $montant;
        $this->typeContrat = $typeContrat;
    }

    public function valider(): bool {
        return strlen($this->contenu) >= 100 
            && !empty($this->partieA) 
            && !empty($this->partieB) 
            && strtotime($this->dateDebut) < strtotime($this->dateFin) 
            && $this->montant > 0;
    }

    public function getTypeDocument(): string {
        return 'Contrat';
    }
}

class RapportMensuel extends Document {
    public int $mois;
    public int $annee;
    public string $departement;
    public array $kpis = [];
    public ?string $fichierJoint = null;

    public function setMoisAnnee(int $mois, int $annee): void {
        $this->mois = $mois;
        $this->annee = $annee;
    }

    public function setDepartement(string $departement): void {
        $this->departement = $departement;
    }

    public function ajouterKpi(string $nom, $valeur): void {
        $this->kpis[$nom] = $valeur;
    }

    public function valider(): bool {
        $anneeCourante = (int)date('Y');
        return !empty($this->titre) 
            && isset($this->mois) && $this->mois >= 1 && $this->mois <= 12 
            && isset($this->annee) && $this->annee >= 2020 && $this->annee <= $anneeCourante 
            && count($this->kpis) > 0;
    }

    public function getTypeDocument(): string {
        return 'RapportMensuel';
    }
}

class Facture extends Document {
    public string $client;
    public array $lignes = [];
    public float $tauxTVA = 0.20;
    public bool $estPayee = false;

    public function __construct(string $client, string $auteur) {
        parent::__construct('', '', $auteur);
        $this->client = $client;
    }

    public function ajouterLigne(string $desc, int $qte, float $pu): void {
        $this->lignes[] = [
            'description' => $desc,
            'quantite' => $qte,
            'prixUnitaire' => $pu
        ];
    }

    public function calculerTotalHT(): float {
        $total = 0.0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne['quantite'] * $ligne['prixUnitaire'];
        }
        return $total;
    }

    public function calculerTotalTTC(): float {
        return $this->calculerTotalHT() * (1 + $this->tauxTVA);
    }

    public function valider(): bool {
        return !empty($this->client) && count($this->lignes) > 0 && $this->calculerTotalHT() > 0;
    }

    public function getTypeDocument(): string {
        return 'Facture';
    }

    public function marquerPayee(): void {
        if ($this->statut === 'approuve') {
            $this->estPayee = true;
        }
    }
}

class GestionnaireDocuments {
    private array $documents = [];

    public function ajouter(Document $d): void {
        $this->documents[] = $d;
    }

    public function getParType(string $type): array {
        return array_filter($this->documents, function($d) use ($type) {
            return $d->getTypeDocument() === $type;
        });
    }

    public function getParStatut(string $statut): array {
        return array_filter($this->documents, function($d) use ($statut) {
            return $d->getStatut() === $statut;
        });
    }

    public function getDocumentsAApprouver(): array {
        return $this->getParStatut('soumis');
    }

    public function statistiques(): array {
        $stats = [
            'total' => count($this->documents),
            'parStatut' => [],
            'parType' => []
        ];

        foreach ($this->documents as $d) {
            $statut = $d->getStatut();
            if (!isset($stats['parStatut'][$statut])) {
                $stats['parStatut'][$statut] = 0;
            }
            $stats['parStatut'][$statut]++;

            $type = $d->getTypeDocument();
            if (!isset($stats['parType'][$type])) {
                $stats['parType'][$type] = 0;
            }
            $stats['parType'][$type]++;
        }

        return $stats;
    }
}

$gestionnaire = new GestionnaireDocuments();

$contrat = new Contrat(
    "Contrat de prestation IT", 
    "Description complete du contrat de prestation pour le developpement d'une application web.",
    "Sys Admin",
    "TechCorp SARL", 
    "Benali Consulting",
    "2024-01-01", 
    "2024-12-31",
    120000.00, 
    "Prestation"
);

$rapport = new RapportMensuel("Rapport Janvier 2024", "Bilan mensuel complet des operations", "RH Manager");
$rapport->ajouterKpi("Effectif", 145);
$rapport->ajouterKpi("Recrutements", 3);
$rapport->ajouterKpi("Departs", 1);
$rapport->setMoisAnnee(1, 2024);
$rapport->setDepartement("Ressources Humaines");

$facture = new Facture("Alami Hassan", "Dev Backend");
$facture->setTitre("Facture 2024-001");
$facture->ajouterLigne("Developpement API REST", 5, 1500);
$facture->ajouterLigne("Tests et documentation", 2, 800);

$gestionnaire->ajouter($contrat);
$gestionnaire->ajouter($rapport);
$gestionnaire->ajouter($facture);

$contrat->soumettre();
$contrat->approuver("Direction");
echo "Statut contrat : " . $contrat->getStatut() . PHP_EOL;

$facture->soumettre();
$facture->approuver("Comptabilite");
$facture->marquerPayee();
echo "Facture TTC : " . $facture->calculerTotalTTC() . " DH" . PHP_EOL;

print_r($gestionnaire->statistiques());