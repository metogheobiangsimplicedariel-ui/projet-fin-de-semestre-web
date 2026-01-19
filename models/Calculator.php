<?php

namespace Models;

use Config\Database;
use PDO;

class Calculator
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Point d'entrée principal : Calcule toute la classe
     */
    public function calculateClass($matiere_id, $periode_id)
    {
        // 1. On récupère la configuration (Coefficients) pour cette période
        // On ne prend que les colonnes qui "comptent" (type 'note')
        $sqlCols = "SELECT id, coefficient FROM configuration_colonnes 
                    WHERE matiere_id = ? AND periode_id = ? AND type = 'note'";
        $stmt = $this->db->prepare($sqlCols);
        $stmt->execute([$matiere_id, $periode_id]);
        $colonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($colonnes)) {
            return 0; // Pas de configuration, pas de calcul
        }

        // 2. On récupère tous les étudiants inscrits
        $sqlEtu = "SELECT etudiant_id FROM inscriptions_matieres 
                   WHERE matiere_id = ? AND periode_id = ?";
        $stmtEtu = $this->db->prepare($sqlEtu);
        $stmtEtu->execute([$matiere_id, $periode_id]);
        $etudiants = $stmtEtu->fetchAll(PDO::FETCH_COLUMN);

        $count = 0;
        foreach ($etudiants as $eid) {
            $this->calculateStudentAverage($eid, $matiere_id, $periode_id, $colonnes);
            $count++;
        }
        return $count;
    }

    /**
     * Calcule la moyenne d'un étudiant en utilisant les COEFFICIENTS
     */
    private function calculateStudentAverage($etudiant_id, $matiere_id, $periode_id, $colonnes)
    {
        $totalPoints = 0;
        $totalCoeffs = 0;

        // On récupère toutes les notes de l'étudiant pour cette matière d'un coup
        $sqlNotes = "SELECT colonne_id, valeur FROM notes 
                     WHERE etudiant_id = ? AND valeur IS NOT NULL";
        $stmt = $this->db->prepare($sqlNotes);
        $stmt->execute([$etudiant_id]);
        // On obtient un tableau : [ID_COLONNE => NOTE]
        $notes = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        foreach ($colonnes as $col) {
            $colId = $col['id'];
            $coeff = floatval($col['coefficient']);

            // Si l'étudiant a une note pour cette colonne
            if (isset($notes[$colId])) {
                $valeur = floatval($notes[$colId]);

                $totalPoints += ($valeur * $coeff);
                $totalCoeffs += $coeff;
            }
            // NOTE : Ici, on gère les absences. 
            // Actuellement, si pas de note, ça ne compte pas dans la moyenne (moyenne glissante).
            // Si vous voulez que l'absence compte pour 0, décommentez la ligne ci-dessous :
            // else { $totalCoeffs += $coeff; } 
        }

        // Calcul final
        $moyenne = null;
        if ($totalCoeffs > 0) {
            $moyenne = round($totalPoints / $totalCoeffs, 2);
        }

        // Sauvegarde
        $this->saveAverage($etudiant_id, $matiere_id, $periode_id, $moyenne);
    }

    private function saveAverage($etudiant_id, $matiere_id, $periode_id, $moyenne)
    {
        // Si la moyenne est null (aucune note), on ne l'enregistre pas ou on la met à NULL
        if ($moyenne === null) return;

        $sql = "INSERT INTO moyennes (etudiant_id, matiere_id, periode_id, moyenne, date_calcul)
                VALUES (:eid, :mid, :pid, :moy, NOW())
                ON DUPLICATE KEY UPDATE moyenne = :moy2, date_calcul = NOW()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':eid' => $etudiant_id,
            ':mid' => $matiere_id,
            ':pid' => $periode_id,
            ':moy' => $moyenne,
            ':moy2' => $moyenne
        ]);
    }
}
