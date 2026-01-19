<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-50 font-sans overflow-hidden">
    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto p-8">

        <header class="mb-8 flex justify-between items-end">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Tableau de Bord</h1>
                <p class="text-gray-500 mt-1">
                    Vue d'ensemble • Période active :
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-bold">
                        <?= htmlspecialchars($periodeActive['nom'] ?? 'Aucune') ?>
                    </span>
                </p>
            </div>
            <div>
                <span class="text-sm text-gray-400">Dernière maj : <?= date('H:i') ?></span>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800"><?= $stats['etudiants'] ?></div>
                    <div class="text-sm text-gray-500">Étudiants Inscrits</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800"><?= $stats['profs'] ?></div>
                    <div class="text-sm text-gray-500">Professeurs Actifs</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center text-xl">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-800"><?= $stats['matieres'] ?></div>
                    <div class="text-sm text-gray-500">Matières Enseignées</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-blue-500"></i> Avancement de la Saisie
                </h3>

                <div class="flex justify-center mb-6">
                    <div class="relative w-40 h-40 flex items-center justify-center rounded-full border-[12px] <?= ($remplissage == 100) ? 'border-green-100' : 'border-blue-50' ?>">
                        <div class="absolute inset-0 rounded-full border-[12px] border-blue-600" style="clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%); opacity: <?= $remplissage / 100 ?>;"></div>
                        <div class="text-center z-10">
                            <span class="text-4xl font-bold text-gray-800"><?= $remplissage ?>%</span>
                            <p class="text-xs text-gray-400 uppercase mt-1">Complété</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Notes saisies</span>
                        <span class="font-bold text-gray-800"><?= $filledSlots ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Total attendu (est.)</span>
                        <span class="font-bold text-gray-800"><?= $totalSlots ?></span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 mt-2">
                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: <?= $remplissage ?>%"></div>
                    </div>

                    <?php if ($remplissage < 100): ?>
                        <div class="bg-orange-50 border-l-4 border-orange-400 p-3 mt-4">
                            <p class="text-xs text-orange-700">
                                <strong>Attention :</strong> La saisie n'est pas terminée. Relancez les professeurs avant la fermeture de la période.
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="bg-green-50 border-l-4 border-green-400 p-3 mt-4">
                            <p class="text-xs text-green-700">
                                <strong>Bravo !</strong> Toutes les notes attendues semblent être saisies.
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clock-rotate-left text-gray-400"></i> Activité Récente
                </h3>

                <?php if (empty($logs)): ?>
                    <p class="text-sm text-gray-400 italic">Aucune activité récente.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($logs as $log): ?>
                            <div class="flex items-start gap-3 pb-3 border-b border-gray-50 last:border-0">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 flex-shrink-0 text-xs">
                                    <i class="fa-solid fa-pen"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-800">
                                        <span class="font-bold"><?= htmlspecialchars($log['prof_nom']) ?></span>
                                        a saisi une note de
                                        <span class="font-mono font-bold"><?= $log['valeur'] ?></span>
                                        en <?= htmlspecialchars($log['mat_code']) ?>
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Pour <?= htmlspecialchars($log['etu_nom']) ?> • <?= date('d/m à H:i', strtotime($log['date_modification'])) ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="mt-6 text-center">
                    <a href="index.php?page=admin_results" class="text-sm text-blue-600 hover:underline">Voir tous les résultats →</a>
                </div>
            </div>

        </div>
    </main>
</div>

<?php require 'views/layout/footer.php'; ?>