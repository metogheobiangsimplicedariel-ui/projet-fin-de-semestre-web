<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;

class NoteModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getNotesByMatiere($matiereId)
    {
        $sql = "SELECT n.etudiant_id, n.colonne_id, n.valeur, n.date_saisie, n.saisie_par
                FROM notes n
                INNER JOIN configuration_colonnes cc ON n.colonne_id = cc.id
                WHERE cc.matiere_id = ?
                ORDER BY n.etudiant_id, n.colonne_id";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$matiereId]);
            // On retourne le tableau associatif brut (Lignes SQL)
            // C'est ce que ResultController (Admin) attend !
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getNotesByMatiere: " . $e->getMessage());
            return [];
        }
    }

    public function sauvegarderNote($etudiantId, $colonneId, $valeur, $professeurId)
    {
        // 1. Vérifier si la note existe déjà
        $sqlCheck = "SELECT id FROM notes WHERE etudiant_id = ? AND colonne_id = ?";
        $stmtCheck = $this->db->prepare($sqlCheck);
        $stmtCheck->execute([$etudiantId, $colonneId]);
        $exists = $stmtCheck->fetchColumn();

        try {
            if ($exists) {
                // UPDATE
                $sql = "UPDATE notes 
                        SET valeur = ?, date_saisie = NOW(), saisie_par = ?
                        WHERE etudiant_id = ? AND colonne_id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$valeur, $professeurId, $etudiantId, $colonneId]);
            } else {
                // INSERT
                $sql = "INSERT INTO notes (etudiant_id, colonne_id, valeur, date_saisie, saisie_par)
                        VALUES (?, ?, ?, NOW(), ?)";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$etudiantId, $colonneId, $valeur, $professeurId]);
            }
        } catch (PDOException $e) {
            error_log("Erreur SQL Note: " . $e->getMessage());
            return false;
        }
    }

    public function mettreAJourProgression($professeurId, $matiereId, $periodeId)
    {
        // 1. Compter les étudiants inscrits (Total à noter)
        $sqlEtu = "SELECT COUNT(*) FROM inscriptions_matieres 
                   WHERE matiere_id = ? AND periode_id = ?";
        $stmtEtu = $this->db->prepare($sqlEtu);
        $stmtEtu->execute([$matiereId, $periodeId]);
        $nbEtudiants = $stmtEtu->fetchColumn();

        // 2. Compter les colonnes configurées
        $sqlCol = "SELECT COUNT(*) FROM configuration_colonnes 
                   WHERE matiere_id = ? AND periode_id = ?";
        $stmtCol = $this->db->prepare($sqlCol);
        $stmtCol->execute([$matiereId, $periodeId]);
        $nbColonnes = $stmtCol->fetchColumn();

        $totalAttendu = $nbEtudiants * $nbColonnes;
        if ($totalAttendu == 0) return;

        // 3. Compter les notes réellement saisies
        $sqlNotes = "SELECT COUNT(n.id) FROM notes n
                     INNER JOIN configuration_colonnes cc ON n.colonne_id = cc.id
                     WHERE cc.matiere_id = ? AND cc.periode_id = ? AND n.valeur IS NOT NULL";
        $stmtNotes = $this->db->prepare($sqlNotes);
        $stmtNotes->execute([$matiereId, $periodeId]);
        $nbSaisies = $stmtNotes->fetchColumn();

        // 4. Calcul du pourcentage
        $pourcentage = ($nbSaisies / $totalAttendu) * 100;

        // 5. INSERTION INTELLIGENTE (ON DUPLICATE KEY UPDATE)
        // Cette requête essaie d'insérer. Si la clé unique (matiere_id, periode_id) existe déjà,
        // elle met simplement à jour les valeurs au lieu de planter.
        $sql = "INSERT INTO progression_saisie 
                (matiere_id, periode_id, professeur_id, total_etudiants, total_notes_attendues, notes_saisies, pourcentage, valide_par_prof, date_mise_a_jour)
                VALUES (?, ?, ?, ?, ?, ?, ?, 0, NOW())
                ON DUPLICATE KEY UPDATE
                notes_saisies = VALUES(notes_saisies),
                total_etudiants = VALUES(total_etudiants),
                total_notes_attendues = VALUES(total_notes_attendues),
                pourcentage = VALUES(pourcentage),
                professeur_id = VALUES(professeur_id), -- On met à jour le dernier prof ayant modifié
                date_mise_a_jour = NOW()";

        $this->db->prepare($sql)->execute([
            $matiereId,
            $periodeId,
            $professeurId,
            $nbEtudiants,
            $totalAttendu,
            $nbSaisies,
            $pourcentage
        ]);
    }
}
