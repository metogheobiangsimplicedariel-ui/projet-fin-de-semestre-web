<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">

    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">

        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200 flex-shrink-0">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="text-slate-400 font-normal">Dashboard /</span> Périodes
            </h1>
            <div class="flex items-center gap-4">
                <button onclick="document.getElementById('modalAddPeriod').classList.remove('hidden')" 
                        class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg font-bold shadow transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nouvelle Période
                </button>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6 lg:p-10">
            
            <?php if(empty($periodes)): ?>
                <div class="col-span-full text-center py-20 text-gray-400">
                    <i class="fa-regular fa-calendar-xmark text-5xl mb-4"></i>
                    <p class="text-lg">Aucune période configurée.</p>
                </div>
            <?php else: ?>

                <?php foreach($periodes as $p): ?>
                    <?php 
                        // Mapping des couleurs
                        $badges = [
                            'a_venir' => 'bg-gray-100 text-gray-600 border-gray-200',
                            'ouverte' => 'bg-green-100 text-green-700 border-green-200 animate-pulse',
                            'fermee'  => 'bg-red-50 text-red-600 border-red-100',
                            'publiee' => 'bg-blue-50 text-blue-600 border-blue-100'
                        ];
                        $badgeClass = $badges[$p['statut']] ?? 'bg-gray-100';
                        
                        // Jolis noms
                        $labels = ['a_venir' => 'À venir', 'ouverte' => 'Saisie Ouverte', 'fermee' => 'Saisie Fermée', 'publiee' => 'Résultats Publiés'];
                        $statusLabel = $labels[$p['statut']] ?? $p['statut'];
                    ?>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative group hover:shadow-md transition flex flex-col justify-between h-full">
                        
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide border <?= $badgeClass ?>">
                                    <?= $statusLabel ?>
                                </span>
                                
                                <form method="POST" class="flex gap-1">
                                    <input type="hidden" name="action" value="update_status">
                                    <input type="hidden" name="periode_id" value="<?= $p['id'] ?>">
                                    
                                    <?php if($p['statut'] == 'a_venir'): ?>
                                        <button name="statut" value="ouverte" class="h-8 w-8 flex items-center justify-center bg-green-50 text-green-600 rounded-lg hover:bg-green-100 transition" title="Ouvrir la saisie">
                                            <i class="fa-solid fa-play"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if($p['statut'] == 'ouverte'): ?>
                                        <button name="statut" value="fermee" onclick="return confirm('Fermer la saisie ?')" class="h-8 w-8 flex items-center justify-center bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition" title="Fermer la saisie">
                                            <i class="fa-solid fa-lock"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if($p['statut'] == 'fermee'): ?>
                                        <button name="statut" value="ouverte" onclick="return confirm('Réouvrir la saisie pour les profs ?')" class="h-8 w-8 flex items-center justify-center bg-orange-50 text-orange-600 rounded-lg hover:bg-orange-100 transition" title="Réouvrir (Correction)">
                                            <i class="fa-solid fa-arrow-rotate-left"></i>
                                        </button>
                                        <button name="statut" value="publiee" onclick="return confirm('Publier les résultats aux étudiants ?')" class="h-8 w-8 flex items-center justify-center bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition" title="Publier aux étudiants">
                                            <i class="fa-solid fa-bullhorn"></i>
                                        </button>
                                    <?php endif; ?>
                                </form>
                            </div>

                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-800"><?= htmlspecialchars($p['nom']) ?></h3>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider"><?= htmlspecialchars($p['code']) ?> • <?= htmlspecialchars($p['annee_universitaire']) ?></p>
                            </div>

                            <div class="text-sm text-gray-600 space-y-2 bg-gray-50 p-3 rounded-lg border border-gray-100">
                                <div class="flex justify-between">
                                    <span class="text-gray-400 text-xs uppercase font-bold">Début Saisie</span>
                                    <span class="font-medium"><?= date('d/m/Y H:i', strtotime($p['date_debut_saisie'])) ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400 text-xs uppercase font-bold">Fin Saisie</span>
                                    <span class="font-medium text-red-500"><?= date('d/m/Y H:i', strtotime($p['date_fin_saisie'])) ?></span>
                                </div>
                            </div>

                            <?php if($p['statut'] === 'ouverte'): ?>
                                <div class="mt-3 text-center">
                                    <p class="text-xs font-bold text-green-600">
                                        <i class="fa-regular fa-clock"></i> Reste <?= $p['jours_restants'] ?> jours
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="mt-5 pt-4 border-t border-gray-100">
                            <a href="index.php?page=admin_config_cols&periode=<?= $p['id'] ?>" class="block w-full text-center bg-slate-800 hover:bg-slate-700 text-white py-2.5 rounded-lg text-sm font-bold shadow-lg shadow-slate-200 transition">
                                <i class="fa-solid fa-sliders mr-2"></i> Configurer les Notes
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>

            <?php endif; ?>
        </div>

    </main>
</div>

<div id="modalAddPeriod" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Nouvelle Période</h2>
            <button onclick="document.getElementById('modalAddPeriod').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create_period">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code (ex: S1-2024)</label>
                    <input type="text" name="code" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Année (ex: 2024-2025)</label>
                    <input type="text" name="annee_universitaire" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom (ex: Semestre 1)</label>
                <input type="text" name="nom" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                <select name="type" class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 bg-white">
                    <option value="semestre">Semestre</option>
                    <option value="trimestre">Trimestre</option>
                    <option value="rattrapage">Rattrapage</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Début Saisie Notes</label>
                    <input type="datetime-local" name="date_debut_saisie" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Fin Saisie Notes</label>
                    <input type="datetime-local" name="date_fin_saisie" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:border-blue-500 text-sm">
                </div>
            </div>
            
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalAddPeriod').classList.add('hidden')" class="px-5 py-2.5 text-gray-600 font-bold hover:bg-gray-100 rounded-lg transition">Annuler</button>
                <button type="submit" class="bg-slate-900 text-white px-5 py-2.5 rounded-lg font-bold shadow-md hover:bg-slate-700 transition">Créer</button>
            </div>
        </form>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>