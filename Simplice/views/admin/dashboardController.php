<?php
require_once __DIR__ . '/../../config/database.php';

// SÉCURITÉ
if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
    header('Location: index.php?page=login');
    exit();
}
$user = $_SESSION['auth'];

// 1. RÉCUPÉRATION DES STATISTIQUES (Pour les cartes du haut)
$countStudents = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'etudiant'")->fetchColumn();
$countProfs    = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'professeur'")->fetchColumn();
$pending_users = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE actif = 0")->fetchColumn(); // Si actif=0 veut dire en attente
$classes       = $pdo->query("SELECT COUNT(*) FROM filieres")->fetchColumn(); // On compte les filières

// 2. RÉCUPÉRATION DES 5 DERNIERS INSCRITS (La partie importante !)
// On trie par date_creation décroissante (DESC) et on en prend 5
$sql = "SELECT id, nom, prenom, email, role, actif, 
        DATE_FORMAT(date_creation, '%d %b %Y') as date_fmt 
        FROM utilisateurs 
        ORDER BY date_creation DESC 
        LIMIT 5";
$stmt = $pdo->query($sql);
$latest_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Appel de la vue
require __DIR__ . '/../../views/admin/dashboard.php';
?>