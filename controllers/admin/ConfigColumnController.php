<?php

namespace Controllers\Admin;

use Models\ColumnConfig;
use Models\Period;
use Models\Subject;

class ConfigColumnController
{
    public function __construct()
    {
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    // Affichage de la page de configuration
    public function index()
    {
        if (!isset($_GET['periode'])) {
            header('Location: index.php?page=admin_periods');
            exit();
        }

        $periode_id = (int)$_GET['periode'];

        // On utilise les modèles existants pour récupérer les infos
        // (Si Period::getById n'existe pas, créez-la dans le modèle Period)
        $periodeModel = new Period();
        $periode = $periodeModel->getById($periode_id);

        if (!$periode) {
            header('Location: index.php?page=admin_periods');
            exit();
        }

        $subjectModel = new Subject();
        $matieres = $subjectModel->getAllWithFiliere();

        // Matière sélectionnée par défaut (la première ou celle de l'URL)
        $current_matiere_id = isset($_GET['matiere']) ? (int)$_GET['matiere'] : ($matieres[0]['id'] ?? 0);

        // Trouver l'objet matière courant pour l'affichage du titre
        $current_matiere = null;
        foreach ($matieres as $m) {
            if ($m['id'] === $current_matiere_id) {
                $current_matiere = $m;
                break;
            }
        }

        // Récupérer les colonnes via le nouveau modèle
        $configModel = new ColumnConfig();
        $colonnes = $configModel->getAll($periode_id, $current_matiere_id);

        require 'views/admin/periods/config_columns.php';
    }

    // Action : Ajouter
    public function store()
    {
        $this->checkPost();
        $configModel = new ColumnConfig();

        $res = $configModel->create(
            (int)$_GET['matiere'], // On suppose que l'ID matière est passé dans l'URL ou un champ hidden
            (int)$_GET['periode'],
            $_POST['nom_colonne'],
            $_POST['code_colonne'],
            $_POST['type'],
            $_POST['note_max'],
            $_POST['coefficient'],
            isset($_POST['obligatoire'])
        );

        if ($res === 'duplicate_code') {
            $_SESSION['error'] = "Ce code colonne existe déjà.";
        } elseif ($res === true) {
            $_SESSION['success'] = "Colonne ajoutée !";
        } else {
            $_SESSION['error'] = "Erreur : " . $res;
        }

        $this->redirectBack();
    }

    // Action : Modifier
    public function update()
    {
        $this->checkPost();
        $configModel = new ColumnConfig();
        $id = (int)$_POST['column_id'];

        if ($configModel->hasNotes($id)) {
            $_SESSION['error'] = "Impossible de modifier : des notes existent déjà.";
        } else {
            $configModel->update(
                $id,
                $_POST['nom_colonne'],
                $_POST['code_colonne'],
                $_POST['type'],
                $_POST['note_max'],
                $_POST['coefficient'],
                isset($_POST['obligatoire'])
            );
            $_SESSION['success'] = "Modification enregistrée.";
        }
        $this->redirectBack();
    }

    // Action : Supprimer
    public function delete()
    {
        $this->checkPost();
        $configModel = new ColumnConfig();
        $id = (int)$_POST['config_id'];

        if ($configModel->hasNotes($id)) {
            $_SESSION['error'] = "Suppression impossible : des notes sont liées.";
        } else {
            $configModel->delete($id);
            $_SESSION['success'] = "Colonne supprimée.";
        }
        $this->redirectBack();
    }

    // Action : Dupliquer
    public function duplicate()
    {
        $this->checkPost();
        $configModel = new ColumnConfig();

        $count = $configModel->duplicate(
            (int)$_POST['source_matiere_id'],
            (int)$_GET['matiere'], // Cible (matière courante)
            (int)$_GET['periode']
        );

        if ($count > 0) $_SESSION['success'] = "$count colonnes copiées.";
        else $_SESSION['error'] = "Aucune colonne copiée (source vide ou doublons).";

        $this->redirectBack();
    }

    // Action : Réorganiser (AJAX)
    public function reorder()
    {
        if (isset($_POST['order']) && is_array($_POST['order'])) {
            (new ColumnConfig())->reorder($_POST['order']);
            echo json_encode(['status' => 'success']);
            exit;
        }
    }

    // Helpers privés
    private function checkPost()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=admin_periods');
            exit;
        }
    }

    private function redirectBack()
    {
        $p = $_GET['periode'] ?? 0;
        $m = $_GET['matiere'] ?? 0;
        header("Location: index.php?page=admin_config_cols&periode=$p&matiere=$m");
        exit;
    }
}
