<?php

namespace Controllers\Admin;

use Models\User;
use Config\Database;
use PDO;

class DashboardController
{
    private $db;

    public function __construct()
    {
        // Sécurité : Vérifier que c'est bien un admin
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
        $this->db = Database::getConnection();
    }

    public function index()
    {
        $userModel = new User();

        // 1. Récupération des compteurs (Cartes du haut)
        $countStudents = $userModel->countByRole('etudiant');
        $countProfs    = $userModel->countByRole('professeur');
        $pending_users = $userModel->countByRole('etudiant'); // Ou countPending() si vous gérez l'activation

        // Pour les classes (Filières), on fait une requête directe simple ici
        $classes = $this->db->query("SELECT COUNT(*) FROM filieres")->fetchColumn();

        // 2. Récupération des 5 derniers inscrits
        // Si la base en a moins de 5, la méthode renverra ce qu'elle a (1, 2, ou 0)
        $latest_users = $userModel->getLatest(5);

        // 3. Chargement de la vue avec les données
        require 'views/admin/dashboard.php';
    }
}
