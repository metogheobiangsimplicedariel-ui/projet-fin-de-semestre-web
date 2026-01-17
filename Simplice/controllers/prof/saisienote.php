<!-- views/professeur/saisie_notes.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saisie des Notes - <?= htmlspecialchars($matiere->getNom()) ?></title>
    <link rel="stylesheet" href="assets/professeur/css/professeur.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Saisie des Notes</h1>
            <div>
                <a href="index.php?action=import&matiere_id=<?= $matiereId ?>&periode_id=<?= $periodeId ?>" 
                   class="btn btn-success">
                    <i class="bi bi-upload"></i> Importer Excel
                </a>
                <a href="index.php?action=resultats&matiere_id=<?= $matiereId ?>&periode_id=<?= $periodeId ?>" 
                   class="btn btn-info">
                    <i class="bi bi-bar-chart"></i> Voir résultats
                </a>
                <a href="index.php?action=valider&matiere_id=<?= $matiereId ?>&periode_id=<?= $periodeId ?>" 
                   class="btn btn-warning">
                    <i class="bi bi-check-circle"></i> Valider la saisie
                </a>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($matiere->getNom()) ?></h5>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Code :</strong> <?= htmlspecialchars($matiere->getCode()) ?></p>
                        <p><strong>Période :</strong> <?= htmlspecialchars($periode['nom']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Colonnes configurées :</strong> <?= count($colonnes) ?></p>
                        <p><strong>Étudiants inscrits :</strong> <?= count($etudiants) ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if (empty($etudiants)): ?>
            <div class="alert alert-warning">
                Aucun étudiant inscrit à cette matière pour cette période.
            </div>
        <?php else: ?>
            <div class="table-responsive" id="tableau-saisie">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th style="position: sticky; left: 0; background: #343a40; z-index: 10;">
                                Étudiant
                            </th>
                            <?php foreach ($colonnes as $colonne): ?>
                                <th class="text-center" data-colonne-id="<?= $colonne->getId() ?>">
                                    <?= htmlspecialchars($colonne->getNomColonne()) ?>
                                    <br>
                                    <small class="text-muted">
                                        Coef: <?= $colonne->getCoefficient() ?> 
                                        | Max: <?= $colonne->getNoteMax() ?>
                                    </small>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($etudiants as $etudiant): ?>
                            <tr data-etudiant-id="<?= $etudiant->getId() ?>">
                                <td style="position: sticky; left: 0; background: white; z-index: 5;">
                                    <strong><?= htmlspecialchars($etudiant->getNomComplet()) ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($etudiant->getGroupe()) ?>
                                        <?php if ($etudiant->isDispense()): ?>
                                            <span class="badge bg-info">Dispensé</span>
                                        <?php endif; ?>
                                    </small>
                                </td>
                                
                                <?php 
                                $notesEtudiant = $notesExistantes[$etudiant->getId()] ?? [];
                                $notesParColonne = [];
                                foreach ($notesEtudiant as $note) {
                                    $notesParColonne[$note['colonne_id']] = $note;
                                }
                                ?>
                                
                                <?php foreach ($colonnes as $colonne): ?>
                                    <?php 
                                    $note = $notesParColonne[$colonne->getId()] ?? null;
                                    $valeur = $note['valeur'] ?? '';
                                    $statut = $note['statut'] ?? 'saisie';
                                    ?>
                                    
                                    <td class="cellule-note text-center">
                                        <div class="input-group input-group-sm">
                                            <input type="text" 
                                                   class="form-control note-input" 
                                                   value="<?= htmlspecialchars($valeur) ?>"
                                                   data-etudiant-id="<?= $etudiant->getId() ?>"
                                                   data-colonne-id="<?= $colonne->getId() ?>"
                                                   data-note-max="<?= $colonne->getNoteMax() ?>"
                                                   placeholder="0-<?= $colonne->getNoteMax() ?>"
                                                   style="text-align: center;"
                                                   <?= $etudiant->isDispense() ? 'disabled' : '' ?>>
                                            
                                            <button class="btn btn-outline-secondary dropdown-toggle" 
                                                    type="button" 
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item statut-note" 
                                                       href="#" 
                                                       data-statut="ABS">
                                                        ABS - Absent
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item statut-note" 
                                                       href="#" 
                                                       data-statut="DIS">
                                                        DIS - Dispensé
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item statut-note" 
                                                       href="#" 
                                                       data-statut="DEF">
                                                        DEF - Défaillant
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item effacer-note" 
                                                       href="#">
                                                        Effacer
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                        
                                        <?php if ($note && $note['statut'] !== 'saisie'): ?>
                                            <small class="text-muted d-block">
                                                <?= htmlspecialchars($note['statut']) ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Légende</h5>
                    <div class="row">
                        <div class="col-auto">
                            <span class="badge bg-primary me-2">Note</span> Valeur numérique
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-warning me-2">ABS</span> Absent
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-info me-2">DIS</span> Dispensé
                        </div>
                        <div class="col-auto">
                            <span class="badge bg-danger me-2">DEF</span> Défaillant
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Modal de sauvegarde -->
    <div class="modal fade" id="modal-sauvegarde" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sauvegarde</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="message-sauvegarde"></div>
                    <div class="progress d-none" id="progress-sauvegarde">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" 
                             style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/professeur/js/saisie_dynamique.js"></script>
</body>
</html>