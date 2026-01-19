<?php require 'views/layout/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

<style>
    /* Styles inchangés... */
    .note-input {
        width: 80px;
        text-align: center;
        font-weight: bold;
    }

    .note-input:focus {
        transform: scale(1.1);
        transition: 0.2s;
        z-index: 10;
    }

    .note-input.modified {
        background-color: #fff3cd !important;
        border-color: #ffc107;
    }

    .note-input.saved {
        background-color: #d4edda !important;
        border-color: #28a745;
    }

    .etudiant-cell {
        position: sticky;
        left: 0;
        background: white;
        z-index: 5;
        border-right: 2px solid #dee2e6;
    }

    .header-colonne {
        position: sticky;
        top: 0;
        z-index: 10;
        background: #f8f9fa;
    }

    .table-container {
        max-height: 75vh;
        overflow: auto;
        border: 1px solid #ddd;
        border-radius: 8px;
    }

    /* Nouveau style pour le mode lecture seule */
    .readonly-mode input {
        background-color: #f8f9fa;
        color: #6c757d;
        border: none;
        cursor: not-allowed;
    }
</style>

<?php if (!$is_readonly): ?>
    <div id="toast-notif" class="alert alert-success shadow-lg" style="position: fixed; top: 80px; right: 20px; z-index: 9999; display: none;">
        <i class="bi bi-check-circle-fill"></i> <span id="toast-msg">Message</span>
    </div>
<?php endif; ?>

<body class="flex flex-col min-h-screen bg-gray-50 font-sans">
    <main class="flex-grow">

        <div class="d-flex justify-content-between align-items-center mb-3 bg-white p-3 rounded shadow-sm border">
            <div class="d-flex align-items-center gap-3">
                <a href="index.php?page=prof_matieres" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Retour
                </a>
                <div>
                    <span class="h5 align-middle d-block mb-0">Saisie des notes</span>
                    <?php if ($is_readonly): ?>
                        <span class="badge bg-warning text-dark"><i class="bi bi-lock-fill"></i> Lecture Seule (Période fermée)</span>
                    <?php else: ?>
                        <span class="badge bg-success"><i class="bi bi-pencil-fill"></i> Mode Édition</span>
                    <?php endif; ?>
                </div>
            </div>

            <div>
                <?php if (!$is_readonly): ?>
                    <button id="btn-save-all" class="btn btn-success fw-bold">
                        <i class="bi bi-save"></i> Tout Sauvegarder
                    </button>
                    <div class="small text-muted text-end mt-1" id="save-status">Tout est à jour</div>
                <?php else: ?>
                    <button disabled class="btn btn-secondary opacity-50">
                        <i class="bi bi-lock"></i> Modifications impossibles
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-container shadow-sm bg-white <?= $is_readonly ? 'readonly-mode' : '' ?>">
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="etudiant-cell p-3">Étudiants (<?= count($etudiants) ?>)</th>
                        <?php foreach ($colonnes as $col): ?>
                            <th class="header-colonne text-center min-w-[120px]">
                                <div class="fw-bold"><?= htmlspecialchars($col['nom_colonne']) ?></div>
                                <span class="badge bg-info text-dark" style="font-size:0.7em">Coeff <?= $col['coefficient'] ?></span>
                                <div class="small text-muted">/<?= $col['note_max'] ?></div>
                            </th>
                        <?php endforeach; ?>
                        <th class="header-colonne text-center bg-light">Moyenne</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($etudiants as $etudiant): ?>
                        <tr data-etudiant="<?= $etudiant['id'] ?>">
                            <td class="etudiant-cell p-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-secondary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width:35px; height:35px;">
                                        <?= substr($etudiant['prenom'], 0, 1) . substr($etudiant['nom'], 0, 1) ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark"><?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']) ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($etudiant['email']) ?></div>
                                    </div>
                                </div>
                            </td>

                            <?php
                            $totalPoints = 0;
                            $totalCoeff = 0;
                            foreach ($colonnes as $col):
                                $valeur = $notesExistantes[$etudiant['id']][$col['id']]['valeur'] ?? '';
                                if ($valeur !== '' && is_numeric($valeur)) {
                                    $totalPoints += $valeur * $col['coefficient'];
                                    $totalCoeff += $col['coefficient'];
                                }
                            ?>
                                <td class="text-center bg-white">
                                    <input type="number"
                                        class="form-control note-input mx-auto"
                                        value="<?= $valeur ?>"
                                        data-original="<?= $valeur ?>"
                                        data-etudiant="<?= $etudiant['id'] ?>"
                                        data-colonne="<?= $col['id'] ?>"
                                        data-coeff="<?= $col['coefficient'] ?>"
                                        min="0" max="<?= $col['note_max'] ?>" step="0.25"
                                        placeholder="-"
                                        <?= $is_readonly ? 'disabled' : '' ?>>
                                </td>
                            <?php endforeach; ?>

                            <td class="text-center fw-bold fs-5 bg-light">
                                <span id="moyenne-<?= $etudiant['id'] ?>" class="<?= ($totalCoeff > 0 && ($totalPoints / $totalCoeff) < 10) ? 'text-danger' : 'text-success' ?>">
                                    <?php if ($totalCoeff > 0) echo number_format($totalPoints / $totalCoeff, 2);
                                    else echo '-'; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>
    </main>
</body>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<?php if (!$is_readonly): ?>
    <script>
        const API_URL = 'api/sauvegarder_note.php';
        let modifications = {};

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.note-input').forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value !== this.dataset.original) {
                        this.classList.add('modified');
                        this.classList.remove('saved');
                        modifications[`${this.dataset.etudiant}-${this.dataset.colonne}`] = this;
                        document.getElementById('save-status').textContent = "⚠️ Modifications non enregistrées";
                    } else {
                        this.classList.remove('modified');
                        delete modifications[`${this.dataset.etudiant}-${this.dataset.colonne}`];
                    }
                    recalculerMoyenne(this.dataset.etudiant);
                });

                input.addEventListener('blur', function() {
                    if (this.classList.contains('modified')) sauvegarderNote(this);
                });

                input.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        sauvegarderNote(this);
                        let inputs = Array.from(document.querySelectorAll('.note-input'));
                        let index = inputs.indexOf(this);
                        if (inputs[index + 1]) inputs[index + 1].focus();
                    }
                });
            });

            document.getElementById('btn-save-all').addEventListener('click', () => {
                Object.values(modifications).forEach(input => sauvegarderNote(input));
            });
        });

        // ... (Garder vos fonctions sauvegarderNote, recalculerMoyenne, afficherNotif ici) ...
        // Pour alléger la réponse, je ne remets pas tout le JS car il reste identique, 
        // l'important est qu'il soit dans le bloc if (!$is_readonly)

        function sauvegarderNote(input) {
            let val = input.value;
            // Validation basique
            if (val !== '' && (val < 0 || val > 20)) {
                alert("La note doit être entre 0 et 20");
                return;
            }
            let data = {
                etudiant_id: input.dataset.etudiant,
                colonne_id: input.dataset.colonne,
                matiere_id: <?= isset($_GET['matiere_id']) ? (int)$_GET['matiere_id'] : 0 ?>,
                valeur: val
            };
            fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(res => res.json())
                .then(response => {
                    if (response.success) {
                        input.classList.remove('modified');
                        input.classList.add('saved');
                        input.dataset.original = val;
                        delete modifications[`${input.dataset.etudiant}-${input.dataset.colonne}`];
                        afficherNotif("Note enregistrée !");
                        setTimeout(() => input.classList.remove('saved'), 2000);
                        if (Object.keys(modifications).length === 0) document.getElementById('save-status').textContent = "Tout est à jour";
                    } else {
                        alert("Erreur : " + response.message);
                    }
                });
        }

        function recalculerMoyenne(etudiantId) {
            let row = document.querySelector(`tr[data-etudiant="${etudiantId}"]`);
            let inputs = row.querySelectorAll('.note-input');
            let total = 0,
                coeffTotal = 0;
            inputs.forEach(inp => {
                let val = parseFloat(inp.value);
                let coeff = parseFloat(inp.dataset.coeff);
                if (!isNaN(val)) {
                    total += val * coeff;
                    coeffTotal += coeff;
                }
            });
            let span = document.getElementById(`moyenne-${etudiantId}`);
            if (coeffTotal > 0) {
                let moy = (total / coeffTotal).toFixed(2);
                span.textContent = moy;
                span.className = moy < 10 ? 'text-danger fw-bold' : 'text-success fw-bold';
            } else {
                span.textContent = "-";
            }
        }

        function afficherNotif(msg) {
            let toast = document.getElementById('toast-notif');
            document.getElementById('toast-msg').textContent = msg;
            toast.style.display = 'block';
            setTimeout(() => toast.style.display = 'none', 2000);
        }
    </script>
<?php endif; ?>

<?php require 'views/layout/footer.php'; ?>