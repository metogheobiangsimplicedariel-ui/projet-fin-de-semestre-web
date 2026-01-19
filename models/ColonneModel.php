<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;

class ColonneModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getColonnesByMatiere($matiereId)
    {
        // On récupère les colonnes actives pour cette matière
        $sql = "SELECT cc.id, cc.nom_colonne, cc.coefficient, cc.type, cc.note_max
                FROM configuration_colonnes cc
                WHERE cc.matiere_id = ?
                ORDER BY cc.ordre ASC, cc.id ASC";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$matiereId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getColonnesByMatiere: " . $e->getMessage());
            return [];
        }
    }
    public function getColonnesByMatiereEtPeriode($matiereId, $periodeId)
    {
        // On ajoute "AND periode_id = ?" pour filtrer
        $sql = "SELECT * FROM configuration_colonnes 
                WHERE matiere_id = ? AND periode_id = ? 
                ORDER BY ordre ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$matiereId, $periodeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getColonneById($colonneId)
    {
        $sql = "SELECT * FROM configuration_colonnes WHERE id = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$colonneId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return null;
        }
    }
}
