<?php

namespace Models;

use Config\Database;
use PDO;

class Result
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Récupère la "Matrice" des résultats pour une période
     * Retourne : [
     * 'etudiants' => [...liste...],
     * 'matieres'  => [...liste...],
     * 'moyennes'  => [ etudiant_id => [ matiere_id => 14.5, ... ] ]
     * ]
     */
    public function getDeliberationData($periode_id)
    {
        // 1. Récupérer toutes les matières de la période (celles qui ont des notes)
        // On peut filtrer par filière si besoin, ici on prend tout pour simplifier
        $sqlMat = "SELECT DISTINCT m.id, m.nom, m.code, m.coefficient, m.credits 
                   FROM matieres m
                   JOIN configuration_colonnes cc ON m.id = cc.matiere_id
                   WHERE cc.periode_id = ?
                   ORDER BY m.nom";
        $matieres = $this->db->prepare($sqlMat);
        $matieres->execute([$periode_id]);
        $allMatieres = $matieres->fetchAll(PDO::FETCH_ASSOC);

        // 2. Récupérer tous les étudiants inscrits sur la période
        // (Ceux qui ont au moins une inscription matière)
        $sqlEtud = "SELECT DISTINCT u.id, u.nom, u.prenom, u.email
                    FROM utilisateurs u
                    JOIN inscriptions_matieres im ON u.id = im.etudiant_id
                    WHERE im.periode_id = ?
                    ORDER BY u.nom, u.prenom";
        $etudiants = $this->db->prepare($sqlEtud);
        $etudiants->execute([$periode_id]);
        $allEtudiants = $etudiants->fetchAll(PDO::FETCH_ASSOC);

        // 3. Récupérer toutes les moyennes calculées
        $sqlMoy = "SELECT etudiant_id, matiere_id, moyenne 
                   FROM moyennes 
                   WHERE periode_id = ?";
        $moy = $this->db->prepare($sqlMoy);
        $moy->execute([$periode_id]);
        $rawMoyennes = $moy->fetchAll(PDO::FETCH_ASSOC);

        // 4. Organiser les moyennes pour un accès facile : $map[etudiant][matiere] = note
        $mapMoyennes = [];
        foreach ($rawMoyennes as $r) {
            $mapMoyennes[$r['etudiant_id']][$r['matiere_id']] = $r['moyenne'];
        }

        return [
            'matieres' => $allMatieres,
            'etudiants' => $allEtudiants,
            'moyennes' => $mapMoyennes
        ];
    }
}
