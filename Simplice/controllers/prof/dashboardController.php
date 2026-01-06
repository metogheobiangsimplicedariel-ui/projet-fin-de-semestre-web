<?php
// controllers/prof/dashboardController.php
require_once __DIR__ . '/../../config/database.php';

// 1. SÉCURITÉ
if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'professeur') {
    header('Location: index.php?page=login');
    exit();
}
$prof_id = $_SESSION['auth']['id'];

// 2. RÉCUPÉRER TOUTES LES PÉRIODES (Pour le menu déroulant)
$stmt = $pdo->query("SELECT * FROM periodes ORDER BY annee_universitaire DESC, date_debut_saisie DESC");
$toutes_les_periodes = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($toutes_les_periodes)) {
    die("Aucune période configurée dans le système.");
}

// 3. DÉTERMINER LA PÉRIODE ACTIVE
$periode = null;

if (isset($_GET['periode'])) {
    // A. Si l'utilisateur a cliqué sur une période spécifique
    foreach ($toutes_les_periodes as $p) {
        if ($p['id'] == $_GET['periode']) {
            $periode = $p;
            break;
        }
    }
}

// B. Si pas de choix (ou ID invalide), on cherche la période "Par défaut" intelligente
if (!$periode) {
    // 1. D'abord, on regarde si une période est "OUVERTE" (Priorité absolue)
    foreach ($toutes_les_periodes as $p) {
        if ($p['statut'] === 'ouverte') {
            $periode = $p;
            break;
        }
    }
    
    // 2. Si aucune ouverte, on regarde celle où le prof A DÉJÀ des affectations
    if (!$periode) {
        $check = $pdo->prepare("SELECT periode_id FROM affectations_profs WHERE professeur_id = ? LIMIT 1");
        $check->execute([$prof_id]);
        $p_id_found = $check->fetchColumn();
        
        if ($p_id_found) {
            foreach ($toutes_les_periodes as $p) {
                if ($p['id'] == $p_id_found) {
                    $periode = $p;
                    break;
                }
            }
        }
    }

    // 3. Si toujours rien, on prend la plus récente (la première de la liste)
    if (!$periode) {
        $periode = $toutes_les_periodes[0];
    }
}

// 4. RÉCUPÉRATION DES MATIÈRES (Pour la période $periode['id'])
$sql = "SELECT m.*, f.nom as nom_filiere, f.code as code_filiere, ap.groupe, ap.id as affectation_id
        FROM affectations_profs ap
        JOIN matieres m ON ap.matiere_id = m.id
        JOIN filieres f ON m.filiere_id = f.id
        WHERE ap.professeur_id = ? AND ap.periode_id = ?
        ORDER BY f.code, m.nom";

$stmt = $pdo->prepare($sql);
$stmt->execute([$prof_id, $periode['id']]);
$mes_matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. RÉCUPÉRATION DES COLONNES (Structure des notes)
foreach ($mes_matieres as &$matiere) {
    $stmt = $pdo->prepare("SELECT nom_colonne, note_max, coefficient 
                           FROM configuration_colonnes 
                           WHERE matiere_id = ? AND periode_id = ? 
                           ORDER BY ordre ASC");
    $stmt->execute([$matiere['id'], $periode['id']]);
    $matiere['colonnes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
unset($matiere);

// Appel de la vue
require __DIR__ . '/../../views/prof/dashboard.php';
?>