<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>PV de Délibération</title>
    <style>
        /* CSS SPÉCIFIQUE POUR DOMPDF (Pas de Tailwind ici) */
        body {
            font-family: Helvetica, sans-serif;
            font-size: 10px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
        }

        h1 {
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }

        .sub-title {
            font-size: 12px;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #999;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #eee;
            font-weight: bold;
            font-size: 9px;
        }

        .col-nom {
            text-align: left;
            width: 140px;
            background-color: #f9f9f9;
        }

        .total-col {
            background-color: #e0e0e0;
            font-weight: bold;
        }

        .admis {
            color: green;
            font-weight: bold;
        }

        .ajourne {
            color: red;
            font-weight: bold;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 8px;
            color: #aaa;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>Procès Verbal de Délibération</h1>
        <div class="sub-title">
            Période : <strong><?= htmlspecialchars($periode['nom']) ?></strong><br>
            Édité le : <?= date('d/m/Y à H:i') ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-nom">ÉTUDIANTS</th>
                <?php foreach ($matieres as $m): ?>
                    <th>
                        <?= htmlspecialchars($m['code']) ?><br>
                        <span style="font-weight:normal">(Coeff <?= $m['coefficient'] ?>)</span>
                    </th>
                <?php endforeach; ?>
                <th class="total-col">MOYENNE</th>
                <th>DÉCISION</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($etudiants as $etud): ?>
                <?php
                $totalPoints = 0;
                $totalCoeff = 0;
                $nbNotes = 0;
                ?>
                <tr>
                    <td class="col-nom">
                        <strong><?= strtoupper($etud['nom']) ?></strong> <?= ucfirst($etud['prenom']) ?>
                    </td>

                    <?php foreach ($matieres as $m): ?>
                        <?php
                        // On récupère la note dans le tableau à 2 dimensions
                        $note = $moyennes[$etud['id']][$m['id']] ?? null;

                        if ($note !== null) {
                            $totalPoints += ($note * $m['coefficient']);
                            $totalCoeff += $m['coefficient'];
                            $nbNotes++;
                        }
                        ?>
                        <td>
                            <?php if ($note === null): ?>
                                -
                            <?php else: ?>
                                <?= number_format($note, 2) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>

                    <td class="total-col">
                        <?php
                        $mg = 0;
                        if ($totalCoeff > 0) {
                            $mg = $totalPoints / $totalCoeff;
                            echo number_format($mg, 2);
                        } else {
                            echo "-";
                        }
                        ?>
                    </td>

                    <td>
                        <?php
                        if ($totalCoeff > 0) {
                            // Seuil d'admission à 10/20
                            if ($mg >= 10) echo '<span class="admis">ADMIS</span>';
                            else echo '<span class="ajourne">AJOURNÉ</span>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer">
        Document généré automatiquement - Système de Gestion de Notes v1.0
    </div>

</body>

</html>