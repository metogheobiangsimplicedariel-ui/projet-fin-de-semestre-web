<?php

namespace Controllers\Prof;

use Models\MatiereModel;
use Config\Database;

class DashboardController
{

    public function __construct()
    {
        // Sécurité : Vérifier que c'est bien un prof
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'professeur') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function index()
    {
        $professeurId = $_SESSION['auth']['id'];
        $db = Database::getConnection();

        // 1. Récupérer TOUTES les périodes (pour le menu déroulant en haut)
        $stmt = $db->query("SELECT * FROM periodes ORDER BY date_debut_saisie DESC");
        $toutes_les_periodes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // 2. Déterminer quelle période afficher
        $periode = null;

        // Cas A : L'utilisateur a sélectionné une période dans la liste
        if (isset($_GET['periode']) && !empty($_GET['periode'])) {
            $periodeId = (int)$_GET['periode'];
            foreach ($toutes_les_periodes as $p) {
                if ($p['id'] == $periodeId) {
                    $periode = $p;
                    break;
                }
            }
        }

        // Cas B : Aucune sélection (ou ID invalide), on cherche la période "ouverte" active
        if (!$periode) {
            foreach ($toutes_les_periodes as $p) {
                if ($p['statut'] === 'ouverte') {
                    $periode = $p;
                    break;
                }
            }
        }

        // Cas C : Toujours rien ? On prend la plus récente tout court
        if (!$periode && !empty($toutes_les_periodes)) {
            $periode = $toutes_les_periodes[0];
        }

        // Si vraiment aucune période n'existe en base
        if (!$periode) {
            die("Aucune période configurée dans l'application.");
        }

        // 3. Récupérer les matières du prof POUR CETTE PÉRIODE PRÉCISE
        $matiereModel = new MatiereModel();
        // On utilise la méthode intelligente qui récupère aussi les infos des colonnes
        $mes_matieres = $matiereModel->getMatieresProfesseurPourPeriode($professeurId, $periode['id']);

        // 4. Charger VOTRE vue Dashboard
        // Assurez-vous que votre fichier vue s'appelle bien 'dashboard.php' dans le dossier views/prof
        require 'views/prof/mes_matieres.php';
    }
}
