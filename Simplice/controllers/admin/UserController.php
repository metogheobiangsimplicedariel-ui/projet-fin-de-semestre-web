<?php
// controllers/admin/UserController.php
namespace Controllers\Admin;

use Models\User;

class UserController
{
    public function __construct()
    {
        // Sécurité : Empêche l'accès si non admin
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function list()
    {
        $userModel = new User();

        // On récupère le terme de recherche s'il existe dans l'URL (?q=...)
        $search = $_GET['q'] ?? null;

        // On passe le terme au modèle
        $all_users = $userModel->getAll($search);

        require 'views/admin/users_list.php';
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ... (Vérification CSRF ici) ...

            // Récupération des données
            $nom = htmlspecialchars(trim($_POST['nom']));
            $prenom = htmlspecialchars(trim($_POST['prenom']));
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $role = $_POST['role'];

            $userModel = new \Models\User();

            // APPEL AU MODÈLE CORRIGÉ
            $result = $userModel->create($nom, $prenom, $email, $password, $role);

            if ($result === true) {
                $_SESSION['success'] = "Utilisateur créé !";
            } else {
                $_SESSION['error'] = "Erreur : " . $result; // Affiche l'erreur SQL si échec
            }

            header('Location: index.php?page=admin_users');
            exit();
        }
    }


    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Sécurité CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                $_SESSION['error'] = "Erreur de sécurité.";
                header('Location: index.php?page=admin_users');
                exit();
            }

            $id_to_delete = (int)$_POST['user_id'];
            $current_user_id = $_SESSION['auth']['id'];

            // --- LA FONCTIONNALITÉ MANQUANTE ---
            // On empêche l'admin connecté de se supprimer lui-même
            if ($id_to_delete === $current_user_id) {
                $_SESSION['error'] = "Sécurité : Vous ne pouvez pas supprimer votre propre compte !";
                header('Location: index.php?page=admin_users');
                exit();
            }

            $userModel = new User();
            // Appel de la suppression en cascade (code donné précédemment)
            $result = $userModel->delete($id_to_delete);

            if ($result === true) {
                $_SESSION['success'] = "Utilisateur supprimé définitivement.";
            } else {
                $_SESSION['error'] = "Erreur lors de la suppression : " . $result;
            }

            header('Location: index.php?page=admin_users');
            exit();
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ... vérif CSRF ...
            $userModel = new User();
            $userModel->update($_POST['user_id'], $_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['role']);
            $_SESSION['success'] = "Profil mis à jour.";
            header('Location: index.php?page=admin_users');
            exit();
        }
    }
}
