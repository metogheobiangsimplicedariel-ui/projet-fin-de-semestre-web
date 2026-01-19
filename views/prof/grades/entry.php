<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-50 font-sans overflow-hidden">
    <?php require 'views/prof/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-4 flex justify-between items-center sticky top-0 z-20">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Saisie des Notes</h1>
                <p class="text-sm text-gray-500">
                    <?= htmlspecialchars($matiere['nom']) ?> • <?= htmlspecialchars($periode['nom']) ?>
                </p>
            </div>
            <div>
                <button type="submit" form="gradeForm" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-bold shadow transition flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Enregistrer
                </button>
            </div>
        </header>

        <div class="p-8">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form id="gradeForm" method="POST" action="index.php?page=prof_save_grades" class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="matiere_id" value="<?= $matiere_id ?>">
                <input type="hidden" name="periode_id" value="<?= $periode_id ?>">

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50 border-b border-gray-200">
                            <tr>
                                <th class="p-4 text-xs font-bold text-gray-500 uppercase tracking-wider w-64 sticky left-0 bg-slate-50 z-10 border-r">
                                    Étudiant
                                </th>
                                <?php foreach ($colonnes as $col): ?>
                                    <th class="p-4 text-center min-w-[100px]">
                                        <div class="text-xs font-bold text-gray-700 uppercase"><?= htmlspecialchars($col['nom_colonne']) ?></div>
                                        <div class="text-[10px] text-gray-400 font-mono mt-1">
                                            /<?= floatval($col['note_max']) ?> (Coeff <?= floatval($col['coefficient']) ?>)
                                        </div>
                                    </th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($etudiants as $etudiant): ?>
                                <tr class="hover:bg-blue-50 transition">
                                    <td class="p-4 font-medium text-gray-800 sticky left-0 bg-white border-r">
                                        <?= htmlspecialchars($etudiant['nom'] . ' ' . $etudiant['prenom']) ?>
                                    </td>

                                    <?php foreach ($colonnes as $col): ?>
                                        <?php
                                        // Récupération de la valeur existante
                                        $valeur = $etudiant['notes'][$col['id']] ?? '';
                                        ?>
                                        <td class="p-2 text-center">
                                            <input type="number"
                                                step="0.01"
                                                min="0"
                                                max="<?= $col['note_max'] ?>"
                                                name="notes[<?= $etudiant['id'] ?>][<?= $col['id'] ?>]"
                                                value="<?= htmlspecialchars($valeur) ?>"
                                                class="w-20 p-2 text-center border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none font-mono text-sm transition"
                                                placeholder="-">
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </main>
</div>

<?php require 'views/layout/footer.php'; ?>