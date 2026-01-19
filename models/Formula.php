<?php

namespace Models;

use Config\Database;
use PDO;

class Formula
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function get($matiere_id, $periode_id)
    {
        $sql = "SELECT * FROM formules WHERE matiere_id = ? AND periode_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$matiere_id, $periode_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTemplates()
    {
        return $this->db->query("SELECT * FROM templates_formules ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
    }


    public function save($matiere_id, $periode_id, $formule, $description = '')
    {
        $sql = "INSERT INTO formules (matiere_id, periode_id, formule, description) 
                VALUES (:mid, :pid, :formule, :desc)
                ON DUPLICATE KEY UPDATE 
                formule = :formule_update, 
                description = :desc_update";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':mid'            => $matiere_id,
            ':pid'            => $periode_id,
            ':formule'        => $formule,
            ':desc'           => $description,
            ':formule_update' => $formule,
            ':desc_update'    => $description
        ]);
    }
}
