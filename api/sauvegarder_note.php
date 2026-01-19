<?php
// web/api/sauvegarder_note.php
ini_set('display_errors', 0);
error_reporting(E_ALL);
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['auth']['id'])) {
    echo json_encode(['success' => false, 'message' => 'Session expirée']);
    exit;
}
$professeurId = $_SESSION['auth']['id'];

require_once __DIR__ . '/../config/database.php';

// Inclusions des modèles
if (file_exists(__DIR__ . '/../models/PermissionModel.php')) require_once __DIR__ . '/../models/PermissionModel.php';
if (file_exists(__DIR__ . '/../models/NoteModel.php')) require_once __DIR__ . '/../models/NoteModel.php';

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'JSON invalide']);
    exit;
}

$etudiantId = (int)$data['etudiant_id'];
$colonneId  = (int)$data['colonne_id'];
$valeur     = $data['valeur'];
if ($valeur === '') $valeur = null;

try {
    // Instance DB
    $db = \Config\Database::getConnection();

    // Instance NoteModel
    $noteModel = new \Models\NoteModel();

    // Vérification existence
    $check = $db->prepare("SELECT COUNT(*) FROM notes WHERE etudiant_id = ? AND colonne_id = ?");
    $check->execute([$etudiantId, $colonneId]);
    $exists = $check->fetchColumn() > 0;

    // --- VERSION AVEC 'E' (saisie_par) ---
    if ($exists) {
        $sql = "UPDATE notes SET valeur = ?, date_saisie = NOW(), saisie_par = ? WHERE etudiant_id = ? AND colonne_id = ?";
        $stmt = $db->prepare($sql);
        $res = $stmt->execute([$valeur, $professeurId, $etudiantId, $colonneId]);
    } else {
        $sql = "INSERT INTO notes (etudiant_id, colonne_id, valeur, date_saisie, saisie_par) VALUES (?, ?, ?, NOW(), ?)";
        $stmt = $db->prepare($sql);
        $res = $stmt->execute([$etudiantId, $colonneId, $valeur, $professeurId]);
    }

    if ($res) {
        // --- MISE À JOUR DE LA PROGRESSION ---
        $stmtInfo = $db->prepare("SELECT matiere_id, periode_id FROM configuration_colonnes WHERE id = ?");
        $stmtInfo->execute([$colonneId]);
        $info = $stmtInfo->fetch();

        if ($info) {
            // Attention : Si votre NoteModel utilise aussi des requêtes SQL, 
            // vérifiez qu'elles utilisent bien la bonne orthographe.
            $noteModel->mettreAJourProgression($professeurId, $info['matiere_id'], $info['periode_id']);
        }

        echo json_encode(['success' => true]);
    } else {
        $err = $stmt->errorInfo();
        echo json_encode(['success' => false, 'message' => 'Erreur SQL: ' . $err[2]]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
}
