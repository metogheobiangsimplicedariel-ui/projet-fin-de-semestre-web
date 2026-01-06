<?php
require_once __DIR__ . '/../../config/database.php';

// Sécurité Admin
if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
    header('Location: index.php?page=login');
    exit();
}

// TRAITEMENT DU FORMULAIRE (Ajout / Modif)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- CRÉATION D'UNE PÉRIODE ---
    if (isset($_POST['action']) && $_POST['action'] === 'create_period') {
        // Récupération des champs spécifiques à votre table
        $nom = $_POST['nom'];
        $code = $_POST['code']; // ex: S1-2025
        $annee = $_POST['annee_universitaire']; // ex: 2025-2026
        $type = $_POST['type']; // semestre, trimestre, etc.
        $debut = $_POST['date_debut_saisie'];
        $fin = $_POST['date_fin_saisie'];
        
        // Calcul du statut automatique
        // Si la date de début est passée, on ouvre, sinon "a_venir" (selon votre ENUM)
        $now = date('Y-m-d H:i:s');
        $statut = ($debut <= $now) ? 'ouverte' : 'a_venir';

        $sql = "INSERT INTO periodes (nom, code, annee_universitaire, type, date_debut_saisie, date_fin_saisie, statut) 
                VALUES (:nom, :code, :annee, :type, :debut, :fin, :statut)";
        
        $req = $pdo->prepare($sql);
        $req->execute([
            ':nom' => $nom,
            ':code' => $code,
            ':annee' => $annee,
            ':type' => $type,
            ':debut' => $debut,
            ':fin' => $fin,
            ':statut' => $statut
        ]);
        
        $_SESSION['success'] = "Période créée avec succès !";
        header('Location: index.php?page=admin_periods');
        exit();
    }
    
    // --- CHANGEMENT DE STATUT ---
    if (isset($_POST['action']) && $_POST['action'] === 'update_status') {
        $id = $_POST['periode_id'];
        $new_statut = $_POST['statut']; // a_venir, ouverte, fermee, publiee
        
        // Si on publie, on met à jour la date_publication
        if ($new_statut === 'publiee') {
            $req = $pdo->prepare("UPDATE periodes SET statut = :statut, date_publication = NOW() WHERE id = :id");
        } else {
            $req = $pdo->prepare("UPDATE periodes SET statut = :statut WHERE id = :id");
        }
        
        $req->execute([':statut' => $new_statut, ':id' => $id]);
        
        header('Location: index.php?page=admin_periods');
        exit();
    }
}

// RÉCUPÉRATION DES PÉRIODES
// On trie par année universitaire DESC, puis par date de début
$stmt = $pdo->query("SELECT *, DATEDIFF(date_fin_saisie, NOW()) as jours_restants FROM periodes ORDER BY annee_universitaire DESC, date_debut_saisie DESC");
$periodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../views/admin/periods/index.php';
?>