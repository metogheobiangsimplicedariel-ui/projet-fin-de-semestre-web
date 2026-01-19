<?php
// web/index.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// 1. Démarrage de la session
session_start();

// 2. Génération du Jeton CSRF de sécurité (s'il n'existe pas)
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// 3. Autoloader Amélioré (Gère la casse des dossiers)
spl_autoload_register(function ($class) {
    // Transforme le Namespace (ex: Controllers\Admin\User) en chemin de fichier
    // On force 'controllers' et 'models' en minuscule pour correspondre à vos dossiers réels
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    // Astuce : On vérifie d'abord le chemin tel quel (Respect de la casse)
    $file = __DIR__ . DIRECTORY_SEPARATOR . $classPath . '.php';

    // Si pas trouvé, on essaie en mettant la première lettre du dossier en minuscule (Controllers -> controllers)
    if (!file_exists($file)) {
        $parts = explode('\\', $class);
        if (isset($parts[0])) {
            $parts[0] = strtolower($parts[0]); // Ex: Controllers -> controllers
        }
        $lowerPath = implode(DIRECTORY_SEPARATOR, $parts);
        $file = __DIR__ . DIRECTORY_SEPARATOR . $lowerPath . '.php';
    }

    if (file_exists($file)) {
        require_once $file;
    } else {
        // Optionnel : Décommentez pour débuguer si une classe est introuvable
        // echo "Erreur Autoload : Impossible de charger $class (Chemin testé : $file)<br>";
    }
});

// 4. Routeur Principal
$page = $_GET['page'] ?? 'login';

// Protection globale : Si on essaie d'accéder à une page admin/prof sans être connecté
$public_pages = ['login', 'login_verify', 'register', 'register_verify', 'logout'];
if (!in_array($page, $public_pages) && !isset($_SESSION['auth'])) {
    header('Location: index.php?page=login');
    exit();
}

switch ($page) {
    // --- AUTHENTIFICATION ---
    case 'login':
        require 'views/auth/login.php';
        break;

    case 'login_verify':
        (new Controllers\Auth\AuthController())->login();
        break;

    case 'register':
        require 'views/auth/register.php'; // Votre vue d'inscription
        break;

    case 'register_verify':
        (new Controllers\Auth\AuthController())->registerVerify();
        break;

    case 'logout':
        (new Controllers\Auth\AuthController())->logout();
        break;
    case 'dashboard':
        // C'est ici qu'on branche le nouveau contrôleur
        (new Controllers\Student\StudentController())->dashboard();
        break;
    case 'student_download_bulletin':
        (new Controllers\Student\StudentController())->downloadBulletin();
        break;

    // --- ESPACE ADMIN ---
    case 'admin_dashboard':
        // Vérification de rôle faite dans le fichier ou un contrôleur dédié idéalement
        if ($_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        // APRES (Correct) : On appelle le contrôleur qui prépare les données
        (new Controllers\Admin\DashboardController())->index();
        break;

    case 'admin_users':
        $ctrl = new Controllers\Admin\UserController();
        // Gestion simple des actions via POST ou défaut sur list()
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'create_user') $ctrl->create();
            if ($_POST['action'] === 'update_user') {
                // Assurez-vous d'avoir ajouté la méthode update() dans UserController
                // Sinon, appelez une méthode générique ou gérez-le ici
                // $ctrl->update(); 
            }
            if ($_POST['action'] === 'delete_user') $ctrl->delete();
        } else {
            $ctrl->list();
        }
        break;

    case 'admin_periods':
        $ctrl = new Controllers\Admin\PeriodController();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                if ($_POST['action'] === 'create_period') {
                    $ctrl->store();
                } elseif ($_POST['action'] === 'toggle_status') {
                    $ctrl->toggleStatus();
                }
            }
        } else {
            $ctrl->index();
        }
        break;
    case 'admin_subjects':
        // C'est ici que l'erreur se produisait : Assurez-vous que le fichier SubjectController.php existe bien !
        $ctrl = new Controllers\Admin\SubjectController();
        if (isset($_POST['action']) && $_POST['action'] === 'create_subject') {
            $ctrl->store();
        } else {
            $ctrl->index();
        }
        break;

    case 'admin_assignments':
        (new Controllers\Admin\AssignmentController())->index();
        break;

    case 'admin_assignments_post':
        (new Controllers\Admin\AssignmentController())->handlePost();
        break;

    case 'admin_config_cols':
        $ctrl = new Controllers\Admin\ConfigColumnController();

        // Gestion des actions POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['action'])) {
                switch ($_POST['action']) {
                    case 'add_column':
                        $ctrl->store();
                        break;
                    case 'edit_column':
                        $ctrl->update();
                        break;
                    case 'delete_column':
                        $ctrl->delete();
                        break;
                    case 'duplicate_config':
                        $ctrl->duplicate();
                        break;
                    case 'reorder_columns':
                        $ctrl->reorder();
                        break;
                }
            }
        } else {
            // Affichage GET
            $ctrl->index();
        }
        break;
    case 'admin_formula':
        $ctrl = new Controllers\Admin\FormulaController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ctrl->update();
        } else {
            $ctrl->edit();
        }
        break;

    case 'admin_calculate':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérif CSRF et Admin
            if ($_SESSION['auth']['role'] !== 'admin') die('Accès refusé');

            $calc = new Models\Calculator();
            $count = $calc->calculateClass($_POST['matiere_id'], $_POST['periode_id']);

            $_SESSION['success'] = "$count moyennes calculées avec succès !";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        break;

    // --- ESPACE PROFESSEUR ---
    case 'prof_dashboard':
    case 'prof_matieres':
        // Sécurité
        if ($_SESSION['auth']['role'] !== 'professeur') {
            header('Location: index.php?page=login');
            exit;
        }
        // On lance le contrôleur qui prépare les données (périodes, matières...)
        (new Controllers\Prof\DashboardController())->index();
        break;


    case 'prof_saisie':
        if ($_SESSION['auth']['role'] !== 'professeur') {
            header('Location: index.php?page=login');
            exit;
        }
        // On lance le contrôleur qui gère la saisie
        (new Controllers\Prof\ProfesseurController())->saisieNotes();
        break;


    case 'admin_results':
        (new Controllers\Admin\ResultController())->index();
        break;

    case 'admin_print_pv':
        (new Controllers\Admin\PrintController())->printPV();
        break;

    case 'admin_print_bulletin':
        if ($_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit;
        }
        // Appel de la méthode printBulletin
        (new Controllers\Admin\PrintController())->printBulletin();
        break;
    case 'admin_update_periode':
        (new Controllers\Admin\ResultController())->updatePeriodeState();
        break;
    // --- DEFAUT ---
    default:
        require 'views/auth/login.php';
        break;
}
