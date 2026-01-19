<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">
    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-2">
                <a href="index.php?page=admin_config_cols&periode=<?= $periode_id ?>&matiere=<?= $matiere_id ?>" class="text-slate-400 hover:text-slate-600 transition">
                    <i class="fa-solid fa-arrow-left"></i> Retour aux colonnes
                </a>
                <span class="text-gray-300">|</span>
                <h1 class="text-xl font-bold text-gray-800">Éditeur de Formule</h1>
            </div>
            <?php /* (Inclure votre bloc admin header habituel ici) */ ?>
        </header>

        <div class="p-8">

            <div class="bg-slate-800 text-white p-6 rounded-xl shadow-lg mb-8 flex justify-between items-center">
                <div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider mb-1">Configuration pour</p>
                    <h2 class="text-2xl font-bold"><?= htmlspecialchars($matiere['nom']) ?></h2>
                    <span class="bg-slate-700 px-2 py-1 rounded text-xs mt-2 inline-block"><?= htmlspecialchars($periode['nom']) ?></span>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-mono font-bold text-blue-400">
                        ƒ(x)
                    </div>
                </div>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6"><?= $_SESSION['success'];
                                                                                                        unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">1. Variables (Colonnes)</h3>
                        <p class="text-xs text-gray-500 mb-4">Cliquez pour insérer dans la formule.</p>

                        <?php if (empty($columns)): ?>
                            <div class="text-center py-4 text-red-500 text-sm">
                                <i class="fa-solid fa-triangle-exclamation"></i> Aucune colonne définie.<br>
                                <a href="index.php?page=admin_config_cols&periode=<?= $periode_id ?>&matiere=<?= $matiere_id ?>" class="underline">Allez en créer d'abord.</a>
                            </div>
                        <?php else: ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($columns as $col): ?>
                                    <button type="button"
                                        onclick="insertVariable('<?= $col['code_colonne'] ?>')"
                                        class="bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 px-3 py-2 rounded font-mono font-bold text-sm transition shadow-sm">
                                        <?= $col['code_colonne'] ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                        <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">2. Fonctions & Opérateurs</h3>
                        <div class="grid grid-cols-4 gap-2 mb-4">
                            <button onclick="insertText('+')" class="bg-gray-100 hover:bg-gray-200 p-2 rounded font-bold text-gray-700">+</button>
                            <button onclick="insertText('-')" class="bg-gray-100 hover:bg-gray-200 p-2 rounded font-bold text-gray-700">-</button>
                            <button onclick="insertText('*')" class="bg-gray-100 hover:bg-gray-200 p-2 rounded font-bold text-gray-700">*</button>
                            <button onclick="insertText('/')" class="bg-gray-100 hover:bg-gray-200 p-2 rounded font-bold text-gray-700">/</button>
                            <button onclick="insertText('(')" class="bg-gray-100 hover:bg-gray-200 p-2 rounded font-bold text-gray-700">(</button>
                            <button onclick="insertText(')')" class="bg-gray-100 hover:bg-gray-200 p-2 rounded font-bold text-gray-700">)</button>
                        </div>
                        <div class="space-y-2">
                            <button onclick="insertText('MAX( , )')" class="block w-full text-left px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded"><strong>MAX(a, b)</strong> : La plus grande valeur</button>
                            <button onclick="insertText('MOYENNE( , )')" class="block w-full text-left px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 rounded"><strong>MOYENNE(a, b)</strong> : Moyenne simple</button>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <form method="POST" action="index.php?page=admin_formula">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="matiere_id" value="<?= $matiere_id ?>">
                        <input type="hidden" name="periode_id" value="<?= $periode_id ?>">

                        <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-200 h-full flex flex-col">
                            <h3 class="font-bold text-gray-800 mb-4 flex justify-between items-center">
                                <span>3. Éditeur de Formule</span>

                                <select onchange="applyTemplate(this)" class="text-xs border border-gray-300 rounded p-1 font-normal text-gray-500 w-64">
                                    <option value="">-- Choisir un modèle --</option>
                                    <?php foreach ($templates as $tpl): ?>
                                        <option value="<?= htmlspecialchars($tpl['formule']) ?>">
                                            <?= htmlspecialchars($tpl['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </h3>

                            <div class="flex-grow">
                                <textarea name="formule" id="formulaInput" required
                                    class="w-full h-40 bg-slate-900 text-green-400 font-mono text-lg p-4 rounded-lg focus:ring-4 focus:ring-blue-500 outline-none shadow-inner"
                                    placeholder="Ex: (DS1 + DS2 + Examen * 2) / 4"><?= $currentFormula['formule'] ?? '' ?></textarea>

                                <p class="text-xs text-gray-500 mt-2 italic">
                                    <i class="fa-solid fa-circle-info"></i> Utilisez les codes exacts des colonnes (ex: DS1). Les espaces ne sont pas gênants.
                                </p>
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Description (Optionnel)</label>
                                <input type="text" name="description" value="<?= htmlspecialchars($currentFormula['description'] ?? '') ?>"
                                    placeholder="Ex: Moyenne pondérée classique"
                                    class="w-full border border-gray-300 rounded p-2 text-sm">
                            </div>

                            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-4">
                                <a href="index.php?page=admin_config_cols&periode=<?= $periode_id ?>&matiere=<?= $matiere_id ?>" class="px-6 py-3 text-gray-500 font-bold hover:bg-gray-100 rounded-lg transition">Annuler</a>
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg font-bold shadow-lg transition flex items-center gap-2">
                                    <i class="fa-solid fa-calculator"></i> Enregistrer la Formule
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>
</div>

<script>
    const input = document.getElementById('formulaInput');

    function insertVariable(code) {
        insertText(code);
    }

    function insertText(text) {
        // Insertion au curseur
        const startPos = input.selectionStart;
        const endPos = input.selectionEnd;

        input.value = input.value.substring(0, startPos) +
            text +
            input.value.substring(endPos, input.value.length);

        input.focus();
        input.selectionStart = startPos + text.length;
        input.selectionEnd = startPos + text.length;
    }

    function applyTemplate(select) {
        if (select.value) {
            if (confirm('Remplacer la formule actuelle par ce modèle ?')) {
                input.value = select.value;
            }
            select.value = ""; // Reset
        }
    }
</script>

<?php require 'views/layout/footer.php'; ?>