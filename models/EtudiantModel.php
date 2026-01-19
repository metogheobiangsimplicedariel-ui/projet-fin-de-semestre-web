<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;

class EtudiantModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getEtudiantsByMatiere($matiereId, $periodeId)
    {
        // AJOUT DE DISTINCT : Pour éviter les doublons si l'étudiant est dans plusieurs groupes
        // AJOUT DE WHERE im.periode_id : Pour ne prendre que les inscriptions de cette période
        $sql = "SELECT DISTINCT u.id, u.nom, u.prenom, u.email
                FROM utilisateurs u
                INNER JOIN inscriptions_matieres im ON u.id = im.etudiant_id
                WHERE im.matiere_id = ?
                AND im.periode_id = ?  
                AND u.role = 'etudiant'
                AND u.actif = 1
                ORDER BY u.nom ASC, u.prenom ASC";
        try {
            $stmt = $this->db->prepare($sql);
            // On passe les deux paramètres
            $stmt->execute([$matiereId, $periodeId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getEtudiantsByMatiere: " . $e->getMessage());
            return [];
        }
    }
}
