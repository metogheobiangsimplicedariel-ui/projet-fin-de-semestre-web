<?php

namespace Controllers\Admin;

use Models\Period;

class PeriodController
{
    public function __construct()
    {
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function index()
    {
        $periodModel = new Period();
        $periodes = $periodModel->getAll();

        // CORRECTION ICI : Le chemin pointe vers votre fichier index.php
        require 'views/admin/periods/index.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Sécurité CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['error'] = "Token invalide.";
                header('Location: index.php?page=admin_periods');
                exit();
            }

            // Récupération
            $nom = htmlspecialchars(trim($_POST['nom']));
            $code = strtoupper(htmlspecialchars(trim($_POST['code'])));
            $annee = htmlspecialchars(trim($_POST['annee_universitaire']));
            $debut = $_POST['date_debut_saisie'];
            $fin = $_POST['date_fin_saisie'];
            $type = $_POST['type'];

            // Validation
            if ($fin < $debut) {
                $_SESSION['error'] = "La date de fin doit être après le début.";
                header('Location: index.php?page=admin_periods');
                exit();
            }

            // Création
            $periodModel = new Period();
            $success = $periodModel->create($nom, $code, $annee, $type, $debut, $fin);

            if ($success) {
                $_SESSION['success'] = "Période créée avec succès !";
            } else {
                $_SESSION['error'] = "Erreur : Ce code existe peut-être déjà.";
            }

            header('Location: index.php?page=admin_periods');
            exit();
        }
    }

    // Dans controllers/admin/PeriodController.php

    public function toggleStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sécurité
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['error'] = "Token invalide.";
                header('Location: index.php?page=admin_periods');
                exit();
            }

            $id = (int)$_POST['period_id'];
            $newStatus = $_POST['new_status'];
            $justification = isset($_POST['justification']) ? trim($_POST['justification']) : null;

            // VÉRIFICATION DE SÉCURITÉ
            if ($newStatus === 'ouverte') {
                if (empty($justification)) {
                    $_SESSION['error'] = "Action refusée : Une justification est obligatoire pour rouvrir une période.";
                    header('Location: index.php?page=admin_periods');
                    exit();
                }
            } else {
                // Si on ferme, on vide la justification (nouvelle session propre) ou on garde l'historique selon votre choix
                $justification = null;
            }

            $periodModel = new \Models\Period();
            $periodModel->updateStatus($id, $newStatus, $justification); // On passe la justification

            $_SESSION['success'] = ($newStatus === 'ouverte')
                ? "Période réouverte (Motif enregistré)."
                : "Période verrouillée avec succès.";

            header('Location: index.php?page=admin_periods');
            exit();
        }
    }
}
