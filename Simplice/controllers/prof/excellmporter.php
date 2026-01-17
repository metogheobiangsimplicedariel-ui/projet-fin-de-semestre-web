<?php
// lib/ExcelImporter.php
class ExcelImporter {
    
    public function lire($fichier) {
        // Utiliser PHPExcel ou PhpSpreadsheet
        require_once 'vendor/autoload.php';
        
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadsheet = $reader->load($fichier);
        $worksheet = $spreadsheet->getActiveSheet();
        
        $donnees = [];
        $lignes = $worksheet->toArray();
        
        // Supprimer l'en-tête
        array_shift($lignes);
        
        foreach ($lignes as $ligne) {
            if (count($ligne) >= 3) {
                $donnees[] = [
                    'etudiant_id' => $ligne[0],
                    'nom' => $ligne[1],
                    'prenom' => $ligne[2],
                    'note' => $ligne[3],
                    'statut' => $ligne[4] ?? 'saisie'
                ];
            }
        }
        
        return $donnees;
    }
    
    public function genererTemplate($colonnes, $etudiants) {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // En-tête
        $sheet->setCellValue('A1', 'ID Étudiant');
        $sheet->setCellValue('B1', 'Nom');
        $sheet->setCellValue('C1', 'Prénom');
        $sheet->setCellValue('D1', 'Note');
        $sheet->setCellValue('E1', 'Statut (ABS/DIS/DEF)');
        
        // Données des étudiants
        $row = 2;
        foreach ($etudiants as $etudiant) {
            $sheet->setCellValue('A' . $row, $etudiant->getId());
            $sheet->setCellValue('B' . $row, $etudiant->getNom());
            $sheet->setCellValue('C' . $row, $etudiant->getPrenom());
            $row++;
        }
        
        // Formatage
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E' . $row)->getAlignment()->setHorizontal('center');
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        
        // Enregistrer le fichier
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_import_' . date('Y-m-d') . '.xlsx';
        $writer->save($filename);
        
        return $filename;
    }
}