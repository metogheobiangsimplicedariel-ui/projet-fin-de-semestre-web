<?php
require 'views/layout/header.php';

// Récupération de sécurité pour le statut de la période
if (!isset($periode_actuelle)) {
    $periode_actuelle = (new \Models\Period())->getById($periode_selected);
}
?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">
    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">
        <header class="h-auto py-4 bg-white shadow-sm flex flex-col md:flex-row items-center justify-between px-8 sticky top-0 z-20 border-b border-gray-200 flex-shrink-0 gap-4">
            <div class="flex items-center gap-2">
                <span class="text-slate-400 font-normal">Administration /</span>
                <h1 class="text-xl font-bold text-gray-800">Délibération & Résultats</h1>
            </div>

            <form method="GET" action="index.php" class="flex items-center gap-2">
                <input type="hidden" name="page" value="admin_results">

                <select name="matiere" class="bg-slate-100 border-none rounded-lg text-sm font-bold text-slate-700 py-2 px-4 cursor-pointer hover:bg-slate-200 focus:ring-2 focus:ring-blue-500">
                    <?php foreach ($listeMatieres as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= ($m['id'] == $matiere_selected) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($m['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="periode" class="bg-slate-100 border-none rounded-lg text-sm font-bold text-slate-700 py-2 px-4 cursor-pointer hover:bg-slate-200 focus:ring-2 focus:ring-blue-500">
                    <?php foreach ($listePeriodes as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($p['id'] == $periode_selected) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                    Filtrer
                </button>
            </form>
        </header>

        <div class="p-8">

            <div class="bg-white p-4 rounded-lg shadow mb-6 border-l-4 border-blue-500 flex flex-col md:flex-row justify-between items-center gap-4">
                <div>
                    <h3 class="font-bold text-gray-700">Calcul des Moyennes</h3>
                    <p class="text-sm text-gray-500">
                        Lancez le calcul pour mettre à jour les résultats avec les dernières notes saisies.
                    </p>
                </div>

                <div class="flex items-center gap-4">
                    <form action="index.php?page=admin_calculate" method="POST" class="flex items-center gap-4">
                        <input type="hidden" name="matiere_id" value="<?= $matiere_selected ?>">
                        <input type="hidden" name="periode_id" value="<?= $periode_selected ?>">

                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow transition flex items-center">
                            <i class="fa-solid fa-calculator mr-2"></i>
                            Calculer maintenant
                        </button>
                    </form>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="bg-green-100 text-green-700 px-4 py-2 rounded font-bold flex items-center shadow-sm">
                            <i class="fa-solid fa-check-circle mr-2"></i>
                            <?= $_SESSION['success'] ?>
                        </div>
                        <?php unset($_SESSION['success']); ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

                <div class="bg-white p-4 rounded-lg shadow border border-gray-200 flex justify-between items-center">
                    <div>
                        <div class="text-xs font-bold text-gray-500 uppercase">Accès Professeurs</div>
                        <?php if ($periode_actuelle['statut'] === 'ouverte'): ?>
                            <div class="text-green-600 font-bold flex items-center mt-1">
                                <i class="fa-solid fa-pen-to-square mr-2"></i> Saisie Ouverte
                            </div>
                        <?php else: ?>
                            <div class="text-red-600 font-bold flex items-center mt-1">
                                <i class="fa-solid fa-lock mr-2"></i> Saisie Fermée
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="index.php?page=admin_update_periode" method="POST">
                        <input type="hidden" name="periode_id" value="<?= $periode_selected ?>">
                        <input type="hidden" name="type_action" value="statut">

                        <?php if ($periode_actuelle['statut'] === 'ouverte'): ?>
                            <input type="hidden" name="valeur" value="fermee">
                            <button type="submit" class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1.5 rounded text-sm font-bold transition">
                                Verrouiller
                            </button>
                        <?php else: ?>
                            <input type="hidden" name="valeur" value="ouverte">
                            <button type="submit" class="bg-green-100 text-green-700 hover:bg-green-200 px-3 py-1.5 rounded text-sm font-bold transition">
                                Ouvrir
                            </button>
                        <?php endif; ?>
                    </form>
                </div>

                <div class="bg-white p-4 rounded-lg shadow border border-gray-200 flex justify-between items-center">
                    <div>
                        <div class="text-xs font-bold text-gray-500 uppercase">Accès Étudiants</div>
                        <?php if ($periode_actuelle['resultats_publies'] == 1): ?>
                            <div class="text-blue-600 font-bold flex items-center mt-1">
                                <i class="fa-solid fa-eye mr-2"></i> Résultats Visibles
                            </div>
                        <?php else: ?>
                            <div class="text-gray-500 font-bold flex items-center mt-1">
                                <i class="fa-solid fa-eye-slash mr-2"></i> Résultats Masqués
                            </div>
                        <?php endif; ?>
                    </div>

                    <form action="index.php?page=admin_update_periode" method="POST">
                        <input type="hidden" name="periode_id" value="<?= $periode_selected ?>">
                        <input type="hidden" name="type_action" value="publication">

                        <?php if ($periode_actuelle['resultats_publies'] == 1): ?>
                            <input type="hidden" name="valeur" value="0">
                            <button type="submit" class="bg-gray-100 text-gray-600 hover:bg-gray-200 px-3 py-1.5 rounded text-sm font-bold transition">
                                Masquer
                            </button>
                        <?php else: ?>
                            <input type="hidden" name="valeur" value="1">
                            <button type="submit" onclick="return confirm('Confirmer la publication officielle ?')"
                                class="bg-blue-600 text-white hover:bg-blue-700 px-3 py-1.5 rounded text-sm font-bold transition shadow-sm">
                                Publier
                            </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <span class="font-bold text-gray-800"><?= count($etudiants) ?></span> Étudiants
                            <span class="w-px h-4 bg-gray-300"></span>
                            <span class="font-bold text-gray-800"><?= count($listeMatieres) ?></span> Matières au total
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <a href="#" onclick="alert('Fonctionnalité PDF à venir')"
                            class="bg-red-600 text-white border border-red-700 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-700 flex items-center gap-2 transition shadow-sm">
                            <i class="fa-solid fa-file-pdf"></i> Imprimer le PV
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border-collapse">
                        <thead class="bg-gray-50 text-gray-700 font-semibold text-xs uppercase tracking-wider">
                            <tr>
                                <th class="py-3 px-4 text-left border-b w-1/4">Étudiant</th>

                                <?php if (!empty($colonnes)): ?>
                                    <?php foreach ($colonnes as $col): ?>
                                        <th class="py-3 px-4 text-center border-b">
                                            <div class="flex flex-col items-center">
                                                <span><?= htmlspecialchars($col['nom_colonne']) ?></span>
                                                <span class="text-[10px] bg-blue-100 text-blue-800 px-1.5 py-0.5 rounded mt-1 normal-case">
                                                    Coeff <?= $col['coefficient'] ?>
                                                </span>
                                            </div>
                                        </th>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <th class="py-3 px-4 text-center border-b italic text-gray-400">Aucune évaluation configurée</th>
                                <?php endif; ?>

                                <th class="py-3 px-4 text-center border-b bg-gray-100 border-l w-24">Moyenne</th>
                                <th class="py-3 px-4 text-center border-b w-16">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php if (empty($etudiants)): ?>
                                <tr>
                                    <td colspan="10" class="py-8 text-center text-gray-500 italic">
                                        Aucun étudiant inscrit à cette matière pour cette période.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($etudiants as $etu): ?>
                                    <tr class="hover:bg-gray-50 transition group">
                                        <td class="py-3 px-4 border-b">
                                            <div class="font-bold text-gray-900">
                                                <?= htmlspecialchars($etu['nom'] . ' ' . $etu['prenom']) ?>
                                            </div>
                                            <div class="text-xs text-gray-500"><?= htmlspecialchars($etu['email']) ?></div>
                                            <div class="text-xs text-gray-400"><?= htmlspecialchars($etu['numero_etudiant'] ?? '') ?></div>
                                        </td>

                                        <?php if (!empty($colonnes)): ?>
                                            <?php foreach ($colonnes as $col): ?>
                                                <td class="py-3 px-4 text-center border-b">
                                                    <?php
                                                    $valeur = isset($notes[$etu['id']][$col['id']]) ? $notes[$etu['id']][$col['id']] : null;
                                                    ?>
                                                    <?php if ($valeur !== null): ?>
                                                        <span class="font-mono font-bold text-gray-700"><?= $valeur ?></span>
                                                    <?php else: ?>
                                                        <span class="text-gray-300">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <td class="border-b"></td>
                                        <?php endif; ?>

                                        <td class="py-3 px-4 text-center border-b bg-gray-50 border-l font-bold text-base">
                                            <?php if ($etu['moyenne'] !== null): ?>
                                                <?php $color = $etu['moyenne'] >= 10 ? 'text-green-600' : 'text-red-600'; ?>
                                                <span class="<?= $color ?>"><?= $etu['moyenne'] ?></span>
                                                <span class="text-xs text-gray-400 font-normal">/20</span>
                                            <?php else: ?>
                                                <span class="text-gray-300 text-xs font-normal">N/A</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="py-3 px-4 text-center border-b">
                                            <a href="index.php?page=admin_print_bulletin&periode=<?= $periode_selected ?>&etudiant=<?= $etu['id'] ?>"
                                                target="_blank"
                                                class="text-gray-400 hover:text-blue-600 transition"
                                                title="Voir le bulletin PDF">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require 'views/layout/footer.php'; ?>