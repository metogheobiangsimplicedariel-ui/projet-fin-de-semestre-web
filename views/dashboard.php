<?php
// 1. Sécurité
if (!isset($_SESSION['auth'])) {
    header('Location: index.php?page=login');
    exit();
}
$user = $_SESSION['auth'];

// 2. Initialisation des variables
// On récupère les variables du contrôleur avec des valeurs par défaut
$bulletin = isset($bulletin) ? $bulletin : [];
$moyenneGenerale = isset($moyenneGenerale) ? $moyenneGenerale : null;
$rang = isset($rang) ? $rang : '--';
$ectsValides = isset($ectsValides) ? $ectsValides : '--';

// IMPORTANT : On récupère l'état du blocage (Publication)
$affichageBloque = isset($affichageBloque) ? $affichageBloque : false;

// 3. Données Statiques
$documents = [
    ['nom' => 'Relevé de notes - Semestre 1', 'date' => '15 Jan 2026', 'type' => 'PDF'],
    ['nom' => 'Attestation de réussite', 'date' => '02 Sept 2025', 'type' => 'PDF'],
];

require 'views/layout/header.php';
?>

<div class="flex h-screen bg-gray-50 font-sans overflow-hidden">

    <aside class="w-64 bg-white border-r border-gray-200 hidden lg:flex flex-col z-20">
        <div class="h-24 flex items-center justify-center border-b border-gray-100">
            <div class="flex items-center gap-2 text-studify-blue">
                <i class="fa-solid fa-eye text-2xl"></i>
                <span class="text-xl font-extrabold tracking-wide uppercase">STUDIFY</span>
            </div>
        </div>

        <nav class="flex-grow p-4 space-y-2">
            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Scolarité</p>

            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-studify-blue text-white rounded-xl shadow-md transition">
                <i class="fa-solid fa-chart-line"></i>
                <span class="font-medium">Tableau de bord</span>
            </a>

            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-studify-blue rounded-xl transition">
                <i class="fa-regular fa-file-pdf"></i>
                <span class="font-medium">Mes Documents</span>
            </a>

            <div class="mt-auto pt-10">
                <a href="index.php?page=logout" class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="font-medium">Déconnexion</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">

        <header class="h-auto py-6 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10">
            <div class="lg:hidden text-studify-blue font-bold text-xl">STUDIFY</div>

            <div class="hidden md:block">
                <h1 class="text-2xl font-bold text-gray-800">Mon Espace Étudiant</h1>
                <p class="text-sm text-gray-500">Année Universitaire 2025/2026</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-700">
                        <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?>
                    </p>
                    <p class="text-xs text-gray-500 uppercase badge bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full inline-block">
                        <?= htmlspecialchars($user['role']) ?>
                    </p>
                </div>
                <div class="h-[4rem] w-[4rem] flex items-center justify-center shadow-sm">
                    <div class="h-full w-full rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xl border-4 border-white shadow-md">
                        <?= substr($user['prenom'], 0, 1) . substr($user['nom'], 0, 1) ?>
                    </div>
                </div>
            </div>
        </header>

        <div class="p-6 lg:p-10 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-sm text-gray-500 font-medium mb-1">Moyenne Générale</p>
                        <p class="text-3xl font-extrabold text-gray-800">
                            <?php if ($affichageBloque): ?>
                                <span class="text-gray-300 text-2xl"><i class="fa-solid fa-lock"></i></span>
                            <?php else: ?>
                                <?= $moyenneGenerale !== null ? $moyenneGenerale : '--' ?>
                                <span class="text-lg text-gray-400 font-normal">/20</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl z-10">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div class="absolute right-0 top-0 h-full w-2 bg-green-500"></div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden">
                    <div>
                        <p class="text-sm text-gray-500 font-medium mb-1">Classement Promo</p>
                        <p class="text-3xl font-extrabold text-studify-blue">
                            <?php if ($affichageBloque): ?>
                                <span class="text-gray-300 text-2xl">--</span>
                            <?php else: ?>
                                <?= $rang ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-blue-100 text-studify-blue flex items-center justify-center text-xl">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div class="absolute right-0 top-0 h-full w-2 bg-studify-blue"></div>
                </div>

                <div class="bg-gradient-to-br from-studify-blue to-blue-600 p-6 rounded-2xl shadow-md text-white flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Crédits ECTS validés</p>
                            <p class="text-2xl font-bold mt-1">
                                <?php if ($affichageBloque): ?>
                                    <span class="text-blue-300">--</span>
                                <?php else: ?>
                                    <?= $ectsValides ?>
                                <?php endif; ?>
                            </p>
                        </div>
                        <i class="fa-solid fa-certificate text-2xl text-blue-200"></i>
                    </div>
                    <div class="w-full bg-blue-800/50 h-1.5 rounded-full mt-4">
                        <div class="bg-white h-1.5 rounded-full" style="width: 50%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fa-solid fa-list-ol mr-2 text-studify-blue"></i> Bulletin de notes
                    </h3>
                    <?php if (!$affichageBloque): ?>
                        <a href="index.php?page=student_download_bulletin" target="_blank" class="text-sm text-studify-blue hover:underline font-medium">
                            <i class="fa-solid fa-download mr-1"></i> Télécharger PDF
                        </a>
                    <?php endif; ?>
                </div>

                <?php if ($affichageBloque): ?>

                    <div class="p-12 text-center flex flex-col items-center justify-center bg-gray-50/50">
                        <div class="h-24 w-24 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner">
                            <i class="fa-solid fa-lock"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-700 mb-2">Résultats non disponibles</h4>
                        <p class="text-gray-500 max-w-md mx-auto mb-6">
                            La délibération est en cours pour cette période. Les résultats n'ont pas encore été publiés officiellement par l'administration.
                        </p>
                        <span class="px-5 py-2.5 bg-white border border-gray-200 text-gray-600 rounded-lg text-sm font-bold shadow-sm">
                            Statut : En attente de publication
                        </span>
                    </div>

                <?php else: ?>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-200">
                                    <th class="p-5 font-semibold w-1/3">Matière</th>
                                    <th class="p-5 font-semibold">Détail des notes</th>
                                    <th class="p-5 font-semibold text-center w-32">Moyenne</th>
                                    <th class="p-5 font-semibold text-center w-32">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm">
                                <?php if (empty($bulletin)): ?>
                                    <tr>
                                        <td colspan="4" class="p-8 text-center text-gray-500 italic">
                                            Aucune note disponible pour le moment.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bulletin as $ligne): ?>
                                        <?php
                                        $m = $ligne['matiere'];
                                        $notes = $ligne['details'];
                                        $moy = $ligne['moyenne'];
                                        ?>
                                        <tr class="hover:bg-gray-50 transition group">
                                            <td class="p-5">
                                                <div class="font-bold text-gray-800 text-base"><?= htmlspecialchars($m['nom']) ?></div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Code: <span class="font-mono"><?= htmlspecialchars($m['code']) ?></span> •
                                                    Coeff: <?= $m['coefficient'] ?>
                                                </div>
                                            </td>

                                            <td class="p-5">
                                                <?php if (empty($notes)): ?>
                                                    <span class="text-gray-300 italic text-xs">En attente...</span>
                                                <?php else: ?>
                                                    <div class="flex flex-wrap gap-2">
                                                        <?php foreach ($notes as $n): ?>
                                                            <?php if ($n['valeur'] !== null): ?>
                                                                <div class="flex flex-col items-center bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-lg shadow-sm">
                                                                    <span class="font-mono font-bold text-gray-800 text-sm"><?= $n['valeur'] ?></span>
                                                                    <span class="text-[10px] text-gray-400 uppercase tracking-wide">
                                                                        <?= htmlspecialchars($n['code_colonne'] ?? 'Note') ?>
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </td>

                                            <td class="p-5 text-center">
                                                <?php if ($moy !== null): ?>
                                                    <span class="font-bold text-lg <?= $moy >= 10 ? 'text-green-600' : 'text-red-600' ?>">
                                                        <?= $moy ?>
                                                    </span>
                                                    <span class="text-xs text-gray-400 block">/20</span>
                                                <?php else: ?>
                                                    <span class="text-gray-300">-</span>
                                                <?php endif; ?>
                                            </td>

                                            <td class="p-5 text-center">
                                                <?php if ($moy !== null): ?>
                                                    <?php if ($moy >= 10): ?>
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                            <i class="fa-solid fa-check mr-1.5"></i> Validé
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                                            <i class="fa-solid fa-triangle-exclamation mr-1.5"></i> Rattrapage
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-gray-400 text-xs italic">En cours</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Documents Officiels</h3>
                    <div class="space-y-3">
                        <?php foreach ($documents as $doc): ?>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition group cursor-pointer border border-transparent hover:border-blue-100">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 bg-red-100 text-red-500 rounded-lg flex items-center justify-center text-xl">
                                        <i class="fa-solid fa-file-pdf"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-700 group-hover:text-studify-blue transition"><?= $doc['nom'] ?></p>
                                        <p class="text-xs text-gray-400"><?= $doc['date'] ?></p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-download text-gray-400 group-hover:text-studify-blue"></i>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Évolution des résultats</h3>
                    <div class="flex-grow flex items-end justify-between px-4 gap-4 h-40">
                        <div class="w-full flex flex-col items-center gap-2 group opacity-50">
                            <div class="w-full bg-blue-100 rounded-t-lg h-[60%]"></div>
                            <div class="text-xs text-gray-500 font-bold">Sem 1</div>
                        </div>
                        <div class="w-full flex flex-col items-center gap-2 group">
                            <?php if ($affichageBloque): ?>
                                <div class="text-xs font-bold text-gray-400 mb-1"><i class="fa-solid fa-lock"></i></div>
                                <div class="w-full bg-gray-200 rounded-t-lg h-[75%]"></div>
                            <?php else: ?>
                                <div class="text-xs font-bold text-studify-blue opacity-100 mb-1"><?= $moyenneGenerale ?? '?' ?></div>
                                <div class="w-full bg-studify-blue rounded-t-lg h-[75%] shadow-lg shadow-blue-200"></div>
                            <?php endif; ?>
                            <div class="text-xs text-studify-blue font-bold">Sem 2</div>
                        </div>
                        <div class="w-full flex flex-col items-center gap-2 opacity-30">
                            <div class="w-full bg-gray-100 rounded-t-lg h-[10%] border border-dashed border-gray-300"></div>
                            <div class="text-xs text-gray-400">Sem 3</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>
</div>

<?php require 'views/layout/footer.php'; ?>