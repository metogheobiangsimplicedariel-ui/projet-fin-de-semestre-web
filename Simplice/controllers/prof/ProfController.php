<?php
// web/controllers/prof/ProfController.php (exemple de transformation en classe)
namespace Controllers\Prof;

class ProfController
{
    public function __construct()
    {
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'professeur') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function dashboard()
    {
        // ... votre logique actuelle de récupération des périodes et matières ...
        require 'views/prof/dashboard.php';
    }
}
