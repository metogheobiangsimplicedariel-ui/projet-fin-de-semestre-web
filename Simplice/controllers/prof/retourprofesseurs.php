<?php
// index.php (extrait)
session_start();

require_once 'config/database.php';
require_once 'models/Professeur.php';
require_once 'controllers/ProfesseurController.php';

$action = $_GET['action'] ?? 'dashboard';
$controller = new ProfesseurController();

switch ($action) {
    case 'dashboard':
        $controller->dashboard();
        break;
        
    case 'matieres':
        $controller->matieres();
        break;
        
    case 'saisie':
        $controller->saisieNotes();
        break;
        
    case 'sauvegarder_note':
        $controller->sauvegarderNote();
        break;
        
    case 'import':
        $controller->importerNotes();
        break;
        
    case 'resultats':
        $controller->visualiserResultats();
        break;
        
    case 'valider':
        $controller->validerSaisie();
        break;
        
    default:
        header('HTTP/1.0 404 Not Found');
        echo 'Page non trouvée';
        break;
}