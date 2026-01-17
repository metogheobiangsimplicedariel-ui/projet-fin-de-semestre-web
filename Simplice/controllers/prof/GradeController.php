<?php

namespace Controllers\Prof;

use Models\Grade;
use Models\ColumnConfig;
use Models\Subject;
use Models\Period;

class GradeController
{
    public function __construct()
    {
        // Sécurité Professeur
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'professeur') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    // Affiche la grille de saisie
    public function entry()
    {
        if (!isset($_GET['matiere']) || !isset($_GET['periode'])) {
            // Rediriger vers le dashboard prof si pas de params
            header('Location: index.php?page=prof_dashboard');
            exit();
        }

        $matiere_id = (int)$_GET['matiere'];
        $periode_id = (int)$_GET['periode'];
        $prof_id = $_SESSION['auth']['id'];

        // TODO: Vérifier ici que le prof est bien affecté à cette matière (Sécurité)
        // (Pour l'instant on laisse passer pour tester)

        // 1. Infos contextuelles
        $matiere = (new Subject())->getById($matiere_id);
        $periode = (new Period())->getById($periode_id);

        // 2. Les colonnes à afficher (Dynamique !)
        $colonnes = (new ColumnConfig())->getAll($periode_id, $matiere_id);

        // 3. Les étudiants et leurs notes actuelles
        $etudiants = (new Grade())->getGradeSheet($matiere_id, $periode_id);

        require 'views/prof/grades/entry.php';
    }

    // Traitement de la sauvegarde (AJAX ou Formulaire classique)
    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérif CSRF
            // ...

            $matiere_id = $_POST['matiere_id'];
            $periode_id = $_POST['periode_id'];
            $prof_id = $_SESSION['auth']['id'];

            // Les notes arrivent sous forme de tableau : notes[etudiant_id][colonne_id]
            if (isset($_POST['notes']) && is_array($_POST['notes'])) {
                $gradeModel = new Grade();

                foreach ($_POST['notes'] as $etudiant_id => $cols) {
                    foreach ($cols as $colonne_id => $valeur) {
                        $gradeModel->save($etudiant_id, $colonne_id, $valeur, $prof_id);
                    }
                }
                $_SESSION['success'] = "Notes enregistrées avec succès.";
            }

            header("Location: index.php?page=prof_entry&periode=$periode_id&matiere=$matiere_id");
            exit();
        }
    }
}
