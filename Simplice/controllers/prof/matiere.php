<?php
// models/Matiere.php
class Matiere {
    private $id;
    private $code;
    private $nom;
    private $filiereId;
    private $coefficient;
    private $credits;
    
    public function __construct($id, $code, $nom, $filiereId, $coefficient = 1, $credits = 3) {
        $this->id = $id;
        $this->code = $code;
        $this->nom = $nom;
        $this->filiereId = $filiereId;
        $this->coefficient = $coefficient;
        $this->credits = $credits;
    }
    
    /**
     * Récupère les colonnes configurées pour cette matière
     */
    public function getColonnesConfigurees($periodeId) {
        $db = Database::getInstance();
        $query = "SELECT * FROM configuration_colonnes 
                  WHERE matiere_id = ? AND periode_id = ? 
                  ORDER BY ordre ASC";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$this->id, $periodeId]);
        
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'ConfigurationColonne');
    }
    
    /**
     * Récupère les étudiants inscrits à cette matière
     */
    public function getEtudiantsInscrits($periodeId, $groupe = null) {
        $db = Database::getInstance();
        
        if ($groupe) {
            $query = "SELECT u.*, im.groupe, im.dispense 
                      FROM inscriptions_matieres im
                      JOIN utilisateurs u ON im.etudiant_id = u.id
                      WHERE im.matiere_id = ? 
                      AND im.periode_id = ? 
                      AND im.groupe = ?
                      ORDER BY u.nom, u.prenom";
            $params = [$this->id, $periodeId, $groupe];
        } else {
            $query = "SELECT u.*, im.groupe, im.dispense 
                      FROM inscriptions_matieres im
                      JOIN utilisateurs u ON im.etudiant_id = u.id
                      WHERE im.matiere_id = ? 
                      AND im.periode_id = ? 
                      ORDER BY u.nom, u.prenom";
            $params = [$this->id, $periodeId];
        }
        
        $stmt = $db->prepare($query);
        $stmt->execute($params);
        
        $etudiants = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $etudiants[] = new Etudiant(
                $row['id'],
                $row['email'],
                $row['nom'],
                $row['prenom'],
                $row['groupe'],
                $row['dispense']
            );
        }
        
        return $etudiants;
    }
    
    /**
     * Récupère la formule de calcul pour cette matière
     */
    public function getFormuleCalcul($periodeId) {
        $db = Database::getInstance();
        $query = "SELECT formule FROM formules 
                  WHERE matiere_id = ? AND periode_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$this->id, $periodeId]);
        
        return $stmt->fetchColumn();
    }
}