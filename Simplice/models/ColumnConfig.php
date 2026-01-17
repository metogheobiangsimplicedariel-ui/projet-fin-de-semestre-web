<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;

class ColumnConfig
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Récupérer les colonnes d'une matière pour une période
    public function getAll($periode_id, $matiere_id)
    {
        $sql = "SELECT * FROM configuration_colonnes 
                WHERE periode_id = ? AND matiere_id = ? 
                ORDER BY ordre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periode_id, $matiere_id]);
        return $stmt->fetchAll();
    }

    // Vérifier si une colonne a déjà des notes (Sécurité avant modif/suppression)
    public function hasNotes($colonne_id)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM notes WHERE colonne_id = ?");
        $stmt->execute([$colonne_id]);
        return $stmt->fetchColumn() > 0;
    }

    // Créer une nouvelle colonne (avec calcul automatique de l'ordre)
    public function create($matiere_id, $periode_id, $nom, $code, $type, $max, $coeff, $obligatoire)
    {
        try {
            // 1. Calcul du nouvel ordre
            $stmt = $this->db->prepare("SELECT MAX(ordre) FROM configuration_colonnes WHERE matiere_id = ? AND periode_id = ?");
            $stmt->execute([$matiere_id, $periode_id]);
            $ordre = ($stmt->fetchColumn() ?? 0) + 1;

            // 2. Insertion
            $sql = "INSERT INTO configuration_colonnes 
                    (matiere_id, periode_id, nom_colonne, code_colonne, type, note_max, coefficient, obligatoire, ordre) 
                    VALUES (:mid, :pid, :nom, :code, :type, :max, :coeff, :obl, :ordre)";

            $req = $this->db->prepare($sql);
            return $req->execute([
                ':mid'   => $matiere_id,
                ':pid'   => $periode_id,
                ':nom'   => $nom,
                ':code'  => strtoupper($code),
                ':type'  => $type,
                ':max'   => $max,
                ':coeff' => $coeff,
                ':obl'   => $obligatoire ? 1 : 0,
                ':ordre' => $ordre
            ]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) return 'duplicate_code';
            return $e->getMessage();
        }
    }

    // Mettre à jour une colonne
    public function update($id, $nom, $code, $type, $max, $coeff, $obligatoire)
    {
        // On ne permet pas de changer matière ou période ici pour simplifier
        $sql = "UPDATE configuration_colonnes SET 
                nom_colonne = :nom, code_colonne = :code, type = :type, 
                note_max = :max, coefficient = :coeff, obligatoire = :obl 
                WHERE id = :id";

        $req = $this->db->prepare($sql);
        return $req->execute([
            ':nom'   => $nom,
            ':code'  => strtoupper($code),
            ':type'  => $type,
            ':max'   => $max,
            ':coeff' => $coeff,
            ':obl'   => $obligatoire ? 1 : 0,
            ':id'    => $id
        ]);
    }

    // Supprimer une colonne
    public function delete($id)
    {
        $req = $this->db->prepare("DELETE FROM configuration_colonnes WHERE id = ?");
        return $req->execute([$id]);
    }

    // Dupliquer la configuration d'une autre matière
    public function duplicate($source_matiere_id, $target_matiere_id, $periode_id)
    {
        // 1. Récupérer la source
        $sources = $this->getAll($periode_id, $source_matiere_id);
        if (empty($sources)) return 0;

        $count = 0;
        $insertSql = "INSERT INTO configuration_colonnes 
                      (matiere_id, periode_id, nom_colonne, code_colonne, type, note_max, coefficient, obligatoire, ordre) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $insertStmt = $this->db->prepare($insertSql);

        // 2. Vérifier existant dans la cible pour éviter doublons de code
        $checkStmt = $this->db->prepare("SELECT id FROM configuration_colonnes WHERE matiere_id = ? AND periode_id = ? AND code_colonne = ?");

        foreach ($sources as $src) {
            $checkStmt->execute([$target_matiere_id, $periode_id, $src['code_colonne']]);
            if (!$checkStmt->fetch()) {
                $insertStmt->execute([
                    $target_matiere_id,
                    $periode_id,
                    $src['nom_colonne'],
                    $src['code_colonne'],
                    $src['type'],
                    $src['note_max'],
                    $src['coefficient'],
                    $src['obligatoire'],
                    $src['ordre']
                ]);
                $count++;
            }
        }
        return $count;
    }

    // Réorganiser (Drag & Drop)
    public function reorder($orderArray)
    {
        $sql = "UPDATE configuration_colonnes SET ordre = :ordre WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        foreach ($orderArray as $index => $id) {
            $stmt->execute([':ordre' => $index + 1, ':id' => $id]);
        }
    }
}
