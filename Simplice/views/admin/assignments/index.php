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
                <select name="periode" onchange="this.form.submit()" class="bg-slate-100 border-none rounded-lg text-sm font-bold text-slate-700 py-2 px-4 focus:ring-0 cursor-pointer hover:bg-slate-200 transition">
                    <option value="<?= $periode['id'] ?>" selected>📅 <?= htmlspecialchars($periode['nom']) ?></option>
                </select>
            </form>
        </header>

        <div class="flex-grow flex flex-col md:flex-row overflow-hidden">
            
            <div class="w-full md:w-80 bg-white border-r border-gray-200 flex flex-col h-full overflow-y-auto">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-xs font-bold text-gray-500 uppercase">Matières (<?= $periode['code'] ?>)</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <?php foreach($matieres as $m): ?>
                        <?php 
                            $isActive = ($m['id'] === $current_matiere_id);
                            $bgClass = $isActive ? 'bg-purple-50 border-l-4 border-purple-600' : 'hover:bg-gray-50 border-l-4 border-transparent';
                        ?>
                        <a href="index.php?page=admin_assignments&periode=<?= $periode_id ?>&matiere=<?= $m['id'] ?>" class="block p-4 transition-all <?= $bgClass ?>">
                            <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($m['nom']) ?></p>
                            <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($m['code_filiere']) ?> • <?= htmlspecialchars($m['code']) ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex-grow bg-gray-50 p-6 lg:p-8 h-full overflow-y-auto space-y-8">
                
                <?php if($current_matiere): ?>
                    
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="bg-green-100 text-green-700 p-3 rounded-lg text-sm border border-green-200 shadow-sm">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fa-solid fa-chalkboard-user text-purple-600"></i> Professeurs
                            </h3>
                            <span class="text-xs font-bold bg-purple-100 text-purple-700 px-2 py-1 rounded">
                                <?= count($profs_affectes) ?> affecté(s)
                            </span>
                        </div>

                        <form method="POST" class="flex gap-2 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-100 items-end">
                            <input type="hidden" name="action" value="assign_prof">
                            <div class="flex-grow">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Professeur</label>
                                <select name="prof_id" class="w-full border-gray-200 rounded text-sm focus:border-purple-500">
                                    <?php foreach($all_profs as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom'] . ' ' . $p['prenom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="w-32">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Groupe</label>
                                <input type="text" name="groupe" placeholder="CM, TD1..." value="Tous" class="w-full border-gray-200 rounded text-sm focus:border-purple-500">
                            </div>
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded text-sm font-bold shadow-sm transition">
                                <i class="fa-solid fa-plus mr-1"></i> Ajouter
                            </button>
                        </form>

                        <table class="w-full text-left text-sm">
                            <thead class="text-gray-400 uppercase text-xs border-b border-gray-100">
                                <tr>
                                    <th class="pb-2">Nom</th>
                                    <th class="pb-2">Groupe assigné</th>
                                    <th class="pb-2 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <?php foreach($profs_affectes as $ap): ?>
                                    <tr>
                                        <td class="py-3 font-bold text-gray-700">
                                            <?= htmlspecialchars($ap['nom'] . ' ' . $ap['prenom']) ?>
                                            <span class="block text-xs text-gray-400 font-normal"><?= htmlspecialchars($ap['email']) ?></span>
                                        </td>
                                        <td class="py-3">
                                            <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs font-bold border"><?= htmlspecialchars($ap['groupe']) ?></span>
                                        </td>
                                        <td class="py-3 text-right">
                                            <form method="POST" onsubmit="return confirm('Retirer ce professeur ?')">
                                                <input type="hidden" name="action" value="remove_prof">
                                                <input type="hidden" name="affectation_id" value="<?= $ap['id'] ?>">
                                                <button class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if(empty($profs_affectes)): ?>
                                    <tr><td colspan="3" class="py-4 text-center text-gray-400 italic">Aucun professeur assigné.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <i class="fa-solid fa-users text-blue-600"></i> Groupes Étudiants
                            </h3>
                            <button onclick="document.getElementById('modalEnroll').classList.remove('hidden')" class="bg-white border border-blue-200 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg text-sm font-bold shadow-sm transition">
                                <i class="fa-solid fa-user-plus mr-1"></i> Inscrire des étudiants
                            </button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                    <tr>
                                        <th class="p-3 rounded-tl-lg">Groupe</th>
                                        <th class="p-3">Étudiant</th>
                                        <th class="p-3 rounded-tr-lg text-right">Options</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 border border-gray-100 rounded-b-lg">
                                    <?php 
                                    // On regroupe visuellement par Groupe si on veut, ou liste simple
                                    foreach($etudiants_inscrits as $etu): 
                                    ?>
                                    <tr class="hover:bg-gray-50 group">
                                        <td class="p-3">
                                            <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded text-xs border border-blue-100">
                                                <?= htmlspecialchars($etu['groupe']) ?>
                                            </span>
                                        </td>
                                        <td class="p-3">
                                            <span class="font-bold text-gray-700"><?= htmlspecialchars($etu['nom'] . ' ' . $etu['prenom']) ?></span>
                                            <span class="text-xs text-gray-400 ml-2"><?= htmlspecialchars($etu['email']) ?></span>
                                        </td>
                                        <td class="p-3 text-right">
                                             <form method="POST" onsubmit="return confirm('Désinscrire cet étudiant ?')">
                                                <input type="hidden" name="action" value="remove_student">
                                                <input type="hidden" name="inscription_id" value="<?= $etu['id'] ?>">
                                                <button class="text-gray-300 hover:text-red-600 opacity-0 group-hover:opacity-100 transition"><i class="fa-solid fa-xmark"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($etudiants_inscrits)): ?>
                                        <tr><td colspan="3" class="p-6 text-center text-gray-400">Aucun étudiant inscrit à cette matière.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <i class="fa-solid fa-arrow-left text-4xl mb-4 animate-bounce"></i>
                        <p class="text-lg font-medium">Sélectionnez une matière à gauche</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<div id="modalEnroll" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-2xl flex flex-col max-h-[90vh]">
        <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
            <i class="fa-solid fa-user-plus text-blue-600"></i> Inscrire des étudiants
        </h3>
        
        <form method="POST" class="flex-grow flex flex-col min-h-0">
            <input type="hidden" name="action" value="enroll_students">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dans le groupe</label>
                <input type="text" name="groupe" value="TD1" required class="w-full border-gray-300 rounded p-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
            </div>

            <div class="mb-2">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sélectionner les étudiants</label>
                <input type="text" id="searchStudent" placeholder="🔍 Filtrer la liste..." class="w-full border-gray-200 rounded p-2 text-sm mb-2 bg-gray-50">
            </div>

            <div class="flex-grow overflow-y-auto border border-gray-200 rounded-lg p-2 bg-gray-50">
                <?php foreach($all_students as $stu): ?>
                    <label class="flex items-center gap-3 p-2 hover:bg-white hover:shadow-sm rounded cursor-pointer transition student-item">
                        <input type="checkbox" name="etudiants[]" value="<?= $stu['id'] ?>" class="w-4 h-4 text-blue-600 rounded border-gray-300">
                        <div>
                            <p class="font-bold text-sm text-gray-700 student-name"><?= htmlspecialchars($stu['nom'] . ' ' . $stu['prenom']) ?></p>
                            <p class="text-xs text-gray-400"><?= htmlspecialchars($stu['email']) ?></p>
                        </div>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t border-gray-100 mt-4">
                <button type="button" onclick="document.getElementById('modalEnroll').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded text-sm font-bold">Annuler</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm font-bold hover:bg-blue-700 shadow">Inscrire la sélection</button>
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
        if(name.includes(term)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

<?php require 'views/layout/footer.php'; ?>