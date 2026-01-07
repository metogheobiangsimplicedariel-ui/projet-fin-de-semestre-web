<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-50 font-sans overflow-hidden">

    <?php require 'views/prof/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">

       <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200 flex-shrink-0">
            <h1 class="text-xl font-bold text-gray-800">
                Tableau de bord
            </h1>
            
            <form method="GET" class="flex items-center gap-3">
                <input type="hidden" name="page" value="prof_dashboard">
                
                <label class="text-sm text-gray-500 hidden md:block">Période :</label>
                
                <div class="relative">
                    <select name="periode" onchange="this.form.submit()" 
                            class="appearance-none bg-indigo-50 border border-indigo-100 text-indigo-700 font-bold py-2 pl-4 pr-10 rounded-lg cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                        <?php foreach($toutes_les_periodes as $p_opt): ?>
                            <option value="<?= $p_opt['id'] ?>" <?= ($p_opt['id'] === $periode['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($p_opt['nom']) ?> (<?= htmlspecialchars($p_opt['annee_universitaire']) ?>)
                                <?php if($p_opt['statut'] == 'ouverte') echo '🟢'; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-indigo-700">
                        <i class="fa-solid fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </form>
        </header>

        <?php 
            // Calcul du statut visuel
            $is_open = ($periode['statut'] === 'ouverte');
            
            // Calcul Jours restants
            $fin = new DateTime($periode['date_fin_saisie']);
            $now = new DateTime();
            $diff = $now->diff($fin);
            $jours_restants = (int)$diff->format('%r%a');
        ?>
        
        <div class="px-8 py-6">
            <?php if($is_open): ?>
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white shadow-lg flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold mb-1">La saisie est ouverte ! ✍️</h2>
                        <p class="text-indigo-100">Vous pouvez saisir et modifier les notes de vos étudiants.</p>
                    </div>
                    <div class="text-right bg-white/10 p-4 rounded-xl border border-white/20 backdrop-blur-sm">
                        <p class="text-xs uppercase font-bold text-indigo-200 mb-1">Fermeture le</p>
                        <p class="text-xl font-bold"><?= $fin->format('d M Y') ?></p>
                        <p class="text-sm font-bold text-yellow-300 mt-1">
                            <i class="fa-regular fa-clock"></i> J - <?= $jours_restants ?>
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-gray-800 rounded-2xl p-6 text-white shadow-lg flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold mb-1">Saisie fermée 🔒</h2>
                        <p class="text-gray-400">La période de notation est close. Mode consultation uniquement.</p>
                    </div>
                    <div class="text-right">
                        <span class="bg-red-500 px-3 py-1 rounded text-xs font-bold uppercase">Clôturé</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="px-8 pb-10">
            <h3 class="font-bold text-gray-700 mb-4 text-lg">Mes Enseignements</h3>

            <?php if(empty($mes_matieres)): ?>
                <div class="bg-white p-10 rounded-xl shadow-sm text-center border border-gray-200">
                    <i class="fa-solid fa-book-open text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500 font-medium">Aucune matière ne vous est assignée pour cette période.</p>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($mes_matieres as $m): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition duration-200 flex flex-col h-full group">
                            
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="bg-indigo-50 text-indigo-600 px-2 py-1 rounded text-xs font-bold uppercase border border-indigo-100">
                                        <?= htmlspecialchars($m['code_filiere']) ?>
                                    </span>
                                    <span class="text-gray-400 text-xs font-bold">
                                        <?= htmlspecialchars($m['groupe']) ?>
                                    </span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-800 group-hover:text-indigo-600 transition">
                                    <?= htmlspecialchars($m['nom']) ?>
                                </h4>
                                <p class="text-xs text-gray-500 font-mono mt-1"><?= htmlspecialchars($m['code']) ?></p>
                            </div>

                            <div class="p-6 bg-gray-50 flex-grow">
                                <p class="text-xs font-bold text-gray-400 uppercase mb-3">Structure des évaluations</p>
                                
                                <?php if(empty($m['colonnes'])): ?>
                                    <p class="text-sm text-gray-400 italic flex items-center gap-2">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        En attente de configuration par l'admin.
                                    </p>
                                <?php else: ?>
                                    <div class="flex flex-wrap gap-2">
                                        <?php foreach($m['colonnes'] as $col): ?>
                                            <div class="bg-white border border-gray-200 px-3 py-1.5 rounded-lg text-sm shadow-sm">
                                                <span class="font-bold text-gray-700"><?= htmlspecialchars($col['nom_colonne']) ?></span>
                                                <span class="text-xs text-gray-400 ml-1">/<?= floatval($col['note_max']) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-4">
                                <?php if($is_open && !empty($m['colonnes'])): ?>
                                    <a href="index.php?page=prof_grades&matiere=<?= $m['id'] ?>&periode=<?= $periode['id'] ?>" 
                                       class="block w-full text-center bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 rounded-lg transition shadow-md shadow-indigo-200">
                                        Saisir les notes <i class="fa-solid fa-arrow-right ml-2"></i>
                                    </a>
                                <?php elseif(empty($m['colonnes'])): ?>
                                    <button disabled class="block w-full text-center bg-gray-200 text-gray-400 font-bold py-2.5 rounded-lg cursor-not-allowed">
                                        Configuration requise
                                    </button>
                                <?php else: ?>
                                    <a href="index.php?page=prof_grades&matiere=<?= $m['id'] ?>&periode=<?= $periode['id'] ?>" 
                                       class="block w-full text-center bg-white border border-gray-300 text-gray-600 hover:bg-gray-50 font-bold py-2.5 rounded-lg transition">
                                        <i class="fa-regular fa-eye mr-2"></i> Consulter
                                    </a>
                                <?php endif; ?>
                            </div>

                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<?php require 'views/layout/footer.php'; ?>