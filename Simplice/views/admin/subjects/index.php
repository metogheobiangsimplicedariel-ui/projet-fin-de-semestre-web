<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">

    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">

        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200 flex-shrink-0">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="text-slate-400 font-normal">Dashboard /</span> Catalogue Matières
            </h1>
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-700">Administrateur</p>
                    <p class="text-xs text-green-600 font-bold">● En ligne</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-slate-900 text-white flex items-center justify-center shadow-md border-2 border-red-500">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            </div>
        </header>

        <div class="p-6 lg:p-10 space-y-6">
            
            <?php if(isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <?php if(isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">Liste des Cours</h2>
                    <p class="text-sm text-gray-500">Définition des matières par filière.</p>
                </div>
                <button onclick="document.getElementById('modalSubject').classList.remove('hidden')" 
                        class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg font-bold shadow transition flex items-center gap-2">
                    <i class="fa-solid fa-plus"></i> Nouvelle Matière
                </button>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase sticky top-0">
                        <tr>
                            <th class="p-4">Code</th>
                            <th class="p-4">Intitulé</th>
                            <th class="p-4">Filière</th>
                            <th class="p-4 text-center">Coeff.</th>
                            <th class="p-4 text-center">Crédits</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        <?php if(empty($matieres)): ?>
                            <tr>
                                <td colspan="6" class="p-8 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <i class="fa-solid fa-book-open text-3xl mb-2 opacity-20"></i>
                                        <p>Aucune matière dans le catalogue.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($matieres as $m): ?>
                            <tr class="hover:bg-gray-50 transition group">
                                <td class="p-4">
                                    <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs border border-blue-100">
                                        <?= htmlspecialchars($m['code']) ?>
                                    </span>
                                </td>
                                <td class="p-4 font-bold text-gray-800">
                                    <?= htmlspecialchars($m['nom']) ?>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-700 text-xs"><?= htmlspecialchars($m['nom_filiere']) ?></span>
                                        <span class="text-[10px] text-gray-400"><?= htmlspecialchars($m['code_filiere']) ?></span>
                                    </div>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold">
                                        x <?= htmlspecialchars($m['coefficient']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <span class="bg-yellow-50 text-yellow-700 border border-yellow-200 px-2 py-1 rounded text-xs font-bold">
                                        <?= htmlspecialchars($m['credits']) ?> ECTS
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button class="text-gray-400 hover:text-blue-600 px-2"><i class="fa-solid fa-pen"></i></button>
                                        <button class="text-gray-400 hover:text-red-600 px-2"><i class="fa-solid fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>
</div>

<div id="modalSubject" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform transition-all scale-100">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Ajouter au catalogue</h2>
            <button onclick="document.getElementById('modalSubject').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="create_subject">
            
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Filière de rattachement <span class="text-red-500">*</span></label>
                <select name="filiere_id" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500">
                    <?php if(empty($filieres)): ?>
                        <option value="" disabled selected>Aucune filière trouvée (Créez-en une d'abord)</option>
                    <?php else: ?>
                        <?php foreach($filieres as $f): ?>
                            <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nom']) ?> (<?= htmlspecialchars($f['niveau']) ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-1">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code <span class="text-red-500">*</span></label>
                    <input type="text" name="code" placeholder="ALGO1" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 uppercase">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Intitulé <span class="text-red-500">*</span></label>
                    <input type="text" name="nom" placeholder="Algorithmique Avancée" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Coeff.</label>
                    <input type="number" name="coefficient" value="1.0" step="0.1" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-center">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Crédits</label>
                    <input type="number" name="credits" value="3" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-center">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Seuil Val.</label>
                    <input type="number" name="seuil_validation" value="10" step="0.5" required class="w-full border border-gray-200 rounded-lg p-2.5 outline-none focus:ring-2 focus:ring-blue-500 text-center">
                </div>
            </div>
            
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('modalSubject').classList.add('hidden')" class="px-5 py-2.5 text-gray-600 font-bold hover:bg-gray-100 rounded-lg transition">Annuler</button>
                <button type="submit" class="bg-studify-blue text-white px-5 py-2.5 rounded-lg font-bold shadow-md hover:bg-blue-700 transition">Ajouter au catalogue</button>
            </div>
        </form>
    </div>
</div>

<?php require 'views/layout/footer.php'; ?>