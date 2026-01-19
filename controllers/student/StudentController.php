<?php

namespace Controllers\Student;

use Config\Database;
use PDO;

require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class StudentController
{
    public function dashboard()
    {
        // 1. Sécurité
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'etudiant') {
            header('Location: index.php?page=login');
            exit();
        }

        $etudiantId = $_SESSION['auth']['id'];
        $db = Database::getConnection();

        // =========================================================================
        // 2. RÉCUPÉRATION INTELLIGENTE DE LA PÉRIODE
        // =========================================================================

        // Priorité 1 : On cherche la dernière période PUBLIÉE où l'étudiant est inscrit
        // (Cela permet d'afficher le Semestre 1 même si le Semestre 2 a déjà commencé)
        $sql = "SELECT p.* FROM periodes p
                INNER JOIN inscriptions_matieres im ON p.id = im.periode_id
                WHERE im.etudiant_id = ? AND p.resultats_publies = 1
                ORDER BY p.date_debut_saisie DESC 
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$etudiantId]);
        $periode = $stmt->fetch(PDO::FETCH_ASSOC);

        // Priorité 2 : Si rien n'est publié, on prend la dernière période où il est inscrit
        // (Pour afficher l'écran "En attente de publication" du semestre en cours)
        if (!$periode) {
            $sqlFallback = "SELECT p.* FROM periodes p
                            INNER JOIN inscriptions_matieres im ON p.id = im.periode_id
                            WHERE im.etudiant_id = ?
                            ORDER BY p.date_debut_saisie DESC 
                            LIMIT 1";
            $stmt = $db->prepare($sqlFallback);
            $stmt->execute([$etudiantId]);
            $periode = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Priorité 3 : Filet de sécurité (Si pas d'inscription trouvée du tout)
        if (!$periode) {
            $periode = $db->query("SELECT * FROM periodes ORDER BY date_debut_saisie DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
        }

        $periodeId = $periode['id'] ?? 0;

        // =========================================================================
        // 3. VÉRIFICATION DU STATUT (Logique Double Contrôle)
        // =========================================================================
        $resultatsAccessibles = false;

        // On vérifie la colonne 'resultats_publies' (et non le statut 'ouverte'/'fermee')
        if ($periode && isset($periode['resultats_publies']) && $periode['resultats_publies'] == 1) {
            $resultatsAccessibles = true;
        }

        // Variables par défaut
        $bulletin = [];
        $moyenneGenerale = null;
        $rang = '--';
        $ectsValides = 0;

        // 4. Si accessible, on récupère les notes
        if ($resultatsAccessibles && $periodeId > 0) {

            // A. Récupérer les matières
            $sqlMatieres = "SELECT m.id, m.nom, m.code, m.coefficient, m.credits, moy.moyenne as moyenne_matiere
                            FROM inscriptions_matieres im
                            JOIN matieres m ON im.matiere_id = m.id
                            LEFT JOIN moyennes moy ON (moy.etudiant_id = im.etudiant_id AND moy.matiere_id = m.id AND moy.periode_id = im.periode_id)
                            WHERE im.etudiant_id = ? AND im.periode_id = ?
                            ORDER BY m.nom ASC";

            $stmtMat = $db->prepare($sqlMatieres);
            $stmtMat->execute([$etudiantId, $periodeId]);
            $matieres = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

            $totalPoints = 0;
            $totalCoeff = 0;

            // B. Récupérer les notes détaillées
            foreach ($matieres as $m) {
                $sqlNotes = "SELECT cc.code_colonne, n.valeur, cc.note_max
                             FROM configuration_colonnes cc
                             LEFT JOIN notes n ON (cc.id = n.colonne_id AND n.etudiant_id = ?)
                             WHERE cc.matiere_id = ? AND cc.periode_id = ?
                             ORDER BY cc.ordre ASC";

                $stmtNotes = $db->prepare($sqlNotes);
                $stmtNotes->execute([$etudiantId, $m['id'], $periodeId]);
                $details = $stmtNotes->fetchAll(PDO::FETCH_ASSOC);

                if ($m['moyenne_matiere'] !== null) {
                    $moy = floatval($m['moyenne_matiere']);
                    $totalPoints += $moy * $m['coefficient'];
                    $totalCoeff += $m['coefficient'];
                    if ($moy >= 10) $ectsValides += $m['credits'];
                }

                $bulletin[] = [
                    'matiere' => $m,
                    'details' => $details,
                    'moyenne' => $m['moyenne_matiere']
                ];
            }

            // C. Moyenne Générale
            if ($totalCoeff > 0) {
                $moyenneGenerale = number_format($totalPoints / $totalCoeff, 2);
            }
        }

        // 5. On envoie l'état à la vue
        $affichageBloque = !$resultatsAccessibles;

        require 'views/dashboard.php';
    }

    // --- NOUVELLE MÉTHODE ---
    public function downloadBulletin()
    {
        // 1. Sécurité
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'etudiant') {
            header('Location: index.php?page=login');
            exit();
        }

        $etudiantId = $_SESSION['auth']['id'];
        $db = Database::getConnection();

        // 2. Récupérer la période (La même logique que le Dashboard)
        // On ne permet le téléchargement QUE si c'est publié
        $sql = "SELECT p.* FROM periodes p
                INNER JOIN inscriptions_matieres im ON p.id = im.periode_id
                WHERE im.etudiant_id = ? AND p.resultats_publies = 1
                ORDER BY p.date_debut_saisie DESC 
                LIMIT 1";
        $stmt = $db->prepare($sql);
        $stmt->execute([$etudiantId]);
        $periode = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$periode) {
            die("Aucun bulletin disponible pour le moment.");
        }
        $periodeId = $periode['id'];

        // 3. Récupérer les infos de l'étudiant
        $stmtUser = $db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmtUser->execute([$etudiantId]);
        $etudiant = $stmtUser->fetch(PDO::FETCH_ASSOC);

        // 4. Récupérer les Matières et les Moyennes pour le PDF
        // Le PDF attend un tableau simple : $mesNotes[matiere_id] = moyenne
        $sqlMatieres = "SELECT m.id, m.nom, m.code, m.coefficient, moy.moyenne
                        FROM inscriptions_matieres im
                        JOIN matieres m ON im.matiere_id = m.id
                        LEFT JOIN moyennes moy ON (moy.etudiant_id = im.etudiant_id AND moy.matiere_id = m.id AND moy.periode_id = im.periode_id)
                        WHERE im.etudiant_id = ? AND im.periode_id = ?
                        ORDER BY m.nom ASC";

        $stmtMat = $db->prepare($sqlMatieres);
        $stmtMat->execute([$etudiantId, $periodeId]);
        $rows = $stmtMat->fetchAll(PDO::FETCH_ASSOC);

        // Formatage pour la vue PDF
        $matieres = []; // Liste des objets matières
        $mesNotes = []; // Map [id => note]

        foreach ($rows as $row) {
            $matieres[] = [
                'id' => $row['id'],
                'nom' => $row['nom'],
                'code' => $row['code'],
                'coefficient' => $row['coefficient']
            ];
            // Si la moyenne est null, on ne met rien, ou null
            $mesNotes[$row['id']] = $row['moyenne'];
        }

        // 5. Génération du PDF
        // On réutilise la vue 'views/admin/print/bulletin_pdf.php' car elle est déjà prête !
        ob_start();
        require 'views/admin/print/bulletin_pdf.php';
        $html = ob_get_clean();

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Téléchargement direct
        $filename = "Bulletin_" . $periode['code'] . ".pdf";
        $dompdf->stream($filename, ["Attachment" => true]); // true = forcer le téléchargement
    }
}
