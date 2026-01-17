<!-- views/professeur/dashboard.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Professeur</title>
    <link rel="stylesheet" href="assets/professeur/css/professeur.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container mt-4">
        <h1>Tableau de Bord</h1>
        <p>Bienvenue, <?= htmlspecialchars($_SESSION['utilisateur']->getNomComplet()) ?></p>
        
        <div class="row">
            <div class="col-md-8">
                <h3>Mes Matières - <?= htmlspecialchars($periodeActive['nom']) ?></h3>
                
                <?php if (empty($matieres)): ?>
                    <div class="alert alert-info">
                        Aucune matière assignée pour cette période.
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Matière</th>
                                    <th>Progression</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matieres as $matiere): ?>
                                    <?php $progression = $progressions[$matiere->getId()] ?? []; ?>
                                    <tr>
                                        <td><?= htmlspecialchars($matiere->getCode()) ?></td>
                                        <td><?= htmlspecialchars($matiere->getNom()) ?></td>
                                        <td>
                                            <div class="progress">
                                                <div class="progress-bar" 
                                                     role="progressbar" 
                                                     style="width: <?= $progression['pourcentage'] ?? 0 ?>%">
                                                    <?= $progression['pourcentage'] ?? 0 ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (($progression['valide_par_prof'] ?? false)): ?>
                                                <span class="badge bg-success">Validé</span>
                                            <?php elseif (($progression['pourcentage'] ?? 0) == 100): ?>
                                                <span class="badge bg-warning">À valider</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">En cours</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="index.php?action=saisie&matiere_id=<?= $matiere->getId() ?>&periode_id=<?= $periodeActive['id'] ?>" 
                                               class="btn btn-primary btn-sm">
                                                Saisir notes
                                            </a>
                                            <a href="index.php?action=resultats&matiere_id=<?= $matiere->getId() ?>" 
                                               class="btn btn-info btn-sm">
                                                Résultats
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        Période active
                    </div>
                    <div class="card-body">
                        <h5><?= htmlspecialchars($periodeActive['nom']) ?></h5>
                        <p>Du <?= date('d/m/Y', strtotime($periodeActive['date_debut_saisie'])) ?>
                           au <?= date('d/m/Y', strtotime($periodeActive['date_fin_saisie'])) ?></p>
                        
                        <?php if (strtotime($periodeActive['date_fin_saisie']) < time()): ?>
                            <div class="alert alert-danger">
                                <strong>Attention :</strong> La période de saisie est terminée.
                            </div>
                        <?php else: ?>
                            <?php $joursRestants = ceil((strtotime($periodeActive['date_fin_saisie']) - time()) / (60*60*24)); ?>
                            <div class="alert alert-info">
                                <strong><?= $joursRestants ?> jours</strong> restants pour la saisie
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header">
                        Actions rapides
                    </div>
                    <div class="card-body">
                        <a href="index.php?action=matieres" class="btn btn-outline-primary w-100 mb-2">
                            Voir toutes mes matières
                        </a>
                        <a href="index.php?action=import" class="btn btn-outline-success w-100 mb-2">
                            Importer des notes
                        </a>
                        <a href="index.php?action=historique" class="btn btn-outline-secondary w-100">
                            Historique des saisies
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>