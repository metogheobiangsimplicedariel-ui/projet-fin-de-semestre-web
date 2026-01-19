<?php

namespace Controllers\Auth;

use Models\User;

class AuthController
{

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // 1. Vérification du jeton CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['errors']['login'] = "Erreur de sécurité : tentative de fraude détectée.";
                header('Location: index.php?page=login');
                exit();
            }

            // 2. Récupération et nettoyage des données
            // Note : On n'utilise PAS htmlspecialchars sur le mot de passe à l'entrée car cela 
            // peut modifier les caractères spéciaux et rendre password_verify caduque.
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['passwd'] ?? '';
            $errors = [];

            // 3. Appel au Modèle
            $userModel = new \Models\User();
            $user = $userModel->findByEmail($email);

            // 4. Vérification sécurisée du mot de passe
            if ($user && password_verify($password, $user['mot_de_passe'])) {

                // 5. Prévention du vol de session (Session Hijacking)
                session_regenerate_id(true);

                $_SESSION['auth'] = [
                    'id' => $user['id'],
                    'nom' => $user['nom'],
                    'prenom' => $user['prenom'],
                    'role' => $user['role']
                ];

                // 6. Redirection selon le rôle
                // 6. Redirection selon le rôle
                switch ($user['role']) {
                    case 'admin':
                        header('Location: index.php?page=admin_dashboard');
                        break;

                    case 'professeur':
                        header('Location: index.php?page=prof_matieres'); // Ou prof_dashboard selon votre choix
                        break;

                    case 'etudiant':
                        // C'est ici qu'on redirige l'étudiant vers SA page
                        header('Location: index.php?page=dashboard');
                        break;

                    default:
                        // Sécurité par défaut
                        header('Location: index.php?page=login');
                        break;
                }
                exit();
            } else {
                // Échec de l'authentification
                $_SESSION['errors']['login'] = "Identifiants incorrects.";
                $_SESSION['old_input_login'] = ['email' => $email];
                header('Location: index.php?page=login');
                exit();
            }
        }
    }

    public function logout()
    {
        session_start();
        session_destroy();
        header('Location: index.php?page=login');
        exit();
    }

    public function registerVerify()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?page=register');
            exit();
        }

        $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
        $prenom = htmlspecialchars(trim($_POST['prenom'] ?? ''));
        $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
        $password = $_POST['passwd'] ?? '';
        $confirm = $_POST['confirm_passwd'] ?? '';
        $errors = [];

        // Validations
        if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
            $errors[] = "Tous les champs sont obligatoires.";
        }
        if ($password !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        if (strlen($password) < 6) {
            $errors[] = "Le mot de passe doit contenir au moins 6 caractères.";
        }

        $userModel = new User();
        if ($userModel->findByEmail($email)) {
            $errors[] = "Cet email est déjà utilisé.";
        }

        if (empty($errors)) {
            if ($userModel->create($nom, $prenom, $email, $password)) {
                $_SESSION['success'] = "Inscription réussie ! Vous pouvez maintenant vous connecter.";
                header('Location: index.php?page=login');
                exit();
            } else {
                $errors[] = "Une erreur est survenue lors de l'inscription.";
            }
        }

        // En cas d'erreur, on stocke et on redirige
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        header('Location: index.php?page=register');
        exit();
    }
}
