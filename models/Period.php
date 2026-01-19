<?php

namespace Models;

use Config\Database;
use PDO;

class Period
{
    private $db;
    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll()
    {
        return $this->db->query("SELECT *, DATEDIFF(date_fin_saisie, NOW()) as jours_restants FROM periodes ORDER BY annee_universitaire DESC, date_debut_saisie DESC")->fetchAll();
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM periodes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($nom, $code, $annee, $type, $debut, $fin)
    {
        // J'utilise vos noms de colonnes exacts (date_debut_saisie, statut...)
        $sql = "INSERT INTO periodes 
            (nom, code, annee_universitaire, type, date_debut_saisie, date_fin_saisie, statut, date_creation) 
            VALUES 
            (:nom, :code, :annee, :type, :debut, :fin, 'ouverte', NOW())";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':nom'   => $nom,
            ':code'  => $code,
            ':annee' => $annee,
            ':type'  => $type,
            ':debut' => $debut,
            ':fin'   => $fin
        ]);
    }

    public function updateStatus($id, $status, $motif = null)
    {
        $sql = "UPDATE periodes SET statut = :status, justification_ouverture = :motif WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':motif'  => $motif, // Sera NULL si on ferme, ou le texte si on ouvre
            ':id'     => $id
        ]);
    }

    public function changerStatut($periodeId, $statut)
    {
        // $statut = 'ouverte' ou 'fermee'
        $sql = "UPDATE periodes SET statut = ? WHERE id = ?";
        return $this->db->prepare($sql)->execute([$statut, $periodeId]);
    }

    // Gère la visibilité pour les ÉTUDIANTS
    public function changerPublication($periodeId, $publie)
    {
        // $publie = 0 ou 1
        $sql = "UPDATE periodes SET resultats_publies = ? WHERE id = ?";
        return $this->db->prepare($sql)->execute([$publie, $periodeId]);
    }
}
