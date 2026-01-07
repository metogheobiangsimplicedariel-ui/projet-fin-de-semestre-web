<?php
class MatiereModel {
    private $db;
    
    public function __construct() {
        require_once __DIR__ . '/../config/database.php';
        $this->db = Database::getConnection();
    }
    
    public function getMatieresByProfesseur($professeurId) {
        $sql = "SELECT m.id, m.nom, m.code, p.nom as periode_nom
                FROM matieres m
                INNER JOIN affectations a ON m.id = a.matiere_id
                INNER JOIN periodes p ON a.periode_id = p.id
                WHERE a.professeur_id = ?
                AND p.statut = 'ouverte'
                ORDER BY m.nom ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$professeurId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur getMatieresByProfesseur: " . $e->getMessage());
            return [];
        }
    }
}
?>