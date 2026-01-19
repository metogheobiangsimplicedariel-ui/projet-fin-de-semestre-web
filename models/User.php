<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;

class User
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    // Dans web/models/User.php

    public function getAll($search = null)
    {
        if ($search) {
            // Mode Recherche : On utilise des noms de paramètres uniques (:nom, :prenom, :email)
            $sql = "SELECT id, nom, prenom, email, role, DATE_FORMAT(date_creation, '%d/%m/%Y') as date_inscription 
                FROM utilisateurs 
                WHERE nom LIKE :nom 
                   OR prenom LIKE :prenom 
                   OR email LIKE :email 
                ORDER BY date_creation DESC";

            $stmt = $this->db->prepare($sql);

            // On prépare le terme avec les pourcentages
            $term = "%$search%";

            // On lie chaque paramètre à la même valeur
            $stmt->execute([
                ':nom'    => $term,
                ':prenom' => $term,
                ':email'  => $term
            ]);

            return $stmt->fetchAll();
        } else {
            $sql = "SELECT id, nom, prenom, email, role, DATE_FORMAT(date_creation, '%d/%m/%Y') as date_inscription 
                FROM utilisateurs 
                ORDER BY date_creation DESC";
            return $this->db->query($sql)->fetchAll();
        }
    }

    public function getAllByRole($role)
    {
        $sql = "SELECT id, nom, prenom, email FROM utilisateurs WHERE role = ? ORDER BY nom ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }


    public function create($nom, $prenom, $email, $password, $role = 'etudiant')
    {
        // 1. On hash le mot de passe ICI
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        try {
            // 2. Requête SQL
            $sql = "INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role, actif) 
                    VALUES (:nom, :prenom, :email, :pass, :role, 1)";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                ':nom'    => $nom,
                ':prenom' => $prenom,
                ':email'  => $email,
                ':pass'   => $hashed_password,
                ':role'   => $role
            ]);
        } catch (PDOException $e) {
            // On renvoie l'erreur pour la voir (ex: Duplicate entry)
            return $e->getMessage();
        }
    }

    public function update($id, $nom, $prenom, $email, $role)
    {
        $sql = "UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, role = :role WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nom'    => $nom,
            ':prenom' => $prenom,
            ':email'  => $email,
            ':role'   => $role,
            ':id'     => $id
        ]);
    }

    // web/models/User.php

    public function delete($id)
    {
        try {
            // 1. Début de la transaction (Sécurité totale)
            $this->db->beginTransaction();

            // 2. Suppression des dépendances Professeur
            $this->db->prepare("DELETE FROM affectations_profs WHERE professeur_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM progression_saisie WHERE professeur_id = ?")->execute([$id]);

            // 3. Suppression des dépendances Étudiant
            $this->db->prepare("DELETE FROM inscriptions_matieres WHERE etudiant_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM notes WHERE etudiant_id = ?")->execute([$id]);
            $this->db->prepare("DELETE FROM moyennes WHERE etudiant_id = ?")->execute([$id]);

            // 4. Suppression de l'Utilisateur lui-même
            $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id = ?");
            $stmt->execute([$id]);

            // 5. Validation finale
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            // En cas d'erreur, on annule tout (Rollback) pour ne pas casser les données
            $this->db->rollBack();

            // On retourne l'erreur pour que le contrôleur puisse l'afficher
            return $e->getMessage();
        }
    }

    // Compter les utilisateurs par rôle
    public function countByRole($role)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchColumn();
    }

    // Compter les utilisateurs en attente (actif = 0)
    public function countPending()
    {
        return $this->db->query("SELECT COUNT(*) FROM utilisateurs WHERE actif = 0")->fetchColumn();
    }

    // Récupérer les X derniers inscrits
    public function getLatest($limit = 5)
    {
        // La requête trie par ID descendant (plus grand ID = plus récent) et limite le résultat
        $sql = "SELECT id, nom, prenom, email, role, actif, 
                DATE_FORMAT(date_creation, '%d/%m/%Y') as date_fmt 
                FROM utilisateurs 
                ORDER BY date_creation DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
