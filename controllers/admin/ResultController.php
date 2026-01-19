<?php

namespace Controllers\Admin;

use Models\ColonneModel;
use Models\NoteModel;
use Config\Database;

class ResultController
{

    public function index()
    {
        // 1. Vérification Admin
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }

        $db = Database::getConnection();

        // 2. Variables de filtres
        // On récupère les choix de l'utilisateur ou on met des valeurs par défaut
        $matiereId = isset($_GET['matiere']) ? (int)$_GET['matiere'] : 1;
        $periodeId = isset($_GET['periode']) ? (int)$_GET['periode'] : 2;

        // 3. Récupérer les listes pour les menus déroulants (C'est ici que ça bloquait)
        $listeMatieres = $db->query("SELECT * FROM matieres ORDER BY nom ASC")->fetchAll();
        $listePeriodes = $db->query("SELECT * FROM periodes ORDER BY date_debut_saisie DESC")->fetchAll();

        // 4. Récupérer les Colonnes de la matière choisie
        $colModel = new ColonneModel();
        // Si la classe ColonneModel est dans un namespace, assurez-vous de l'utiliser correctement
        $colonnes = $colModel->getColonnesByMatiereEtPeriode($matiereId, $periodeId);

        // 5. Récupérer les notes
        $noteModel = new NoteModel();
        $rawNotes = $noteModel->getNotesByMatiere($matiereId);

        $rawNotes = $noteModel->getNotesByMatiere($matiereId);

        $notes = [];
        foreach ($rawNotes as $n) {
            // Petite sécurité supplémentaire
            if (isset($n['etudiant_id']) && isset($n['colonne_id'])) {
                $notes[$n['etudiant_id']][$n['colonne_id']] = $n['valeur'];
            }
        }

        // 6. Récupérer les étudiants et leurs moyennes
        $sql = "SELECT u.id, u.nom, u.prenom, u.email, u.numero_etudiant,
                       m.moyenne, m.decision
                FROM inscriptions_matieres im
                JOIN utilisateurs u ON im.etudiant_id = u.id
                LEFT JOIN moyennes m ON (u.id = m.etudiant_id AND m.matiere_id = ? AND m.periode_id = ?)
                WHERE im.matiere_id = ? AND im.periode_id = ?
                ORDER BY u.nom ASC";

        $stmt = $db->prepare($sql);
        $stmt->execute([$matiereId, $periodeId, $matiereId, $periodeId]);
        $etudiants = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 7. On passe les variables simples à la vue (pour garder la sélection dans les menus)
        $matiere_selected = $matiereId;
        $periode_selected = $periodeId;

        require 'views/admin/results/index.php';
    }

    public function updatePeriodeState()
    {
        if ($_SESSION['auth']['role'] !== 'admin') header('Location: index.php');

        $periodeId = (int)$_POST['periode_id'];
        $typeAction = $_POST['type_action']; // 'statut' ou 'publication'
        $valeur = $_POST['valeur'];

        $periodModel = new \Models\Period();

        if ($typeAction === 'statut') {
            // Changement Ouverture/Fermeture
            $periodModel->changerStatut($periodeId, $valeur);
            $_SESSION['success'] = ($valeur === 'ouverte')
                ? "Période OUVERTE : Les professeurs peuvent saisir."
                : "Période FERMÉE : Saisie bloquée.";
        } elseif ($typeAction === 'publication') {
            // Changement Visibilité
            $periodModel->changerPublication($periodeId, $valeur);
            $_SESSION['success'] = ($valeur == 1)
                ? "Résultats PUBLIÉS aux étudiants."
                : "Résultats MASQUÉS aux étudiants.";
        }

        header("Location: index.php?page=admin_results&periode=$periodeId");
        exit;
    }
}
