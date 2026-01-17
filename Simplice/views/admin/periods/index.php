<?php $title = "Studify - Périodes & Semestres"; ?>
<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">

    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">

        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200 flex-shrink-0">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="text-slate-400 font-normal">Dashboard /</span> Périodes
            </h1>
            <div class="flex items-center gap-4">
                <button onclick="document.getElementById('modalAddPeriod').classList.remove('hidden')" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold shadow transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nouvelle Période
                </button>
            </div>
        </header>

        <div class="p-6 lg:p-10">

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6">
                    <?= $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                <?php
                $today = new DateTime();

                foreach ($periodes as $p):
                    // CORRECTION 1 : On utilise les bons noms de colonnes de votre BDD
                    $debut = new DateTime($p['date_debut_saisie']);
                    $fin   = new DateTime($p['date_fin_saisie']);

                    // CORRECTION 2 : On vérifie le statut 'ouverte' au lieu de actif == 1
                    // On s'assure aussi que la date d'aujourd'hui est bien dans l'intervalle
                    $isStatutOuvert = (isset($p['statut']) && $p['statut'] === 'ouverte');
                    $isOpen = ($today >= $debut && $today <= $fin) && $isStatutOuvert;

                    $isFuture = ($today < $debut);

                    $interval = $today->diff($fin);
                    $joursRestants = (int)$interval->format('%r%a');
                    $isUrgent = ($joursRestants >= 0 && $joursRestants < 7 && $isOpen);
                ?>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col hover:shadow-md transition duration-300">

                        <div class="flex justify-between items-start mb-4">
                            <?php if ($isOpen): ?>
                                <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border border-green-200 mt-1">
                                    Saisie Ouverte
                                </span>
                            <?php elseif ($isFuture): ?>
                                <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border border-blue-200 mt-1">
                                    À venir
                                </span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border border-gray-200 mt-1">
                                    Clôturée
                                </span>
                            <?php endif; ?>

                            <div class="flex justify-between items-start mb-4">

                                <div>
                                    <?php if ($p['statut'] === 'ouverte'): ?>
                                        <form method="POST" action="index.php?page=admin_periods" onsubmit="return confirm('Verrouiller cette période ? Les professeurs ne pourront plus saisir de notes.');">
                                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="period_id" value="<?= $p['id'] ?>">
                                            <input type="hidden" name="new_status" value="fermee">

                                            <button type="submit" class="h-8 w-8 rounded-full bg-green-50 text-green-500 hover:bg-red-100 hover:text-red-500 flex items-center justify-center transition shadow-sm mb-0" title="Période ouverte. Cliquez pour verrouiller.">
                                                <i class="fa-solid fa-lock-open"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button onclick="openUnlockModal(<?= $p['id'] ?>)" class="h-8 w-8 rounded-full bg-red-50 text-red-500 hover:bg-green-100 hover:text-green-500 flex items-center justify-center transition shadow-sm" title="Période verrouillée. Justification requise pour ouvrir.">
                                            <i class="fa-solid fa-lock"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>

                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-800 leading-tight mb-1">
                                <?= htmlspecialchars($p['nom']) ?>
                            </h3>
                            <p class="text-xs text-gray-400 font-bold uppercase">
                                <?= htmlspecialchars($p['code']) ?> • <?= htmlspecialchars($p['annee_universitaire']) ?>
                            </p>
                        </div>

                        <div class="bg-gray-50 rounded-lg p-4 mb-6 border border-gray-100">
                            <div class="flex justify-between text-xs mb-2">
                                <span class="text-gray-400 font-bold uppercase">Début Saisie</span>
                                <span class="text-gray-600 font-mono"><?= $debut->format('d/m/Y H:i') ?></span>
                            </div>
                            <div class="flex justify-between text-xs mb-4">
                                <span class="text-gray-400 font-bold uppercase">Fin Saisie</span>
                                <span class="<?= $isUrgent ? 'text-red-600 font-bold' : 'text-gray-600' ?> font-mono">
                                    <?= $fin->format('d/m/Y H:i') ?>
                                </span>
                            </div>

                            <div class="flex items-center justify-center gap-2 pt-3 border-t border-gray-200">
                                <?php if ($isOpen): ?>
                                    <i class="fa-regular fa-clock text-green-500"></i>
                                    <span class="text-sm font-bold text-green-600">Reste <?= $joursRestants ?> jours</span>
                                <?php elseif ($isFuture): ?>
                                    <i class="fa-regular fa-calendar text-blue-400"></i>
                                    <span class="text-sm font-bold text-blue-500">Commence bientôt</span>
                                <?php else: ?>
                                    <i class="fa-solid fa-check-circle text-gray-400"></i>
                                    <span class="text-sm font-bold text-gray-400">Terminée</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <a href="index.php?page=admin_config_cols&periode=<?= $p['id'] ?>"
                            class="mt-auto block w-full bg-slate-900 hover:bg-slate-800 text-white text-center py-3 rounded-lg text-sm font-bold transition shadow-sm flex items-center justify-center gap-2">
                            <i class="fa-solid fa-sliders"></i> Configurer les Notes
                        </a>
                    </div>

                <?php endforeach; ?>

            </div>
        </div>
    </main>
</div>

<div id="modalAddPeriod" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg transform transition-all">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Nouvelle Période</h2>
            <button onclick="document.getElementById('modalAddPeriod').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form method="POST" action="index.php?page=admin_periods" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="create_period">

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom complet</label>
                    <input type="text" name="nom" placeholder="Ex: Semestre 2" required class="w-full border border-gray-300 rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code</label>
                    <input type="text" name="code" placeholder="Ex: S2" required class="w-full border border-gray-300 rounded p-2 text-sm uppercase">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type de période</label>
                <select name="type" class="w-full border border-gray-300 rounded p-2 text-sm bg-white focus:ring-2 focus:ring-slate-900 outline-none">
                    <option value="semestre">Semestre</option>
                    <option value="trimestre">Trimestre</option>
                    <option value="session">Session</option>
                    <option value="rattrapage">Rattrapage</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Année Universitaire</label>
                <input type="text" name="annee_universitaire" value="2025-2026" required class="w-full border border-gray-300 rounded p-2 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Début Saisie</label>
                    <input type="datetime-local" name="date_debut_saisie" required class="w-full border border-gray-300 rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Fin Saisie</label>
                    <input type="datetime-local" name="date_fin_saisie" required class="w-full border border-gray-300 rounded p-2 text-sm">
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalAddPeriod').classList.add('hidden')" class="px-5 py-2 text-gray-600 font-bold hover:bg-gray-100 rounded-lg">Annuler</button>
                <button type="submit" class="bg-slate-900 text-white px-5 py-2 rounded-lg font-bold shadow-md hover:bg-slate-800">Créer</button>
            </div>
        </form>
    </div>
</div>

<div id="modalUnlock" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold text-gray-800 mb-2 flex items-center gap-2">
            <i class="fa-solid fa-shield-halved text-orange-500"></i> Déverrouillage Restreint
        </h3>
        <p class="text-sm text-gray-500 mb-4">
            Cette période est clôturée. En tant qu'administrateur, vous devez justifier sa réouverture pour l'historique.
        </p>

        <form method="POST" action="index.php?page=admin_periods">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="action" value="toggle_status">
            <input type="hidden" name="new_status" value="ouverte">
            <input type="hidden" name="period_id" id="unlock_period_id">

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Motif de la modification <span class="text-red-500">*</span></label>
                <textarea name="justification" required rows="3" placeholder="Ex: Correction d'une note en Algorithme suite à une erreur de saisie..." class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-2 focus:ring-orange-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalUnlock').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded text-sm font-bold">Annuler</button>
                <button type="submit" class="bg-orange-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-orange-700">Confirmer & Ouvrir</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUnlockModal(id) {
        document.getElementById('unlock_period_id').value = id;
        document.getElementById('modalUnlock').classList.remove('hidden');
    }
</script>

<?php require 'views/layout/footer.php'; ?>