<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;


class PermissionModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function verifierPermissionProfesseur($professeurId, $matiereId, $colonneId = null)
    {
        // 1. Vérifier l'affectation sur une période ouverte
        $sql = "SELECT COUNT(*) as count
                FROM affectations_profs a
                JOIN periodes p ON a.periode_id = p.id
                WHERE a.professeur_id = ?
                AND a.matiere_id = ?
                AND p.statut = 'ouverte'";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$professeurId, $matiereId]);
        if ($stmt->fetchColumn() == 0) {
            return false;
        }

        // 2. Si une colonne est spécifiée, vérifier qu'elle appartient bien à la matière
        if ($colonneId) {
            $sqlCol = "SELECT COUNT(*) FROM configuration_colonnes 
                       WHERE id = ? AND matiere_id = ?";
            $stmtCol = $this->db->prepare($sqlCol);
            $stmtCol->execute([$colonneId, $matiereId]);
            if ($stmtCol->fetchColumn() == 0) {
                return false;
            }
        }

        return true;
    }
}
