<?php


require_once __DIR__ . '/../../config/database.php';
require_once 'lib/php_function/functions.php';

// 1. SÉCURITÉ : Vérification stricte du rôle ADMIN
if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
    // Si pas connecté OU pas admin -> Dehors !
    header('Location: index.php?page=login');
    exit();
}
$user = $_SESSION['auth'];

// 2. MOCK DATA (Données fictives pour l'exemple)
$countStudents = compt_entries($pdo, 'utilisateurs', 'role', 'etudiant');
$countProfs    = compt_entries($pdo, 'utilisateurs', 'role', 'professeur');
$countAdmins   = compt_entries($pdo, 'utilisateurs', 'role', 'admin');
$pending_users = 5;
$classes = 12;



