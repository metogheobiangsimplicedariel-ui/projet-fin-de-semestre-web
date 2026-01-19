<?php

namespace Controllers\Admin;

use Models\Subject;

class SubjectController
{
    public function __construct()
    {
        // 1. Sécurité Admin
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    // Afficher la liste + formulaire
    public function index()
    {
        $subjectModel = new Subject();

        // Récupère les matières avec le nom de la filière
        $matieres = $subjectModel->getAllWithFiliere();

        // Récupère les filières pour le menu déroulant
        $filieres = $subjectModel->getAllFilieres();

        require 'views/admin/subjects/index.php';
    }

    // Traiter l'ajout
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Sécurité CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['error'] = "Jeton de sécurité invalide.";
                header('Location: index.php?page=admin_subjects');
                exit();
            }

            // Nettoyage
            $code = strtoupper(trim($_POST['code']));
            $nom = htmlspecialchars(trim($_POST['nom']));
            $filiere_id = (int)$_POST['filiere_id'];
            $coeff = (float)$_POST['coefficient'];
            $credits = (int)$_POST['credits'];
            $seuil = (float)$_POST['seuil_validation'];

            // Appel Modèle
            $subjectModel = new Subject();
            $res = $subjectModel->create($code, $nom, $filiere_id, $coeff, $credits, $seuil);

            if ($res === true) {
                $_SESSION['success'] = "Matière ajoutée !";
            } elseif ($res === 'duplicate') {
                $_SESSION['error'] = "Ce code matière existe déjà.";
            } else {
                $_SESSION['error'] = "Erreur lors de l'enregistrement.";
            }

            header('Location: index.php?page=admin_subjects');
            exit();
        }
    }
}
