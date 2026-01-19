<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Relevé de Notes</title>
    <style>
        body {
            font-family: Helvetica, sans-serif;
            color: #333;
            font-size: 12px;
        }

        .container {
            width: 100%;
            padding: 20px;
        }

        /* En-tête */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo-box {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
        }

        .info-box {
            display: table-cell;
            vertical-align: middle;
            width: 50%;
            text-align: right;
        }

        h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }

        h2 {
            margin: 5px 0 0;
            font-size: 14px;
            font-weight: normal;
            color: #555;
        }

        /* Info Étudiant */
        .student-box {
            background-color: #f4f6f8;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
            border: 1px solid #ddd;
        }

        .student-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* Tableau */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background-color: #333;
            color: white;
            text-align: left;
            padding: 8px;
            text-transform: uppercase;
            font-size: 10px;
        }

        td {
            border-bottom: 1px solid #ddd;
            padding: 8px;
        }

        .col-note {
            text-align: center;
            font-weight: bold;
            width: 80px;
        }

        .col-coeff {
            text-align: center;
            width: 60px;
            color: #666;
        }

        /* Résultat Final */
        .final-box {
            text-align: right;
            margin-top: 20px;
        }

        .moyenne-generale {
            font-size: 18px;
            font-weight: bold;
        }

        .decision {
            margin-top: 5px;
            font-size: 14px;
            text-transform: uppercase;
        }

        .admis {
            color: green;
        }

        .ajourne {
            color: red;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
            margin-right: 50px;
            font-style: italic;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <div class="logo-box">
                <h1>Université Projet 5</h1>
                <h2>Année Universitaire 2024-2025</h2>
            </div>
            <div class="info-box">
                <strong>RELEVÉ DE NOTES</strong><br>
                Période : <?= htmlspecialchars($periode['nom']) ?><br>
                Date : <?= date('d/m/Y') ?>
            </div>
        </div>

        <div class="student-box">
            <div class="student-name"><?= strtoupper($etudiant['nom']) ?> <?= ucfirst($etudiant['prenom']) ?></div>
            <div>Email : <?= $etudiant['email'] ?></div>
            <div>Filière : Informatique (Exemple)</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Matière</th>
                    <th class="col-coeff">Coeff.</th>
                    <th class="col-note">Moyenne / 20</th>
                    <th>Appréciation</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $totalPoints = 0;
                $totalCoeff = 0;
                ?>
                <?php foreach ($matieres as $m): ?>
                    <?php
                    $note = $mesNotes[$m['id']] ?? null;
                    if ($note !== null) {
                        $totalPoints += ($note * $m['coefficient']);
                        $totalCoeff += $m['coefficient'];
                    }
                    ?>
                    <tr>
                        <td>
                            <strong><?= htmlspecialchars($m['nom']) ?></strong><br>
                            <span style="font-size:10px; color:#888"><?= $m['code'] ?></span>
                        </td>
                        <td class="col-coeff"><?= $m['coefficient'] ?></td>
                        <td class="col-note">
                            <?php if ($note === null): ?>
                                -
                            <?php else: ?>
                                <?= number_format($note, 2) ?>
                            <?php endif; ?>
                        </td>
                        <td style="font-style:italic; color:#999; font-size:10px;">
                            <?= ($note !== null && $note >= 10) ? 'Validé' : '' ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="final-box">
            <?php
            $mg = 0;
            if ($totalCoeff > 0) $mg = $totalPoints / $totalCoeff;
            ?>
            <div class="moyenne-generale">
                Moyenne Générale : <?= number_format($mg, 2) ?> / 20
            </div>
            <div class="decision">
                Résultat :
                <?php if ($mg >= 10): ?>
                    <span class="admis">ADMIS</span>
                <?php else: ?>
                    <span class="ajourne">AJOURNÉ</span>
                <?php endif; ?>
            </div>
        </div>

        <div class="signature">
            <p>Le Directeur des Études,</p>
            <br><br>
            <p>Signature</p>
        </div>
    </div>

</body>

</html>