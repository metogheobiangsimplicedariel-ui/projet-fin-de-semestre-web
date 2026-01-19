<?php

namespace Controllers\Admin;

use Config\Database;
use PDO;

class DashboardController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            header('Location: index.php?page=login');
            exit();
        }
    }

    public function index()
    {
        // 1. Période Active (La plus récente ouverte)
        // On en a besoin pour savoir sur quoi se basent les stats
        $stmt = $this->db->query("SELECT * FROM periodes WHERE statut = 'ouverte' ORDER BY date_debut_saisie DESC LIMIT 1");
        $periodeActive = $stmt->fetch(PDO::FETCH_ASSOC);

        // Si aucune ouverte, on prend la dernière créée
        if (!$periodeActive) {
            $stmt = $this->db->query("SELECT * FROM periodes ORDER BY date_debut_saisie DESC LIMIT 1");
            $periodeActive = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        $pid = $periodeActive['id'] ?? 0;

        // 2. Chiffres Clés (KPIs)
        $stats = [
            'etudiants' => $this->db->query("SELECT COUNT(*) FROM utilisateurs WHERE role='etudiant'")->fetchColumn(),
            'profs'     => $this->db->query("SELECT COUNT(*) FROM utilisateurs WHERE role='professeur'")->fetchColumn(),
            'matieres'  => $this->db->query("SELECT COUNT(*) FROM matieres")->fetchColumn(),
        ];

        // 3. Calcul du Taux de Saisie pour la Période Active
        // A. Nombre total de cases à remplir (Nombre d'inscriptions * Nombre de colonnes configurées)
        // C'est une estimation pour avoir un % global
        $sqlTotalSlots = "SELECT COUNT(*) 
                          FROM inscriptions_matieres im
                          JOIN configuration_colonnes cc ON im.matiere_id = cc.matiere_id AND im.periode_id = cc.periode_id
                          WHERE im.periode_id = ?";
        $stmt = $this->db->prepare($sqlTotalSlots);
        $stmt->execute([$pid]);
        $totalSlots = $stmt->fetchColumn();

        // B. Nombre de notes réellement saisies
        $sqlFilled = "SELECT COUNT(*) 
                      FROM notes n 
                      JOIN configuration_colonnes cc ON n.colonne_id = cc.id 
                      WHERE cc.periode_id = ?";
        $stmt = $this->db->prepare($sqlFilled);
        $stmt->execute([$pid]);
        $filledSlots = $stmt->fetchColumn();

        // C. Pourcentage
        $remplissage = ($totalSlots > 0) ? round(($filledSlots / $totalSlots) * 100) : 0;


        // 4. Dernières Activités (Logs)
        // On regarde les 5 dernières notes modifiées/ajoutées
        $sqlLogs = "SELECT n.date_modification, n.valeur, u.nom as prof_nom, m.code as mat_code, etu.nom as etu_nom
                    FROM notes n
                    JOIN utilisateurs u ON n.saisie_par = u.id
                    JOIN configuration_colonnes cc ON n.colonne_id = cc.id
                    JOIN matieres m ON cc.matiere_id = m.id
                    JOIN utilisateurs etu ON n.etudiant_id = etu.id
                    ORDER BY n.date_modification DESC LIMIT 5";
        $logs = $this->db->query($sqlLogs)->fetchAll(PDO::FETCH_ASSOC);

        // Appel de la vue
        require 'views/admin/dashboard.php';
    }
}
