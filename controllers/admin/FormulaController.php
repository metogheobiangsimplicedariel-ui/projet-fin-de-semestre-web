<?php

namespace Controllers\Admin;

use Models\Formula;
use Models\ColumnConfig;
use Models\Subject;
use Models\Period;

class FormulaController
{
    public function __construct()
    {
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function edit()
    {
        if (!isset($_GET['matiere']) || !isset($_GET['periode'])) {
            header('Location: index.php?page=admin_periods');
            exit();
        }

        $matiere_id = (int)$_GET['matiere'];
        $periode_id = (int)$_GET['periode'];

        $matiere = (new Subject())->getById($matiere_id);
        $periode = (new Period())->getById($periode_id);
        $columns = (new ColumnConfig())->getAll($periode_id, $matiere_id);
        $currentFormula = (new Formula())->get($matiere_id, $periode_id);
        $templates = (new Formula())->getTemplates();

        require 'views/admin/formulas/edit.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['error'] = "Token invalide.";
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit();
            }

            $matiere_id = (int)$_POST['matiere_id'];
            $periode_id = (int)$_POST['periode_id'];
            $formule = trim($_POST['formule']);
            $description = trim($_POST['description']);

            // Appel au Modèle (C'est ici qu'on appelle la méthode save du fichier Formula.php)
            $success = (new Formula())->save($matiere_id, $periode_id, $formule, $description);

            if ($success) {
                $_SESSION['success'] = "Formule enregistrée avec succès !";
            } else {
                $_SESSION['error'] = "Erreur lors de l'enregistrement.";
            }

            header("Location: index.php?page=admin_formula&periode=$periode_id&matiere=$matiere_id");
            exit();
        }
    }
}
