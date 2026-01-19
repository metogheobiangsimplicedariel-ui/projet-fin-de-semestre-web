<?php

namespace Controllers\Admin;

use Models\Result;
use Models\Period;

// C'EST ICI QUE TOUT SE JOUE : On pointe vers le dossier vendor créé par Composer
require_once __DIR__ . '/../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class PrintController
{
    // ... Le reste du code reste identique à ce que je vous ai donné avant ...
    public function __construct()
    {
        if (!isset($_SESSION['auth']) || $_SESSION['auth']['role'] !== 'admin') {
            die("Accès interdit");
        }
    }

    public function printPV()
    {
        if (!isset($_GET['periode'])) die("Période manquante");
        $periode_id = (int)$_GET['periode'];

        // 1. Données
        $data = (new Result())->getDeliberationData($periode_id);
        $periode = (new Period())->getById($periode_id);

        $matieres = $data['matieres'];
        $etudiants = $data['etudiants'];
        $moyennes = $data['moyennes'];

        // 2. HTML
        ob_start();
        require 'views/admin/print/pv_pdf.php';
        $html = ob_get_clean();

        // 3. PDF Generation
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $dompdf->stream("PV_" . $periode['code'] . ".pdf", ["Attachment" => false]);
    }
    // Générer le Bulletin Individuel
    public function printBulletin()
    {
        if (!isset($_GET['periode']) || !isset($_GET['etudiant'])) die("Paramètres manquants");

        $periode_id = (int)$_GET['periode'];
        $etudiant_id = (int)$_GET['etudiant'];

        // 1. Récupérer les infos
        $periode = (new Period())->getById($periode_id);

        // On récupère l'étudiant via une requête simple (ou via votre modèle User)
        $pdo = \Config\Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id = ?");
        $stmt->execute([$etudiant_id]);
        $etudiant = $stmt->fetch();

        // On récupère TOUTES les données de la période (comme pour le PV)
        // C'est le plus simple, ensuite on filtrera juste les notes de cet étudiant
        $data = (new Result())->getDeliberationData($periode_id);
        $matieres = $data['matieres'];
        $moyennes = $data['moyennes']; // Tableau global [etud][matiere]

        // On isole les notes de CET étudiant
        $mesNotes = $moyennes[$etudiant_id] ?? [];

        // 2. Temporisation HTML
        ob_start();
        require 'views/admin/print/bulletin_pdf.php';
        $html = ob_get_clean();

        // 3. Génération PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Helvetica');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // Portrait pour un bulletin individuel
        $dompdf->setPaper('A4', 'portrait');

        $dompdf->render();

        $filename = "Bulletin_" . strtoupper($etudiant['nom']) . "_" . $periode['code'] . ".pdf";
        $dompdf->stream($filename, ["Attachment" => false]);
    }
}
