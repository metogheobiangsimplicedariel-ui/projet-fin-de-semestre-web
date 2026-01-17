<?php
// models/Professeur.php
class Professeur extends Utilisateur {
    private $id;
    private $matieres = [];
    private $statut;
    
    public function __construct($id, $email, $nom, $prenom) {
        parent::__construct($id, $email, 'professeur', $nom, $prenom);
        $this->id = $id;
    }
    
    /**
     * Récupère les matières assignées pour une période
     */
    public function getMatieresAssignees($periodeId) {
        $db = Database::getInstance();
        $query = "SELECT m.*, ap.groupe 
                  FROM affectations_profs ap 
                  JOIN matieres m ON ap.matiere_id = m.id 
                  WHERE ap.professeur_id = ? AND ap.periode_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$this->id, $periodeId]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Matiere');
    }
    
    /**
     * Vérifie si le professeur peut saisir des notes pour une matière
     */
    public function peutSaisirNotes($matiereId, $periodeId) {
        $db = Database::getInstance();
        $query = "SELECT COUNT(*) FROM affectations_profs 
                  WHERE professeur_id = ? 
                  AND matiere_id = ? 
                  AND periode_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$this->id, $matiereId, $periodeId]);
        
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Récupère la progression de saisie pour une matière
     */
    public function getProgressionSaisie($matiereId, $periodeId) {
        $db = Database::getInstance();
        $query = "SELECT * FROM progression_saisie 
                  WHERE matiere_id = ? 
                  AND periode_id = ? 
                  AND professeur_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$matiereId, $periodeId, $this->id]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}