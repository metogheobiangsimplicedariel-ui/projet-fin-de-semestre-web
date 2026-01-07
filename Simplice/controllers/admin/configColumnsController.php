<?php
require_once __DIR__ . '/../../config/database.php';

// 1. SÉCURITÉ
if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
    header('Location: index.php?page=login');
    exit();
}

// 2. VÉRIFICATION DE LA PÉRIODE
if (!isset($_GET['periode'])) {
    header('Location: index.php?page=admin_periods');
    exit();
}
$periode_id = (int)$_GET['periode'];

// Infos de la période
$stmt = $pdo->prepare("SELECT * FROM periodes WHERE id = ?");
$stmt->execute([$periode_id]);
$periode = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periode) {
    header('Location: index.php?page=admin_periods');
    exit();
}

// 3. RÉCUPÉRATION DES MATIÈRES (Avec le nom de la Filière !)
// On joint la table filieres pour afficher "Maths - Licence 1"
$sql = "SELECT m.*, f.nom as nom_filiere, f.code as code_filiere 
        FROM matieres m 
        JOIN filieres f ON m.filiere_id = f.id 
        ORDER BY f.nom ASC, m.nom ASC";
$stmt = $pdo->query($sql);
$matieres = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Matière sélectionnée par défaut
$current_matiere_id = isset($_GET['matiere']) ? (int)$_GET['matiere'] : ($matieres[0]['id'] ?? 0);

// Trouver infos matière courante
$current_matiere = null;
foreach ($matieres as $m) {
    if ($m['id'] === $current_matiere_id) {
        $current_matiere = $m;
        break;
    }
}

// 4. TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // --- AJOUT COLONNE ---
    if (isset($_POST['action']) && $_POST['action'] === 'add_column') {
        try {
            // Calcul de l'ordre automatique (MAX + 1)
            $stmtOrder = $pdo->prepare("SELECT MAX(ordre) FROM configuration_colonnes WHERE matiere_id = ? AND periode_id = ?");
            $stmtOrder->execute([$current_matiere_id, $periode_id]);
            $maxOrder = $stmtOrder->fetchColumn();
            $newOrder = $maxOrder ? $maxOrder + 1 : 1;

            $sql = "INSERT INTO configuration_colonnes 
                    (matiere_id, periode_id, nom_colonne, code_colonne, type, note_max, coefficient, obligatoire, ordre) 
                    VALUES (:mid, :pid, :nom, :code, :type, :max, :coeff, :obl, :ordre)";
            
            $req = $pdo->prepare($sql);
            $req->execute([
                ':mid'   => $current_matiere_id,
                ':pid'   => $periode_id,
                ':nom'   => $_POST['nom_colonne'],
                ':code'  => strtoupper($_POST['code_colonne']), // Toujours en majuscule ex: DS1
                ':type'  => $_POST['type'],
                ':max'   => $_POST['note_max'],
                ':coeff' => $_POST['coefficient'],
                ':obl'   => isset($_POST['obligatoire']) ? 1 : 0,
                ':ordre' => $newOrder
            ]);
            
            $_SESSION['success'] = "Colonne ajoutée avec succès !";
        } catch (PDOException $e) {
            // Gestion de l'erreur UNIQUE (si le code existe déjà)
            if ($e->getCode() == 23000) {
                $_SESSION['error'] = "Erreur : Ce code colonne (ex: DS1) existe déjà pour cette matière.";
            } else {
                $_SESSION['error'] = "Erreur SQL : " . $e->getMessage();
            }
        }
        
        header("Location: index.php?page=admin_config_cols&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }

    // --- SUPPRESSION COLONNE ---
    if (isset($_POST['action']) && $_POST['action'] === 'delete_column') {
        $col_id = (int)$_POST['config_id'];
        $req = $pdo->prepare("DELETE FROM configuration_colonnes WHERE id = ? AND periode_id = ?");
        $req->execute([$col_id, $periode_id]);
        
        $_SESSION['success'] = "Colonne supprimée !";
        header("Location: index.php?page=admin_config_cols&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }
}

// 5. RÉCUPÉRATION DES COLONNES EXISTANTES
// On utilise ta table 'configuration_colonnes'
$stmt = $pdo->prepare("SELECT * FROM configuration_colonnes WHERE periode_id = ? AND matiere_id = ? ORDER BY ordre ASC");
$stmt->execute([$periode_id, $current_matiere_id]);
$colonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 5. TRAITEMENT DU FORMULAIRE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // =========================================================
    // 1. AJOUT DE COLONNE
    // =========================================================
    if (isset($_POST['action']) && $_POST['action'] === 'add_column') {
        // ... (Ton code d'ajout existant reste ici) ...
        // (Assure-toi juste de rediriger vers la bonne page à la fin)
    }

    // =========================================================
    // 2. MODIFICATION (Sécurisée)
    // =========================================================
    if (isset($_POST['action']) && $_POST['action'] === 'edit_column') {
        $col_id = (int)$_POST['column_id'];
        
        // A. Vérifier si des notes existent déjà
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE colonne_id = ?");
        $stmt->execute([$col_id]);
        $has_notes = $stmt->fetchColumn() > 0;

        if ($has_notes) {
            $_SESSION['error'] = "Impossible de modifier : des notes ont déjà été saisies pour cette colonne.";
        } else {
            // B. Update
            $sql = "UPDATE configuration_colonnes SET 
                    nom_colonne = :nom, code_colonne = :code, type = :type, 
                    note_max = :max, coefficient = :coeff, obligatoire = :obl 
                    WHERE id = :id";
            $req = $pdo->prepare($sql);
            $req->execute([
                ':nom'   => $_POST['nom_colonne'],
                ':code'  => strtoupper($_POST['code_colonne']),
                ':type'  => $_POST['type'],
                ':max'   => $_POST['note_max'],
                ':coeff' => $_POST['coefficient'],
                ':obl'   => isset($_POST['obligatoire']) ? 1 : 0,
                ':id'    => $col_id
            ]);
            $_SESSION['success'] = "Colonne modifiée avec succès.";
        }
        header("Location: index.php?page=admin_config_cols&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }

    // =========================================================
    // 3. SUPPRESSION (Sécurisée)
    // =========================================================
    if (isset($_POST['action']) && $_POST['action'] === 'delete_column') {
        $col_id = (int)$_POST['config_id'];
        
        // A. Vérifier notes
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM notes WHERE colonne_id = ?");
        $stmt->execute([$col_id]);
        
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error'] = "Suppression impossible : Des notes sont liées à cette colonne.";
        } else {
            $req = $pdo->prepare("DELETE FROM configuration_colonnes WHERE id = ? AND periode_id = ?");
            $req->execute([$col_id, $periode_id]);
            $_SESSION['success'] = "Colonne supprimée !";
        }
        header("Location: index.php?page=admin_config_cols&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }

    // =========================================================
    // 4. DUPLICATION (Copier d'une autre matière/période)
    // =========================================================
    if (isset($_POST['action']) && $_POST['action'] === 'duplicate_config') {
        $source_matiere_id = (int)$_POST['source_matiere_id'];
        // Note: Pour simplifier, on copie depuis la même période ou une autre, 
        // mais le formulaire enverra l'ID matière source.
        
        // 1. Récupérer les colonnes sources
        $stmt = $pdo->prepare("SELECT * FROM configuration_colonnes WHERE matiere_id = ? AND periode_id = ?");
        // Ici je suppose qu'on copie depuis la MEME période pour l'instant (simplification)
        // Pour copier d'un autre semestre, il faudrait un champ 'source_periode_id' dans le formulaire
        $stmt->execute([$source_matiere_id, $periode_id]); 
        $sources = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($sources)) {
            $_SESSION['error'] = "Aucune configuration trouvée sur la matière source.";
        } else {
            $count = 0;
            foreach ($sources as $src) {
                // On vérifie que le code n'existe pas déjà dans la destination
                $check = $pdo->prepare("SELECT id FROM configuration_colonnes WHERE matiere_id = ? AND periode_id = ? AND code_colonne = ?");
                $check->execute([$current_matiere_id, $periode_id, $src['code_colonne']]);
                
                if (!$check->fetch()) {
                    $ins = $pdo->prepare("INSERT INTO configuration_colonnes (matiere_id, periode_id, nom_colonne, code_colonne, type, note_max, coefficient, obligatoire, ordre) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $ins->execute([
                        $current_matiere_id, $periode_id, $src['nom_colonne'], $src['code_colonne'], 
                        $src['type'], $src['note_max'], $src['coefficient'], $src['obligatoire'], $src['ordre']
                    ]);
                    $count++;
                }
            }
            $_SESSION['success'] = "$count colonnes dupliquées avec succès !";
        }
        header("Location: index.php?page=admin_config_cols&periode=$periode_id&matiere=$current_matiere_id");
        exit();
    }
    
    // =========================================================
    // 5. RÉORGANISATION (DRAG & DROP - AJAX)
    // =========================================================
    if (isset($_POST['action']) && $_POST['action'] === 'reorder_columns') {
        // On reçoit un tableau d'IDs dans le bon ordre : [12, 5, 8, 3]
        $order = $_POST['order']; 
        
        if (is_array($order)) {
            $sql = "UPDATE configuration_colonnes SET ordre = :ordre WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            
            foreach ($order as $index => $id) {
                $stmt->execute([
                    ':ordre' => $index + 1, // Ordre commence à 1
                    ':id' => $id
                ]);
            }
        }
        // Pas de redirection ici car c'est un appel AJAX (Javascript)
        echo json_encode(['status' => 'success']);
        exit();
    }
}

// ... (La suite du fichier : récupération des colonnes, require Vue, etc.) ...

require __DIR__ . '/../../views/admin/periods/config_columns.php';
?>

