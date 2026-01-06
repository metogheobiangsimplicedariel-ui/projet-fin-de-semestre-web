<?php
require_once __DIR__ . '/../../config/database.php';

// 1. SÉCURITÉ
if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
    header('Location: index.php?page=login');
    exit();
}

// 2. VÉRIFICATION DE LA PÉRIODE (Obligatoire)
if (!isset($_GET['periode'])) {
    // Si pas de période, on redirige vers la liste des périodes ou on prend la plus récente
    $stmt = $pdo->query("SELECT id FROM periodes WHERE statut != 'fermee' ORDER BY date_debut_saisie DESC LIMIT 1");
    $p = $stmt->fetchColumn();
    if($p) {
        header("Location: index.php?page=admin_assignments&periode=$p");
        exit();
    } else {
        header('Location: index.php?page=admin_periods');
        exit();
    }
}
$periode_id = (int)$_GET['periode'];

// Infos Période
$stmt = $pdo->prepare("SELECT * FROM periodes WHERE id = ?");
$stmt->execute([$periode_id]);
$periode = $stmt->fetch(PDO::FETCH_ASSOC);

// 3. LISTE DES MATIÈRES (Menu Gauche)
$sql = "SELECT m.*, f.code as code_filiere 
        FROM matieres m 
        JOIN filieres f ON m.filiere_id = f.id 
        ORDER BY f.code ASC, m.nom ASC";
$matieres = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Matière sélectionnée
$current_matiere_id = isset($_GET['matiere']) ? (int)$_GET['matiere'] : ($matieres[0]['id'] ?? 0);

// Trouver nom matière courante
$current_matiere = null;
foreach ($matieres as $m) {
    if ($m['id'] === $current_matiere_id) $current_matiere = $m;
}

// 4. TRAITEMENT DES FORMULAIRES
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- A. AFFECTATION PROFESSEUR ---
    if (isset($_POST['action']) && $_POST['action'] === 'assign_prof') {
        $prof_id = (int)$_POST['prof_id'];
        $groupe = htmlspecialchars($_POST['groupe']); // Ex: "CM", "TD A", "Tous"

        try {
            $sql = "INSERT INTO affectations_profs (professeur_id, matiere_id, periode_id, groupe) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$prof_id, $current_matiere_id, $periode_id, $groupe]);
            $_SESSION['success'] = "Professeur affecté avec succès !";
        } catch (PDOException $e) {
            $_SESSION['error'] = "Ce professeur est déjà affecté à ce groupe.";
        }
        header("Location: index.php?page=admin_assignments&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }

    // --- B. SUPPRESSION PROFESSEUR ---
    if (isset($_POST['action']) && $_POST['action'] === 'remove_prof') {
        $aff_id = (int)$_POST['affectation_id'];
        $pdo->prepare("DELETE FROM affectations_profs WHERE id = ?")->execute([$aff_id]);
        $_SESSION['success'] = "Affectation supprimée.";
        header("Location: index.php?page=admin_assignments&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }

    // --- C. INSCRIPTION ÉTUDIANT (MULTI-SELECT) ---
    if (isset($_POST['action']) && $_POST['action'] === 'enroll_students') {
        $etudiants = $_POST['etudiants'] ?? []; // Tableau d'IDs
        $groupe = htmlspecialchars($_POST['groupe']);
        
        $count = 0;
        $sql = "INSERT IGNORE INTO inscriptions_matieres (etudiant_id, matiere_id, periode_id, groupe) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        foreach ($etudiants as $etu_id) {
            $stmt->execute([$etu_id, $current_matiere_id, $periode_id, $groupe]);
            if ($stmt->rowCount() > 0) $count++;
        }
        
        $_SESSION['success'] = "$count étudiant(s) inscrit(s) dans le groupe $groupe !";
        header("Location: index.php?page=admin_assignments&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }
    
    // --- D. DÉSINSCRIPTION ÉTUDIANT ---
    if (isset($_POST['action']) && $_POST['action'] === 'remove_student') {
        $ins_id = (int)$_POST['inscription_id'];
        $pdo->prepare("DELETE FROM inscriptions_matieres WHERE id = ?")->execute([$ins_id]);
        header("Location: index.php?page=admin_assignments&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }
}

// 5. RÉCUPÉRATION DES DONNÉES

// A. Profs déjà affectés
$sql = "SELECT ap.*, u.nom, u.prenom, u.email 
        FROM affectations_profs ap 
        JOIN utilisateurs u ON ap.professeur_id = u.id 
        WHERE ap.matiere_id = ? AND ap.periode_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$current_matiere_id, $periode_id]);
$profs_affectes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// B. Étudiants inscrits
$sql = "SELECT im.*, u.nom, u.prenom, u.email 
        FROM inscriptions_matieres im 
        JOIN utilisateurs u ON im.etudiant_id = u.id 
        WHERE im.matiere_id = ? AND im.periode_id = ?
        ORDER BY im.groupe, u.nom";
$stmt = $pdo->prepare($sql);
$stmt->execute([$current_matiere_id, $periode_id]);
$etudiants_inscrits = $stmt->fetchAll(PDO::FETCH_ASSOC);

// C. Listes pour les formulaires (Tous les profs / Tous les étudiants)
$all_profs = $pdo->query("SELECT id, nom, prenom FROM utilisateurs WHERE role = 'professeur' ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

// Pour les étudiants, on essaie de ne récupérer que ceux PAS encore inscrits à cette matière pour alléger
// Mais pour faire simple ici, on charge tout ceux de la filière si possible, sinon tous.
// Astuce : On récupère tous les étudiants actifs
$all_students = $pdo->query("SELECT id, nom, prenom, email FROM utilisateurs WHERE role = 'etudiant' AND actif = 1 ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../../views/admin/assignments/index.php';
?>