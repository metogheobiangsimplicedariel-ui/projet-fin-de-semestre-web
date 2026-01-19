<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;

class MatiereModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Nouvelle version adaptée à votre Dashboard Tailwind
    public function getMatieresProfesseurPourPeriode($professeurId, $periodeId)
    {
        // 1. Récupérer les matières
        $sql = "SELECT m.id, m.nom, m.code, f.code as code_filiere, ap.groupe
                FROM matieres m
                INNER JOIN affectations_profs ap ON m.id = ap.matiere_id
                INNER JOIN filieres f ON m.filiere_id = f.id
                WHERE ap.professeur_id = ?
                AND ap.periode_id = ?
                ORDER BY m.nom ASC";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$professeurId, $periodeId]);
            $matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // 2. Pour chaque matière, on va chercher ses colonnes configurées
            // C'est nécessaire pour l'affichage des petits badges dans la vue
            foreach ($matieres as &$m) {
                $sqlCol = "SELECT nom_colonne, note_max FROM configuration_colonnes 
                           WHERE matiere_id = ? AND periode_id = ? 
                           ORDER BY ordre ASC";
                $stmtCol = $this->db->prepare($sqlCol);
                $stmtCol->execute([$m['id'], $periodeId]);
                $m['colonnes'] = $stmtCol->fetchAll(PDO::FETCH_ASSOC);
            }

            return $matieres;
        } catch (PDOException $e) {
            error_log("Erreur getMatieresProfesseurPourPeriode: " . $e->getMessage());
            return [];
        }
    }
}
