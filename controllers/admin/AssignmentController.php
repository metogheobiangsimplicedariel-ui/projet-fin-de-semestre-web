<?php

namespace Controllers\Admin;

use Models\Period;
use Models\Subject;
use Models\Assignment;
use Models\User;

class AssignmentController
{
    public function __construct()
    {
        // 1. SÉCURITÉ
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function index()
    {
        $pdo = \Config\Database::getConnection();

        // 2. LOGIQUE DE PÉRIODE 
        if (!isset($_GET['periode'])) {
            $stmt = $pdo->query("SELECT id FROM periodes WHERE statut != 'fermee' ORDER BY date_debut_saisie DESC LIMIT 1");
            $p_id = $stmt->fetchColumn();

            if (!$p_id) {
                $stmt = $pdo->query("SELECT id FROM periodes ORDER BY date_debut_saisie DESC LIMIT 1");
                $p_id = $stmt->fetchColumn();
            }

            if ($p_id) {
                header("Location: index.php?page=admin_assignments&periode=$p_id");
                exit();
            } else {
                header('Location: index.php?page=admin_periods');
                exit();
            }
        }

        $periode_id = (int)$_GET['periode'];
        $periode = (new \Models\Period())->getById($periode_id); // J'ai ajouté le namespace \Models\ par sécurité

        // 3. LISTE DES MATIÈRES (Pour la sidebar)
        $matieres = (new \Models\Subject())->getAllWithFiliere();

        // 4. AJOUT CRUCIAL POUR LE MENU DÉROULANT
        // Sans cette ligne, votre <select> en haut de page sera vide !
        $all_periods = $pdo->query("SELECT * FROM periodes ORDER BY date_debut_saisie DESC")->fetchAll(\PDO::FETCH_ASSOC);

        // 5. MATIÈRE SÉLECTIONNÉE
        $current_matiere = null;
        $profs_affectes = [];
        $etudiants_inscrits = [];
        $all_profs = [];
        $all_students = [];

        if (isset($_GET['matiere'])) {
            $matiere_id = (int)$_GET['matiere'];
            $current_matiere = (new \Models\Subject())->getById($matiere_id);

            if ($current_matiere) {
                $assignmentModel = new \Models\Assignment();
                $profs_affectes = $assignmentModel->getAssignedProfs($matiere_id, $periode_id);
                $etudiants_inscrits = $assignmentModel->getEnrolledStudents($matiere_id, $periode_id);

                // Listes pour les Modales
                // Assurez-vous que votre modèle User a bien cette méthode getAllByRole()
                $userModel = new \Models\User();

                // Si la méthode existe dans User.php :
                $all_profs = $userModel->getAllByRole('professeur');
                $all_students = $userModel->getAllByRole('etudiant');

                // SINON, remplacez par ces requêtes directes :
                /*
            $all_profs = $pdo->query("SELECT * FROM utilisateurs WHERE role='professeur'")->fetchAll();
            $all_students = $pdo->query("SELECT * FROM utilisateurs WHERE role='etudiant'")->fetchAll();
            */
            }
        }

        require 'views/admin/assignments/index.php';
    }

    // Traitement des actions POST (Assigner, Supprimer...)
    public function handlePost()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';
            $assignmentModel = new Assignment();

            // Récupération sécurisée des IDs pour redirection
            $p_id = $_POST['periode_id'] ?? 0;
            $m_id = $_POST['matiere_id'] ?? 0;

            if ($action === 'assign_prof') {
                $assignmentModel->assignProf($_POST['prof_id'], $m_id, $p_id, $_POST['groupe']);
                $_SESSION['success'] = "Professeur affecté.";
            } elseif ($action === 'remove_prof') {
                $assignmentModel->removeProf($_POST['assignment_id']);
                $_SESSION['success'] = "Affectation supprimée.";
            } elseif ($action === 'enroll_students') {
                if (!empty($_POST['etudiants'])) {
                    foreach ($_POST['etudiants'] as $stu_id) {
                        $assignmentModel->enrollStudent($stu_id, $m_id, $p_id, $_POST['groupe']);
                    }
                    $_SESSION['success'] = "Étudiants inscrits.";
                }
            } elseif ($action === 'remove_student') {
                $assignmentModel->removeStudent($_POST['inscription_id']);
                $_SESSION['success'] = "Inscription retirée.";
            }

            // Redirection exacte pour rester sur la même page
            header("Location: index.php?page=admin_assignments&periode=$p_id&matiere=$m_id");
            exit();
        }
    }
}
