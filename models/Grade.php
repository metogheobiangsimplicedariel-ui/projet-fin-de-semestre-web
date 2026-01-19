<?php

namespace Models;

use Config\Database;
use PDO;

class Grade
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Récupère les étudiants inscrits à une matière + leurs notes déjà saisies
    public function getGradeSheet($matiere_id, $periode_id)
    {
        // 1. Récupérer tous les étudiants inscrits
        // Note: On suppose qu'il y a une table 'inscriptions_matieres' ou qu'on prend tous les étudiants 'etudiant'
        // Pour simplifier ici, on prend tous les utilisateurs role='etudiant'
        // IDEALEMENT : Il faut une table de liaison inscriptions_matieres
        $sql = "SELECT id, nom, prenom FROM utilisateurs WHERE role = 'etudiant' ORDER BY nom ASC";
        $etudiants = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        // 2. Récupérer toutes les notes existantes pour cette matière/période
        $sqlNotes = "SELECT n.etudiant_id, n.colonne_id, n.valeur 
                     FROM notes n
                     JOIN configuration_colonnes c ON n.colonne_id = c.id
                     WHERE c.matiere_id = ? AND c.periode_id = ?";
        $stmt = $this->db->prepare($sqlNotes);
        $stmt->execute([$matiere_id, $periode_id]);
        $existingNotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Organiser les notes par [etudiant_id][colonne_id] = valeur
        $notesMap = [];
        foreach ($existingNotes as $n) {
            $notesMap[$n['etudiant_id']][$n['colonne_id']] = $n['valeur'];
        }

        // 4. Fusionner
        foreach ($etudiants as &$etudiant) {
            $etudiant['notes'] = $notesMap[$etudiant['id']] ?? [];
        }

        return $etudiants;
    }

    // Sauvegarder une note (Insert ou Update)
    public function save($etudiant_id, $colonne_id, $valeur, $saisi_par)
    {
        // Si valeur vide, on met NULL
        $val = ($valeur === '') ? null : $valeur;

        $sql = "INSERT INTO notes (etudiant_id, colonne_id, valeur, saisi_par, date_saisie)
                VALUES (:eid, :cid, :val, :prof, NOW())
                ON DUPLICATE KEY UPDATE 
                valeur = :val_upd, 
                saisi_par = :prof_upd,
                date_modification = NOW()";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':eid' => $etudiant_id,
            ':cid' => $colonne_id,
            ':val' => $val,
            ':prof' => $saisi_par,
            ':val_upd' => $val,
            ':prof_upd' => $saisi_par
        ]);
    }
}
