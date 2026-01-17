<?php
// controllers/auth/loginController.php

require_once __DIR__ . '/../../config/database.php'; // Connexion BDD

class LoginController
{
    public function login()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // On instancie le Modèle (C'est lui qui parle à la BDD)
            $userModel = new User();

            // On utilise la méthode propre du Modèle
            $user = $userModel->findByEmail($email);

            // Vérification : L'user existe ET le mot de passe est bon
            // Dans votre méthode LoginController->authenticate()
            if ($user && password_verify($password, $user['mot_de_passe_hash'])) {

                $_SESSION['auth'] = [
                    'id' => $user->id,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'role' => $user->role
                ];

                // --- REDIRECTION INTELLIGENTE SELON LE RÔLE ---
                switch ($user->role) {
                    case 'admin':
                        header('Location: index.php?page=admin_dashboard');
                        break;

                    case 'professeur':
                        // On n'a pas encore créé cette page, mais on prépare le terrain
                        header('Location: index.php?page=prof_dashboard');
                        break;

                    case 'etudiant':
                    default:
                        // Par défaut, on envoie vers l'espace étudiant
                        header('Location: index.php?page=dashboard');
                        break;
                }

                exit();
                exit; // Toujours ajouter exit après une redirection header
            } else {
                $error = "Email ou mot de passe incorrect.";
            }
        }

        // Si on n'est pas en POST ou s'il y a une erreur, on affiche la vue
        require __DIR__ . '/../Views/auth/login.php';
    }

    // =========================================================
    // 2. DÉCONNEXION
    // =========================================================
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_destroy(); // On détruit la session
        header('Location: index.php?page=home');
        exit;
    }

    // =========================================================
    // 3. INSCRIPTION (Corrigée pour Nom/Prénom)
    // =========================================================
    public function register()
    {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Nettoyage et récupération des 5 champs
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $confirm = $_POST['confirm_password']; // Assurez-vous d'avoir ce champ dans la vue register
            $tel = $_POST['telephone'];
            $nom = $_POST['nom'];       // <--- AJOUTÉ
            $prenom = $_POST['prenom']; // <--- AJOUTÉ

            // Validations de base
            if ($password !== $confirm) {
                $error = "Les mots de passe ne correspondent pas.";
            } elseif (strlen($password) < 6) {
                $error = "Le mot de passe doit faire au moins 6 caractères.";
            } else {
                $userModel = new User();

                // On vérifie si l'email existe déjà
                if ($userModel->findByEmail($email)) {
                    $error = "Cet email est déjà utilisé.";
                } else {
                    // CRÉATION : On passe bien les 5 arguments attendus par User::create
                    $success = $userModel->create($email, $password, $tel, $nom, $prenom);

                    if ($success) {
                        // Redirection vers le login après succès
                        header('Location: index.php?page=login');
                        exit;
                    } else {
                        $error = "Une erreur est survenue lors de l'inscription.";
                    }
                }
            }
        }

        require __DIR__ . '/../Views/auth/register.php';
    }
}




































if (!empty($_POST)) {

    // 1. Récupération des données
    $email_posted = $_POST['email'];
    $password_posted = $_POST['passwd'];
    $errors = [];

    // 2. Vérification basique (champs vides)
    if (empty($email_posted) || empty($password_posted)) {
        $errors['login'] = "Veuillez remplir tous les champs.";
    } else {

        // 3. On cherche l'utilisateur par son EMAIL
        $req = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $req->execute([':email' => $email_posted]);

        $user = $req->fetch(); // On récupère l'utilisateur sous forme d'objet

        // 4. LA VÉRIFICATION MAGIQUE
        // On vérifie deux choses en même temps :
        // A. Est-ce qu'on a trouvé un utilisateur ? ($user)
        // B. Est-ce que le mot de passe correspond au Hash ? (password_verify)
        if ($user && password_verify($password_posted, $user->mot_de_passe)) {


            // On stocke les infos importantes dans la session
            $_SESSION['auth'] = [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role
            ];

            // --- REDIRECTION INTELLIGENTE SELON LE RÔLE ---
            switch ($user->role) {
                case 'admin':
                    header('Location: index.php?page=admin_dashboard');
                    break;

                case 'professeur':
                    // On n'a pas encore créé cette page, mais on prépare le terrain
                    header('Location: index.php?page=prof_dashboard');
                    break;

                case 'etudiant':
                default:
                    // Par défaut, on envoie vers l'espace étudiant
                    header('Location: index.php?page=dashboard');
                    break;
            }

            exit();
        } else {
            // --- ÉCHEC ---
            // Sécurité : On affiche un message flou. 
            // Ne dites PAS "Mot de passe faux", dites "Identifiants incorrects".
            // Cela empêche les pirates de savoir si l'email existe ou non.
            $errors['login'] = "Email ou mot de passe incorrect.";
        }
    }

    // 5. Gestion des erreurs
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input_login'] = ['email' => $email_posted]; // Pour remettre l'email
        header('Location: index.php?page=login');
        exit();
    }
}
