<?php

namespace Controllers\Prof;

use Models\MatiereModel;
use Models\EtudiantModel;
use Models\ColonneModel;
use Models\NoteModel;
use Models\Period;

class ProfesseurController
{
    public function __construct()
    {
        // Sécurité : Vérifie que l'utilisateur est connecté et est un professeur
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'professeur') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function mesMatieres()
    {
        $professeurId = $_SESSION['auth']['id'];
        $db = \Config\Database::getConnection();

        // 1. Récupérer toutes les périodes pour le menu déroulant
        $stmt = $db->query("SELECT * FROM periodes ORDER BY date_debut_saisie DESC");
        $toutes_les_periodes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Déterminer la période sélectionnée
        $periode = null;
        if (isset($_GET['periode'])) {
            foreach ($toutes_les_periodes as $p) {
                if ($p['id'] == $_GET['periode']) {
                    $periode = $p;
                    break;
                }
            }
        }

        // Si aucune période choisie, on prend la plus récente "ouverte"
        if (!$periode) {
            foreach ($toutes_les_periodes as $p) {
                if ($p['statut'] === 'ouverte') {
                    $periode = $p;
                    break;
                }
            }
        }
        // Sinon la première de la liste
        if (!$periode && !empty($toutes_les_periodes)) {
            $periode = $toutes_les_periodes[0];
        }

        // 3. Récupérer les matières POUR CETTE PÉRIODE
        $mes_matieres = [];
        if ($periode) {
            $matiereModel = new MatiereModel();

            // On vérifie si la méthode existe (votre ancien ou nouveau modèle)
            if (method_exists($matiereModel, 'getMatieresProfesseurPourPeriode')) {
                $mes_matieres = $matiereModel->getMatieresProfesseurPourPeriode($professeurId, $periode['id']);
            } else {
                // Requête de secours si la méthode n'existe pas dans le modèle
                $sql = "SELECT m.*, f.code as filiere_code, f.nom as filiere_nom 
                        FROM affectations_profs ap
                        JOIN matieres m ON ap.matiere_id = m.id
                        JOIN filieres f ON m.filiere_id = f.id
                        WHERE ap.professeur_id = ? AND ap.periode_id = ?";
                $stmt = $db->prepare($sql);
                $stmt->execute([$professeurId, $periode['id']]);
                $mes_matieres = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            }
        }

        require 'views/prof/mes_matieres.php';
    }

    public function saisieNotes()
    {
        // 1. Validation
        if (!isset($_GET['matiere_id']) || !isset($_GET['periode'])) {
            header('Location: index.php?page=prof_dashboard');
            exit();
        }

        $matiereId = (int)$_GET['matiere_id'];
        $periodeId = (int)$_GET['periode'];

        // 2. Modèles
        $etudiantModel = new EtudiantModel();
        $colonneModel  = new ColonneModel();
        $noteModel     = new NoteModel();
        $periodModel   = new Period();

        // 3. Vérifier le statut de la période (LECTURE SEULE ?)
        $periodeInfo = $periodModel->getById($periodeId);
        // Si la période n'est pas 'ouverte', on passe en mode lecture seule
        $is_readonly = ($periodeInfo['statut'] !== 'ouverte');

        // 4. Récupération des données
        $etudiants = $etudiantModel->getEtudiantsByMatiere($matiereId, $periodeId);
        $colonnes  = $colonneModel->getColonnesByMatiereEtPeriode($matiereId, $periodeId);
        $rawNotes  = $noteModel->getNotesByMatiere($matiereId);

        $notesExistantes = [];
        foreach ($rawNotes as $n) {
            $notesExistantes[$n['etudiant_id']][$n['colonne_id']] = [
                'valeur'      => $n['valeur'],
                'date_saisie' => $n['date_saisie'] ?? null
            ];
        }

        // 5. On envoie $is_readonly à la vue
        require 'views/prof/saisie_notes.php';
    }
}
