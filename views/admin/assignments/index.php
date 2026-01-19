<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">
    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-2">
                <span class="text-slate-400 font-normal">Dashboard /</span>
                <h1 class="text-xl font-bold text-gray-800">Affectations & Groupes</h1>
            </div>

            <form method="GET" class="flex items-center gap-2">
                <input type="hidden" name="page" value="admin_assignments">

                <?php if (isset($_GET['matiere'])): ?>
                    <input type="hidden" name="matiere" value="<?= htmlspecialchars($_GET['matiere']) ?>">
                <?php endif; ?>

                <select name="periode" onchange="this.form.submit()"
                    class="bg-slate-100 border-none rounded-lg text-sm font-bold text-slate-700 py-2 px-4 focus:ring-0 cursor-pointer hover:bg-slate-200 transition">

                    <?php foreach ($all_periods as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($p['id'] == $periode['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['nom']) ?> (<?= $p['statut'] ?>)
                        </option>
                    <?php endforeach; ?>

                </select>
            </form>
        </header>

        <div class="flex-grow flex flex-col md:flex-row overflow-hidden">

            <div class="w-full md:w-80 bg-white border-r border-gray-200 flex flex-col h-full overflow-y-auto flex-shrink-0">
                <div class="p-4 border-b border-gray-100 bg-gray-50 sticky top-0">
                    <h3 class="text-xs font-bold text-gray-500 uppercase">Matières disponibles</h3>
                </div>

                <div class="divide-y divide-gray-100">
                    <?php foreach ($matieres as $m): ?>
                        <?php
                        $isActive = (isset($current_matiere) && $current_matiere['id'] == $m['id']);
                        $activeClass = $isActive ? 'bg-blue-50 border-l-4 border-blue-600' : 'hover:bg-gray-50 border-l-4 border-transparent';
                        ?>
                        <a href="index.php?page=admin_assignments&periode=<?= $periode['id'] ?>&matiere=<?= $m['id'] ?>"
                            class="block p-4 transition-all <?= $activeClass ?>">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($m['nom']) ?></p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        <span class="bg-gray-200 px-1.5 py-0.5 rounded text-[10px]"><?= htmlspecialchars($m['code_filiere'] ?? 'N/A') ?></span>
                                    </p>
                                </div>
                                <?php if ($isActive): ?>
                                    <i class="fa-solid fa-chevron-right text-xs text-blue-600 mt-1"></i>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex-grow bg-gray-50 p-6 lg:p-8 h-full overflow-y-auto">
                <?php if ($current_matiere): ?>

                    <div class="flex justify-between items-end mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($current_matiere['nom']) ?></h2>
                            <p class="text-gray-500 text-sm">Gestion des affectations pour <span class="font-mono font-bold"><?= htmlspecialchars($periode['code']) ?></span></p>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6"><?= $_SESSION['success'];
                                                                                                                unset($_SESSION['success']); ?></div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
                            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                                    <i class="fa-solid fa-chalkboard-user text-blue-500"></i> Professeurs
                                </h3>
                                <button onclick="document.getElementById('modalAddProf').classList.remove('hidden')" class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg font-bold transition shadow-sm">
                                    <i class="fa-solid fa-plus"></i> Ajouter
                                </button>
                            </div>

                            <div class="p-0">
                                <?php if (empty($profs_affectes)): ?>
                                    <div class="p-8 text-center text-gray-400">
                                        <p class="text-sm">Aucun professeur affecté.</p>
                                    </div>
                                <?php else: ?>
                                    <table class="w-full text-left text-sm">
                                        <tbody class="divide-y divide-gray-100">
                                            <?php foreach ($profs_affectes as $aff): ?>
                                                <tr class="group hover:bg-gray-50">
                                                    <td class="p-4">
                                                        <p class="font-bold text-gray-800"><?= htmlspecialchars($aff['nom'] . ' ' . $aff['prenom']) ?></p>
                                                        <p class="text-xs text-gray-400"><?= htmlspecialchars($aff['email']) ?></p>
                                                    </td>
                                                    <td class="p-4">
                                                        <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-xs font-bold border border-blue-100">
                                                            <?= htmlspecialchars($aff['groupe'] ?: 'Tous') ?>
                                                        </span>
                                                    </td>
                                                    <td class="p-4 text-right">
                                                        <form method="POST" action="index.php?page=admin_assignments_post" onsubmit="return confirm('Retirer ce professeur ?');">
                                                            <input type="hidden" name="action" value="remove_prof">
                                                            <input type="hidden" name="assignment_id" value="<?= $aff['id'] ?>">
                                                            <input type="hidden" name="periode_id" value="<?= $periode['id'] ?>">
                                                            <input type="hidden" name="matiere_id" value="<?= $current_matiere['id'] ?>">
                                                            <button type="submit" class="text-gray-300 hover:text-red-500 transition"><i class="fa-solid fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col">
                            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                                <h3 class="font-bold text-gray-700 flex items-center gap-2">
                                    <i class="fa-solid fa-graduation-cap text-green-600"></i> Étudiants inscrits
                                </h3>
                                <div class="flex gap-2">
                                    <span class="bg-gray-200 text-gray-600 text-xs font-bold px-2 py-1 rounded"><?= count($etudiants_inscrits) ?></span>
                                    <button onclick="document.getElementById('modalEnroll').classList.remove('hidden')" class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg font-bold transition shadow-sm">
                                        <i class="fa-solid fa-user-plus"></i> Inscrire
                                    </button>
                                </div>
                            </div>

                            <div class="p-0 max-h-[500px] overflow-y-auto">
                                <?php if (empty($etudiants_inscrits)): ?>
                                    <div class="p-8 text-center text-gray-400">
                                        <p class="text-sm">Aucun étudiant inscrit.</p>
                                    </div>
                                <?php else: ?>
                                    <table class="w-full text-left text-sm">
                                        <tbody class="divide-y divide-gray-100">
                                            <?php foreach ($etudiants_inscrits as $insc): ?>
                                                <tr class="group hover:bg-gray-50">
                                                    <td class="p-4 w-10">
                                                        <div class="h-8 w-8 rounded-full bg-slate-100 flex items-center justify-center text-xs font-bold text-slate-500">
                                                            <?= strtoupper(substr($insc['prenom'], 0, 1) . substr($insc['nom'], 0, 1)) ?>
                                                        </div>
                                                    </td>
                                                    <td class="p-4">
                                                        <p class="font-bold text-gray-800"><?= htmlspecialchars($insc['nom'] . ' ' . $insc['prenom']) ?></p>
                                                    </td>
                                                    <td class="p-4">
                                                        <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs border border-gray-200">
                                                            <?= htmlspecialchars($insc['groupe'] ?: 'Standard') ?>
                                                        </span>
                                                    </td>
                                                    <td class="p-4 text-right">
                                                        <form method="POST" action="index.php?page=admin_assignments_post" onsubmit="return confirm('Désinscrire ?');">
                                                            <input type="hidden" name="action" value="remove_student">
                                                            <input type="hidden" name="inscription_id" value="<?= $insc['id'] ?>">
                                                            <input type="hidden" name="periode_id" value="<?= $periode['id'] ?>">
                                                            <input type="hidden" name="matiere_id" value="<?= $current_matiere['id'] ?>">
                                                            <button type="submit" class="text-gray-300 hover:text-red-500 transition"><i class="fa-solid fa-xmark"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>

                <?php else: ?>
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <i class="fa-solid fa-arrow-left text-4xl mb-4 animate-bounce"></i>
                        <p class="text-lg font-medium">Sélectionnez une matière à gauche</p>
                        <p class="text-sm">pour gérer les professeurs et les groupes.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<div id="modalAddProf" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">Affecter un Professeur</h3>
            <button onclick="document.getElementById('modalAddProf').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form action="index.php?page=admin_assignments_post" method="POST">
            <input type="hidden" name="action" value="assign_prof">
            <input type="hidden" name="periode_id" value="<?= $periode['id'] ?>">
            <input type="hidden" name="matiere_id" value="<?= $current_matiere['id'] ?>">

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Professeur</label>
                <select name="prof_id" required class="w-full border border-gray-300 rounded p-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Choisir --</option>
                    <?php foreach ($all_profs as $prof): ?>
                        <option value="<?= $prof['id'] ?>">
                            <?= htmlspecialchars($prof['nom'] . ' ' . $prof['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Groupe (Optionnel)</label>
                <input type="text" name="groupe" placeholder="Ex: CM, TD A, TP 1..." class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                <p class="text-xs text-gray-400 mt-1">Laissez vide pour affecter à toute la promo.</p>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAddProf').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded text-sm font-bold">Annuler</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-blue-700 shadow">Valider l'affectation</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEnroll" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg h-[80vh] flex flex-col">
        <div class="flex justify-between items-center mb-4 flex-shrink-0">
            <h3 class="text-lg font-bold text-gray-800">Inscrire des Étudiants</h3>
            <button onclick="document.getElementById('modalEnroll').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form action="index.php?page=admin_assignments_post" method="POST" class="flex flex-col h-full overflow-hidden">
            <input type="hidden" name="action" value="enroll_students">
            <input type="hidden" name="periode_id" value="<?= $periode['id'] ?>">
            <input type="hidden" name="matiere_id" value="<?= $current_matiere['id'] ?>">

            <div class="mb-4 flex-shrink-0">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Groupe Cible</label>
                <input type="text" name="groupe" placeholder="Ex: TD A" class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
            </div>

            <div class="mb-2 flex-shrink-0">
                <input type="text" id="searchStudent" placeholder="Rechercher un étudiant..." class="w-full border border-gray-200 rounded p-2 text-sm bg-gray-50">
            </div>

            <div class="flex-grow overflow-y-auto border border-gray-200 rounded p-2 space-y-1 bg-gray-50">
                <?php foreach ($all_students as $stu): ?>
                    <label class="flex items-center gap-3 p-2 hover:bg-white rounded cursor-pointer transition student-item">
                        <input type="checkbox" name="etudiants[]" value="<?= $stu['id'] ?>" class="w-4 h-4 text-green-600 rounded border-gray-300 focus:ring-green-500">
                        <div>
                            <p class="font-bold text-sm text-gray-700 student-name"><?= htmlspecialchars($stu['nom'] . ' ' . $stu['prenom']) ?></p>
                            <p class="text-xs text-gray-400"><?= htmlspecialchars($stu['email']) ?></p>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4 flex-shrink-0">
                <button type="button" onclick="document.getElementById('modalEnroll').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded text-sm font-bold">Annuler</button>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-green-700 shadow">Inscrire la sélection</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Petit script pour filtrer la liste des étudiants dans le modal
    document.getElementById('searchStudent').addEventListener('keyup', function(e) {
        const term = e.target.value.toLowerCase();
        const items = document.querySelectorAll('.student-item');

        items.forEach(item => {
            const name = item.querySelector('.student-name').textContent.toLowerCase();
            if (name.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });
</script>

<?php require 'views/layout/footer.php'; ?>