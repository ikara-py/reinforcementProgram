<?php

abstract class Notification {
    public $id;
    public $destinataire;
    public $contenu;
    public $dateEnvoi;
    public $statut;

    public function __construct(string $destinataire, string $contenu) {
        $this->id = uniqid();
        $this->destinataire = $destinataire;
        $this->contenu = $contenu;
        $this->statut = 'en_attente';
        $this->dateEnvoi = null;
    }

    abstract public function envoyer(): bool;

    public function getResume(): string {
        $type = get_class($this);
        $contenuAbrege = substr($this->contenu, 0, 30);
        return sprintf("[%s] A: %s | Statut: %s | %s", $type, $this->destinataire, $this->statut, $contenuAbrege);
    }

    protected function marquerEnvoye(): void {
        $this->statut = 'envoye';
        $this->dateEnvoi = date('Y-m-d H:i:s');
    }

    protected function marquerEchec(string $raison): void {
        $this->statut = 'echec';
    }
}

class EmailNotification extends Notification {
    public $expediteur;
    public $sujet;
    public $piecesJointes = [];

    public function __construct(string $destinataire, string $sujet, string $contenu, string $expediteur, string $titreExtra = '') {
        parent::__construct($destinataire, $contenu);
        $this->sujet = $sujet;
        $this->expediteur = $expediteur;
    }

    public function envoyer(): bool {
        if (filter_var($this->destinataire, FILTER_VALIDATE_EMAIL)) {
            $this->marquerEnvoye();
            return true;
        }
        $this->marquerEchec("Format d'email invalide");
        return false;
    }

    public function ajouterPieceJointe(string $fichier): void {
        $this->piecesJointes[] = $fichier;
    }
}

class SmsNotification extends Notification {
    public $numeroTelephone;

    public function __construct(string $numeroTelephone, string $contenu) {
        parent::__construct($numeroTelephone, $contenu);
        $this->numeroTelephone = $numeroTelephone;
    }

    public function envoyer(): bool {
        if (preg_match('/^(06|07)[0-9]{8}$/', $this->numeroTelephone) && strlen($this->contenu) <= 160) {
            $this->marquerEnvoye();
            return true;
        }
        $this->marquerEchec("Numero invalide ou contenu trop long");
        return false;
    }
}

class PushNotification extends Notification {
    public $tokenAppareil;
    public $titre;
    public $donnees = [];

    public function __construct(string $tokenAppareil, string $titre, string $contenu, string $destinataire = 'Appareil Inconnu') {
        parent::__construct($destinataire, $contenu);
        $this->tokenAppareil = $tokenAppareil;
        $this->titre = $titre;
    }

    public function envoyer(): bool {
        if (!empty($this->tokenAppareil)) {
            $this->marquerEnvoye();
            return true;
        }
        $this->marquerEchec("Token d'appareil manquant");
        return false;
    }
}

class NotificationService {
    public $notifications = [];

    public function ajouter(Notification $n): void {
        $this->notifications[] = $n;
    }

    public function envoyerToutes(): array {
        $envoyes = 0;
        $echecs = 0;
        $details = [];

        foreach ($this->notifications as $n) {
            if ($n->statut === 'en_attente') {
                if ($n->envoyer()) {
                    $envoyes++;
                } else {
                    $echecs++;
                }
                $details[] = $n->getResume();
            }
        }

        return [
            'envoyes' => $envoyes,
            'echecs' => $echecs,
            'details' => $details
        ];
    }

    public function getParStatut(string $statut): array {
        return array_filter($this->notifications, function($n) use ($statut) {
            return $n->statut === $statut;
        });
    }

    public function statistiques(): array {
        $stats = [
            'total' => count($this->notifications),
            'envoyes' => 0,
            'echecs' => 0,
            'en_attente' => 0,
            'parType' => [
                'EmailNotification' => 0,
                'SmsNotification' => 0,
                'PushNotification' => 0
            ]
        ];

        foreach ($this->notifications as $n) {
            $stats[$n->statut]++;
            
            $type = get_class($n);
            if (!isset($stats['parType'][$type])) {
                $stats['parType'][$type] = 0;
            }
            $stats['parType'][$type]++;
        }

        return $stats;
    }
}

$service = new NotificationService();

$email1 = new EmailNotification('h.alami@email.ma', 'Confirmation commande', 'Votre commande CMD-001 a bien ete validee.', 'noreply@shop.ma', 'Confirmation commande #CMD-001');

$email2 = new EmailNotification('email-invalide', 'Test', 'Ce message ne sera pas envoye.', 'noreply@shop.ma', 'Test');

$sms1 = new SmsNotification('0612345678', 'Votre code OTP est : 482917. Valable 5 minutes.');

$sms2 = new SmsNotification('0512345678', 'Test SMS');

$push1 = new PushNotification('device_token_abc123', 'Nouvelle commande !', "La commande CMD-002 vient d'etre passee.", 'Notification');

$service->ajouter($email1);
$service->ajouter($email2);
$service->ajouter($sms1);
$service->ajouter($sms2);
$service->ajouter($push1);

$resultats = $service->envoyerToutes();
echo "Envoyes : " . $resultats['envoyes'] . PHP_EOL;
echo "Echecs : " . $resultats['echecs'] . PHP_EOL;

print_r($service->statistiques());