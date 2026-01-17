<?php
// controllers/ProfesseurController.php
class ProfesseurController {
    
    public function dashboard() {
        // Vérifier l'authentification
        Auth::verifierRole('professeur');
        
        $professeur = $_SESSION['utilisateur'];
        $periodeActive = Periode::getActive();
        
        // Récupérer les matières assignées
        $matieres = $professeur->getMatieresAssignees($periodeActive['id']);
        
        // Récupérer les progressions
        $progressions = [];
        foreach ($matieres as $matiere) {
            $progressions[$matiere->getId()] = $professeur->getProgressionSaisie(
                $matiere->getId(),
                $periodeActive['id']
            );
        }
        
        // Afficher la vue
        require_once 'views/professeur/dashboard.php';
    }
    
    public function matieres() {
        Auth::verifierRole('professeur');
        
        $professeur = $_SESSION['utilisateur'];
        $periodeId = $_GET['periode_id'] ?? Periode::getActive()['id'];
        
        $matieres = $professeur->getMatieresAssignees($periodeId);
        $periode = Periode::getById($periodeId);
        
        require_once 'views/professeur/matieres.php';
    }
    
    public function saisieNotes() {
        Auth::verifierRole('professeur');
        
        $professeur = $_SESSION['utilisateur'];
        $matiereId = $_GET['matiere_id'];
        $periodeId = $_GET['periode_id'];
        
        // Vérifier les permissions
        if (!$professeur->peutSaisirNotes($matiereId, $periodeId)) {
            Session::setFlash('error', 'Vous n\'êtes pas autorisé à saisir des notes pour cette matière.');
            header('Location: index.php?action=matieres');
            exit;
        }
        
        // Vérifier si la période est ouverte
        $periode = Periode::getById($periodeId);
        if ($periode['statut'] !== 'ouverte') {
            Session::setFlash('error', 'La période de saisie est fermée.');
            header('Location: index.php?action=matieres');
            exit;
        }
        
        $matiere = Matiere::getById($matiereId);
        $colonnes = $matiere->getColonnesConfigurees($periodeId);
        $etudiants = $matiere->getEtudiantsInscrits($periodeId);
        
        // Récupérer les notes existantes
        $notesExistantes = [];
        foreach ($etudiants as $etudiant) {
            $notesExistantes[$etudiant->getId()] = Note::getNotesEtudiant(
                $etudiant->getId(),
                $matiereId,
                $periodeId
            );
        }
        
        require_once 'views/professeur/saisie_notes.php';
    }
    
    public function sauvegarderNote() {
        Auth::verifierRole('professeur');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        $professeurId = $_SESSION['utilisateur']->getId();
        $etudiantId = $_POST['etudiant_id'];
        $colonneId = $_POST['colonne_id'];
        $valeur = $_POST['valeur'];
        $statut = $_POST['statut'] ?? 'saisie';
        
        // Validation
        if (!is_numeric($valeur) && !in_array($valeur, ['ABS', 'DIS', 'DEF'])) {
            echo json_encode(['success' => false, 'message' => 'Valeur invalide']);
            exit;
        }
        
        // Validation de la plage
        $colonne = ConfigurationColonne::getById($colonneId);
        if (is_numeric($valeur) && ($valeur < 0 || $valeur > $colonne->getNoteMax())) {
            echo json_encode(['success' => false, 'message' => 'Note hors plage autorisée']);
            exit;
        }
        
        try {
            $success = Note::sauvegarder($etudiantId, $colonneId, $valeur, $statut, $professeurId);
            
            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Note sauvegardée']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Erreur de sauvegarde']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
    
    public function importerNotes() {
        Auth::verifierRole('professeur');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professeurId = $_SESSION['utilisateur']->getId();
            $colonneId = $_POST['colonne_id'];
            
            if (isset($_FILES['fichier_excel']) && $_FILES['fichier_excel']['error'] === 0) {
                $resultat = Note::importerExcel($_FILES['fichier_excel']['tmp_name'], $colonneId, $professeurId);
                
                if ($resultat['succes'] > 0) {
                    Session::setFlash('success', 
                        "Import réussi : {$resultat['succes']} notes importées.");
                }
                
                if (!empty($resultat['erreurs'])) {
                    Session::setFlash('warning', 
                        "Erreurs : " . implode(', ', $resultat['erreurs']));
                }
            }
            
            header('Location: index.php?action=import&matiere_id=' . $_POST['matiere_id']);
            exit;
        }
        
        $matiereId = $_GET['matiere_id'];
        $periodeId = $_GET['periode_id'];
        $matiere = Matiere::getById($matiereId);
        $colonnes = $matiere->getColonnesConfigurees($periodeId);
        
        require_once 'views/professeur/import_notes.php';
    }
    
    public function visualiserResultats() {
        Auth::verifierRole('professeur');
        
        $matiereId = $_GET['matiere_id'];
        $periodeId = $_GET['periode_id'];
        $matiere = Matiere::getById($matiereId);
        
        $etudiants = $matiere->getEtudiantsInscrits($periodeId);
        $colonnes = $matiere->getColonnesConfigurees($periodeId);
        $formule = $matiere->getFormuleCalcul($periodeId);
        
        // Calculer les moyennes
        $resultats = [];
        foreach ($etudiants as $etudiant) {
            $notes = Note::getNotesEtudiant($etudiant->getId(), $matiereId, $periodeId);
            
            // Préparer les valeurs pour le parser
            $valeurs = [];
            foreach ($notes as $note) {
                $valeurs[$note['code_colonne']] = $note['valeur'];
            }
            
            // Calculer la moyenne
            $parser = new FormulaParser();
            $moyenne = $parser->evaluer($formule, $valeurs);
            
            $resultats[] = [
                'etudiant' => $etudiant,
                'notes' => $notes,
                'moyenne' => $moyenne,
                'validation' => $moyenne >= $matiere->getSeuilValidation()
            ];
        }
        
        require_once 'views/professeur/statistiques.php';
    }
    
    public function validerSaisie() {
        Auth::verifierRole('professeur');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $professeurId = $_SESSION['utilisateur']->getId();
            $matiereId = $_POST['matiere_id'];
            $periodeId = $_POST['periode_id'];
            
            // Vérifier que toutes les notes sont saisies
            $matiere = Matiere::getById($matiereId);
            $etudiants = $matiere->getEtudiantsInscrits($periodeId);
            $colonnes = $matiere->getColonnesConfigurees($periodeId);
            
            $notesManquantes = [];
            foreach ($etudiants as $etudiant) {
                foreach ($colonnes as $colonne) {
                    $note = Note::getNote($etudiant->getId(), $colonne->getId());
                    if (!$note && $colonne->isObligatoire()) {
                        $notesManquantes[] = $etudiant->getNomComplet();
                        break;
                    }
                }
            }
            
            if (!empty($notesManquantes)) {
                Session::setFlash('error', 
                    "Notes manquantes pour : " . implode(', ', $notesManquantes));
                header('Location: index.php?action=saisie&matiere_id=' . $matiereId);
                exit;
            }
            
            // Marquer comme validé
            $db = Database::getInstance();
            $query = "UPDATE progression_saisie SET 
                      valide_par_prof = TRUE, date_validation = NOW()
                      WHERE matiere_id = ? AND periode_id = ? AND professeur_id = ?";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$matiereId, $periodeId, $professeurId]);
            
            // Notifier l'admin
            Notification::envoyer(
                'admin',
                "Saisie validée",
                "Le professeur a validé la saisie pour la matière " . $matiere->getNom()
            );
            
            Session::setFlash('success', 'Saisie validée avec succès.');
            header('Location: index.php?action=dashboard');
            exit;
        }
        
        require_once 'views/professeur/validation.php';
    }
}