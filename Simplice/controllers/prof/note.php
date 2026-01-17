<?php
// models/Note.php
class Note {
    private $id;
    private $etudiantId;
    private $colonneId;
    private $valeur;
    private $statut;
    private $saisiPar;
    private $dateSaisie;
    
    /**
     * Sauvegarde une note
     */
    public static function sauvegarder($etudiantId, $colonneId, $valeur, $statut, $professeurId) {
        $db = Database::getInstance();
        
        // Vérifier si la note existe déjà
        $query = "SELECT id FROM notes 
                  WHERE etudiant_id = ? AND colonne_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$etudiantId, $colonneId]);
        
        if ($stmt->fetch()) {
            // Mise à jour
            $query = "UPDATE notes SET 
                      valeur = ?, statut = ?, saisi_par = ?, date_modification = NOW()
                      WHERE etudiant_id = ? AND colonne_id = ?";
        } else {
            // Insertion
            $query = "INSERT INTO notes 
                     (etudiant_id, colonne_id, valeur, statut, saisi_par, date_saisie) 
                     VALUES (?, ?, ?, ?, ?, NOW())";
        }
        
        $stmt = $db->prepare($query);
        
        if ($stmt->rowCount() > 0) {
            // Historiser la modification
            self::historiser($etudiantId, $colonneId, $valeur, $statut, $professeurId);
            
            // Mettre à jour la progression
            self::updateProgression($colonneId);
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Récupère les notes d'un étudiant pour une matière
     */
    public static function getNotesEtudiant($etudiantId, $matiereId, $periodeId) {
        $db = Database::getInstance();
        $query = "SELECT n.*, cc.nom_colonne, cc.code_colonne, cc.coefficient 
                  FROM notes n
                  JOIN configuration_colonnes cc ON n.colonne_id = cc.id
                  WHERE n.etudiant_id = ? 
                  AND cc.matiere_id = ? 
                  AND cc.periode_id = ?";
        
        $stmt = $db->prepare($query);
        $stmt->execute([$etudiantId, $matiereId, $periodeId]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Importe des notes depuis un fichier Excel
     */
    public static function importerExcel($fichier, $colonneId, $professeurId) {
        $importer = new ExcelImporter();
        $donnees = $importer->lire($fichier);
        
        $resultats = [
            'succes' => 0,
            'erreurs' => []
        ];
        
        foreach ($donnees as $ligne) {
            $etudiantId = $ligne['etudiant_id'];
            $valeur = $ligne['note'];
            $statut = $ligne['statut'] ?? 'saisie';
            
            try {
                if (self::sauvegarder($etudiantId, $colonneId, $valeur, $statut, $professeurId)) {
                    $resultats['succes']++;
                }
            } catch (Exception $e) {
                $resultats['erreurs'][] = "Étudiant $etudiantId : " . $e->getMessage();
            }
        }
        
        return $resultats;
    }
    
    /**
     * Historise les modifications
     */
    private static function historiser($etudiantId, $colonneId, $nouvelleValeur, $nouveauStatut, $professeurId) {
        $db = Database::getInstance();
        
        // Récupérer l'ancienne valeur
        $query = "SELECT valeur, statut FROM notes 
                  WHERE etudiant_id = ? AND colonne_id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$etudiantId, $colonneId]);
        $ancienne = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $query = "INSERT INTO historique_notes 
                 (note_id, ancienne_valeur, nouvelle_valeur, ancien_statut, nouveau_statut, modifie_par, adresse_ip) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($query);
        $stmt->execute([
            self::getNoteId($etudiantId, $colonneId),
            $ancienne['valeur'] ?? null,
            $nouvelleValeur,
            $ancienne['statut'] ?? null,
            $nouveauStatut,
            $professeurId,
            $_SERVER['REMOTE_ADDR']
        ]);
    }
}