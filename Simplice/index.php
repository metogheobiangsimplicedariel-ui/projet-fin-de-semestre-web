<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// index.php - Votre routeur principal

$page = $_GET['page'] ?? 'login';

// 1. Définition des chemins (Bonne pratique : tout utiliser via ces variables)
$viewsAuthPath = __DIR__ . '/views/auth/';
$viewsLayoutPath = __DIR__ . '/views/layout/';
$controllerAuthPath = __DIR__ . '/controllers/auth/';

// 2. Routage
switch ($page) {
    
    // === VUES (Affichage) ===
    
    case 'login':
        $title = "Studify - Connexion";
        require $viewsAuthPath . 'login.php';
        break;
        
    case 'register':
    case 'signup': 
        $title = "Studify - Inscription";
        // Correction : On utilise la variable $viewsAuthPath ici aussi
        require $viewsAuthPath . 'register.php';
        break;

    // === CONTROLLERS (Traitements) ===

    case 'register_verify':
        // Traitement de l'inscription
        // Correction : On utilise la variable $controllerAuthPath
        require $controllerAuthPath . 'registerController.php';
        break;
    
    case 'login_verify':
        // CORRECTION IMPORTANTE : C'est ici qu'on traite la connexion
        // (Votre formulaire login pointe vers index.php?page=login_verify)
        require $controllerAuthPath . 'loginController.php';
        break;

    // === PAGES CONNECTÉES ===

    case 'dashboard':
        // C'est la page où l'on arrive après la connexion
        // Vérifiez ici si l'utilisateur est bien connecté (via $_SESSION['auth'])
        require 'views/dashboard.php';
        break;

    // 2. Espace Admin
    case 'admin_dashboard':
        require 'views/admin/dashboardController.php';
        break;

    // 3. Espace Prof
    case 'prof_dashboard':
        require 'controllers/prof/dashboardController.php';
        break;
    
    case 'logout':
        require 'controllers/auth/logoutController.php';
        break;

    case 'admin_users':
        require 'controllers/admin/usersListController.php';
        break;
    
    case 'admin_periods':
        require 'controllers/admin/periodsController.php';
        break;
    
    case 'admin_config_cols':
        require 'controllers/admin/configColumnsController.php'; 
        break;

    case 'admin_subjects':
        require 'controllers/admin/subjectsController.php';
        break;

    case 'admin_assignments':
    require 'controllers/admin/assignmentsController.php';
    break;

    // === ERREURS ===

    default:
        http_response_code(404);
        echo "<h1>Page non trouvée</h1>";
        break;
}
?>