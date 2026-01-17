<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;

class Subject
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Récupérer une matière par son ID (C'est la méthode qu'il vous manquait !)
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM matieres WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllWithFiliere()
    {
        return $this->db->query("SELECT m.*, f.nom as nom_filiere, f.code as code_filiere 
                                 FROM matieres m 
                                 JOIN filieres f ON m.filiere_id = f.id 
                                 ORDER BY f.code ASC, m.nom ASC")->fetchAll();
    }

    public function getAllFilieres()
    {
        return $this->db->query("SELECT * FROM filieres ORDER BY nom ASC")->fetchAll();
    }

    public function create($code, $nom, $filiere_id, $coeff, $credits, $seuil)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO matieres (code, nom, filiere_id, coefficient, credits, seuil_validation) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
            return $stmt->execute([$code, $nom, $filiere_id, $coeff, $credits, $seuil]);
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) return 'duplicate';
            return false;
        }
    }
}
