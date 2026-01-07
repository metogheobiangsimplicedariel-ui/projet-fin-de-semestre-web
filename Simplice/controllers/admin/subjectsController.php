<?php
require_once __DIR__ . '/../../config/database.php';

// 1. SÉCURITÉ
if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
    header('Location: index.php?page=login');
    exit();
}

// 2. TRAITEMENT : AJOUTER UNE MATIÈRE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_subject') {
    
    // Nettoyage des données
    $code = strtoupper(trim($_POST['code']));
    $nom = trim($_POST['nom']);
    $filiere_id = (int)$_POST['filiere_id'];
    $coeff = (float)$_POST['coefficient'];
    $credits = (int)$_POST['credits'];
    $seuil = (float)$_POST['seuil_validation'];

    try {
        $sql = "INSERT INTO matieres (code, nom, filiere_id, coefficient, credits, seuil_validation) 
                VALUES (:code, :nom, :fid, :coeff, :credits, :seuil)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':code' => $code,
            ':nom' => $nom,
            ':fid' => $filiere_id,
            ':coeff' => $coeff,
            ':credits' => $credits,
            ':seuil' => $seuil
        ]);

        $_SESSION['success'] = "Matière ajoutée au catalogue !";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $_SESSION['error'] = "Erreur : Ce code matière existe déjà.";
        } else {
            $_SESSION['error'] = "Erreur SQL : " . $e->getMessage();
        }
    }

    header('Location: index.php?page=admin_subjects');
    exit();
}

// 3. RÉCUPÉRATION DES DONNÉES

// A. Liste des matières (Avec le nom de la filière via une JOINTURE)
$sql = "SELECT m.*, f.nom as nom_filiere, f.code as code_filiere 
        FROM matieres m
        JOIN filieres f ON m.filiere_id = f.id
        ORDER BY f.code ASC, m.nom ASC";
$stmt = $pdo->query($sql);
$matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// B. Liste des filières (Pour le menu déroulant du formulaire)
$stmt = $pdo->query("SELECT * FROM filieres ORDER BY nom ASC");
$filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../views/admin/subjects/index.php';
?>