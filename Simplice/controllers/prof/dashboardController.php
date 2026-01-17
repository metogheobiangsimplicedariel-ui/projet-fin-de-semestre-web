<?php

namespace Controllers\Prof;

use Models\Period;
use Models\Assignment;
use Models\ColumnConfig;

class DashboardController
{
    public function __construct()
    {
        // 1. SÉCURITÉ : Vérification du rôle Professeur
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'professeur') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function index()
    {
        $prof_id = $_SESSION['auth']['id'];

        // Instanciation des modèles
        $periodModel = new Period();
        $assignmentModel = new Assignment(); // Nouveau modèle (voir plus bas)
        $columnModel = new ColumnConfig();

        // 2. RÉCUPÉRER TOUTES LES PÉRIODES
        // On suppose que votre modèle Period a une méthode getAll() triée par date DESC
        $toutes_les_periodes = $periodModel->getAll();

        if (empty($toutes_les_periodes)) {
            die("Aucune période configurée dans le système.");
        }

        // 3. DÉTERMINER LA PÉRIODE ACTIVE (Algorithme de priorité)
        $periode = null;

        // A. Choix explicite via URL
        if (isset($_GET['periode'])) {
            foreach ($toutes_les_periodes as $p) {
                if ($p['id'] == $_GET['periode']) {
                    $periode = $p;
                    break;
                }
            }
        }

        // B. Recherche intelligente par défaut
        if (!$periode) {
            // Priorité 1 : Période OUVERTE
            foreach ($toutes_les_periodes as $p) {
                if ($p['statut'] === 'ouverte') { // Assurez-vous que le champ est bien 'statut' ou 'actif' selon votre BDD
                    $periode = $p;
                    break;
                }
            }

            // Priorité 2 : Dernière période où le prof a une affectation
            if (!$periode) {
                $lastActiveId = $assignmentModel->getLastPeriodIdForProf($prof_id);
                if ($lastActiveId) {
                    foreach ($toutes_les_periodes as $p) {
                        if ($p['id'] == $lastActiveId) {
                            $periode = $p;
                            break;
                        }
                    }
                }
            }

            // Priorité 3 : La plus récente
            if (!$periode) {
                $periode = $toutes_les_periodes[0];
            }
        }

        // 4. RÉCUPÉRATION DES MATIÈRES pour ce prof et cette période
        // On déplace la grosse requête SQL dans le modèle Assignment
        $mes_matieres = $assignmentModel->getSubjectsForProf($prof_id, $periode['id']);

        // 5. RÉCUPÉRATION DES COLONNES (Structure des notes)
        foreach ($mes_matieres as &$matiere) {
            // On utilise le modèle existant ColumnConfig
            $matiere['colonnes'] = $columnModel->getAll($periode['id'], $matiere['id']);
        }
        unset($matiere); // Bon réflexe à garder après une référence

        // Appel de la vue
        require 'views/prof/dashboard.php';
    }
}
