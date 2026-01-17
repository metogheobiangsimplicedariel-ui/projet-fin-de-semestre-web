<?php

namespace Models;

use Config\Database;
use PDO;

class Assignment
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /* ==========================================================
       PARTIE PROFESSEUR (Utilisée par DashboardController Prof)
       ========================================================== */

    /**
     * Récupère les matières affectées à un professeur pour une période
     */
    public function getSubjectsForProf($prof_id, $periode_id)
    {
        $sql = "SELECT m.*, f.nom as nom_filiere, f.code as code_filiere, ap.groupe, ap.id as affectation_id
                FROM affectations_profs ap
                JOIN matieres m ON ap.matiere_id = m.id
                JOIN filieres f ON m.filiere_id = f.id
                WHERE ap.professeur_id = ? AND ap.periode_id = ?
                ORDER BY f.code ASC, m.nom ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$prof_id, $periode_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Trouve la dernière période active du prof (pour l'algo de redirection auto)
     */
    public function getLastPeriodIdForProf($prof_id)
    {
        $sql = "SELECT periode_id FROM affectations_profs 
                WHERE professeur_id = ? 
                ORDER BY periode_id DESC 
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$prof_id]);
        return $stmt->fetchColumn();
    }

    /* ==========================================================
       PARTIE ADMIN (Utilisée par AssignmentController Admin)
       ========================================================== */

    // Récupérer les profs affectés avec leurs détails
    public function getAssignedProfs($matiere_id, $periode_id)
    {
        $sql = "SELECT ap.*, u.nom, u.prenom, u.email 
                FROM affectations_profs ap 
                JOIN utilisateurs u ON ap.professeur_id = u.id 
                WHERE ap.matiere_id = ? AND ap.periode_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$matiere_id, $periode_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les étudiants inscrits
    public function getEnrolledStudents($matiere_id, $periode_id)
    {
        $sql = "SELECT im.*, u.nom, u.prenom, u.email 
                FROM inscriptions_matieres im 
                JOIN utilisateurs u ON im.etudiant_id = u.id 
                WHERE im.matiere_id = ? AND im.periode_id = ?
                ORDER BY im.groupe, u.nom";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$matiere_id, $periode_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Assigner un prof
    public function assignProf($prof_id, $matiere_id, $periode_id, $groupe)
    {
        $sql = "INSERT INTO affectations_profs (professeur_id, matiere_id, periode_id, groupe) VALUES (?, ?, ?, ?)";
        return $this->db->prepare($sql)->execute([$prof_id, $matiere_id, $periode_id, $groupe]);
    }

    // Retirer un prof
    public function removeProf($assignment_id)
    {
        return $this->db->prepare("DELETE FROM affectations_profs WHERE id = ?")->execute([$assignment_id]);
    }

    // Inscrire un étudiant (IGNORE pour éviter les erreurs de doublons)
    public function enrollStudent($student_id, $matiere_id, $periode_id, $groupe)
    {
        $sql = "INSERT IGNORE INTO inscriptions_matieres (etudiant_id, matiere_id, periode_id, groupe) VALUES (?, ?, ?, ?)";
        return $this->db->prepare($sql)->execute([$student_id, $matiere_id, $periode_id, $groupe]);
    }

    // Désinscrire un étudiant
    public function removeStudent($inscription_id)
    {
        return $this->db->prepare("DELETE FROM inscriptions_matieres WHERE id = ?")->execute([$inscription_id]);
    }
}
