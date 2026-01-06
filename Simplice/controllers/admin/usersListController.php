<?php
// controllers/admin/usersListController.php

require_once __DIR__ . '/../../config/database.php';

// 1. SÉCURITÉ
if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
    header('Location: index.php?page=login');
    exit();
}
$user = $_SESSION['auth'];

// ============================================================
// 2. TRAITEMENT DES ACTIONS (POST)
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- A. CRÉATION D'UTILISATEUR (Vient du Dashboard ou d'ici) ---
    if (isset($_POST['action']) && $_POST['action'] === 'create_user') {
        $nom = htmlspecialchars(trim($_POST['nom']));
        $prenom = htmlspecialchars(trim($_POST['prenom']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $password = $_POST['password']; // On le hash juste après
        $role = $_POST['role'];

        // Vérification si l'email existe déjà
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->execute([$email]);
        
        if ($check->rowCount() > 0) {
            $_SESSION['error'] = "Erreur : Cet email est déjà utilisé par un autre utilisateur.";
        } else {
            // Hashage du mot de passe
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
            $success = $stmt->execute([$nom, $prenom, $email, $hashed_password, $role]);

            if ($success) {
                $_SESSION['success'] = "Nouvel utilisateur ($role) créé avec succès !";
            } else {
                $_SESSION['error'] = "Erreur lors de la création en base de données.";
            }
        }
        header('Location: index.php?page=admin_users');
        exit();
    }

    // --- B. MISE À JOUR D'UN UTILISATEUR ---
    if (isset($_POST['action']) && $_POST['action'] === 'update_user') {
        $id = (int)$_POST['user_id'];
        $nom = htmlspecialchars(trim($_POST['nom']));
        $prenom = htmlspecialchars(trim($_POST['prenom']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $role = $_POST['role'];

        $sql = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, role = :role WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            ':nom' => $nom, 
            ':prenom' => $prenom, 
            ':email' => $email, 
            ':role' => $role, 
            ':id' => $id
        ]);

        if ($success) {
            $_SESSION['success'] = "Profil mis à jour avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de la mise à jour.";
        }
        header('Location: index.php?page=admin_users');
        exit();
    }

    // --- C. SUPPRESSION (Avec gestion des clés étrangères) ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete_user') {
        $id_to_delete = (int)$_POST['user_id'];

        // On empêche l'admin de se supprimer lui-même
        if ($id_to_delete === $user['id']) {
            $_SESSION['error'] = "Sécurité : Vous ne pouvez pas supprimer votre propre compte !";
        } else {
            try {
                // Transaction pour suppression propre
                $pdo->beginTransaction();

                // 1. Supprimer liens profs
                $pdo->prepare("DELETE FROM affectations_profs WHERE professeur_id = ?")->execute([$id_to_delete]);
                $pdo->prepare("DELETE FROM progression_saisie WHERE professeur_id = ?")->execute([$id_to_delete]);

                // 2. Supprimer liens étudiants
                $pdo->prepare("DELETE FROM inscriptions_matieres WHERE etudiant_id = ?")->execute([$id_to_delete]);
                $pdo->prepare("DELETE FROM notes WHERE etudiant_id = ?")->execute([$id_to_delete]);
                $pdo->prepare("DELETE FROM moyennes WHERE etudiant_id = ?")->execute([$id_to_delete]);

                // 3. Supprimer l'utilisateur
                $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?")->execute([$id_to_delete]);

                $pdo->commit();
                $_SESSION['success'] = "Utilisateur supprimé définitivement.";

            } catch (PDOException $e) {
                $pdo->rollBack();
                // Si on a oublié une table liée (ex: saisi_par dans notes)
                if ($e->getCode() == '23000') {
                    $_SESSION['error'] = "Impossible de supprimer : Cet utilisateur a laissé des traces (notes saisies, etc.). Désactivez-le plutôt.";
                } else {
                    $_SESSION['error'] = "Erreur technique : " . $e->getMessage();
                }
            }
        }
        header('Location: index.php?page=admin_users');
        exit();
    }
}

// ============================================================
// 3. AFFICHAGE DE LA LISTE (GET)
// ============================================================
$search = isset($_GET['q']) ? trim($_GET['q']) : null;

if ($search) {
    // Mode Recherche
    $sql = "SELECT id, nom, prenom, email, role, DATE_FORMAT(date_creation, '%d/%m/%Y') as date_inscription 
            FROM utilisateurs 
            WHERE nom LIKE :s OR prenom LIKE :s OR email LIKE :s 
            ORDER BY date_creation DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':s' => "%$search%"]);
} else {
    // Mode Liste Complète
    $sql = "SELECT id, nom, prenom, email, role, DATE_FORMAT(date_creation, '%d/%m/%Y') as date_inscription 
            FROM utilisateurs 
            ORDER BY date_creation DESC";
    $stmt = $pdo->query($sql);
}

$all_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Appel de la vue
require __DIR__ . '/../../views/admin/users_list.php';
?>