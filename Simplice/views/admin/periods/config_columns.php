<?php require 'views/layout/header.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">

    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">

        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200 flex-shrink-0">
            <div class="flex items-center gap-2">
                <a href="index.php?page=admin_periods" class="text-slate-400 font-normal hover:text-slate-600 transition">Périodes /</a>
                <h1 class="text-xl font-bold text-gray-800">Structure des Notes</h1>
            </div>
            <div class="flex items-center gap-4">
                 <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-700">Administrateur</p>
                </div>
                <div class="h-10 w-10 rounded-full bg-slate-900 text-white flex items-center justify-center shadow-md border-2 border-red-500">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
            </div>
        </header>

        <div class="bg-slate-800 text-white px-8 py-4 flex justify-between items-center shadow-inner flex-shrink-0">
            <div>
                <p class="text-xs uppercase text-slate-400 font-bold tracking-wider">Configuration pour la période</p>
                <h2 class="text-xl font-bold text-white"><?= htmlspecialchars($periode['nom']) ?> <span class="text-slate-400 text-sm font-normal">(<?= htmlspecialchars($periode['code']) ?>)</span></h2>
            </div>
            <div class="text-right">
                <span class="bg-slate-700 px-3 py-1 rounded text-xs font-bold border border-slate-600">
                    <?= htmlspecialchars($periode['annee_universitaire']) ?>
                </span>
            </div>
        </div>

        <div class="flex-grow flex flex-col md:flex-row overflow-hidden">
            
            <div class="w-full md:w-80 bg-white border-r border-gray-200 flex flex-col h-full overflow-y-auto">
                <div class="p-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-xs font-bold text-gray-500 uppercase">Sélectionner une matière</h3>
                </div>
                
                <?php if(empty($matieres)): ?>
                    <div class="p-6 text-center text-gray-400 text-sm">
                        <i class="fa-solid fa-folder-open text-2xl mb-2"></i>
                        <p>Aucune matière.</p>
                        <a href="index.php?page=admin_subjects" class="text-blue-600 underline">En créer une ?</a>
                    </div>
                <?php else: ?>
                    <div class="divide-y divide-gray-100">
                        <?php foreach($matieres as $m): ?>
                            <?php 
                                $isActive = ($m['id'] === $current_matiere_id);
                                $bgClass = $isActive ? 'bg-blue-50 border-l-4 border-blue-600' : 'hover:bg-gray-50 border-l-4 border-transparent';
                            ?>
                            <a href="index.php?page=admin_config_cols&periode=<?= $periode_id ?>&matiere=<?= $m['id'] ?>" 
                               class="block p-4 transition-all <?= $bgClass ?>">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-bold text-gray-800 text-sm"><?= htmlspecialchars($m['nom']) ?></p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fa-solid fa-graduation-cap text-[10px] mr-1"></i>
                                            <?= htmlspecialchars($m['nom_filiere']) ?>
                                        </p>
                                    </div>
                                    <?php if($isActive): ?>
                                        <i class="fa-solid fa-chevron-right text-xs text-blue-600 mt-1"></i>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex-grow bg-gray-50 p-6 lg:p-8 h-full overflow-y-auto">
                
                <?php if($current_matiere): ?>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-plus-circle text-blue-500"></i> Nouvelle colonne
                        </h3>
                        
                        <?php if(isset($_SESSION['error'])): ?>
                            <div class="bg-red-50 text-red-600 p-3 rounded text-sm mb-4 border border-red-200">
                                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if(isset($_SESSION['success'])): ?>
                            <div class="bg-green-50 text-green-600 p-3 rounded text-sm mb-4 border border-green-200">
                                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <input type="hidden" name="action" value="add_column">
                            
                            <div class="md:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Libellé <span class="text-red-500">*</span></label>
                                <input type="text" name="nom_colonne" placeholder="Ex: Devoir Surveillé 1" required 
                                       class="w-full border border-gray-300 rounded p-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1" title="Utilisé pour les formules">Code Unique <span class="text-red-500">*</span></label>
                                <input type="text" name="code_colonne" placeholder="Ex: DS1" required 
                                       class="w-full border border-gray-300 rounded p-2 text-sm uppercase focus:ring-2 focus:ring-blue-500 outline-none"
                                       title="Sans espace, ex: DS1, TP2">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Barème</label>
                                <input type="number" name="note_max" value="20" required class="w-full border border-gray-300 rounded p-2 text-sm text-center">
                            </div>

                            <div class="md:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Coeff</label>
                                <input type="number" name="coefficient" value="1.0" step="0.1" required class="w-full border border-gray-300 rounded p-2 text-sm text-center">
                            </div>

                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                                <select name="type" class="w-full border border-gray-300 rounded p-2 text-sm bg-white">
                                    <option value="note">Note standard</option>
                                    <option value="bonus">Bonus (+)</option>
                                    <option value="malus">Malus (-)</option>
                                    <option value="info">Info (non noté)</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-1 flex items-center justify-center pb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="obligatoire" checked class="w-4 h-4 text-blue-600 rounded">
                                    <span class="text-xs font-bold text-gray-500">Oblig.</span>
                                </label>
                            </div>

                            <div class="md:col-span-2">
                                <button type="submit" class="w-full bg-slate-900 text-white px-4 py-2 rounded font-bold hover:bg-slate-700 transition">
                                    Ajouter
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-700">Colonnes configurées pour <span class="text-blue-600"><?= htmlspecialchars($current_matiere['nom']) ?></span></h3>
                            
                            <div class="flex gap-2">
                                <button onclick="document.getElementById('modalDuplicate').classList.remove('hidden')" class="bg-white border border-gray-300 text-gray-600 px-3 py-1 rounded text-xs font-bold hover:bg-gray-100 transition shadow-sm">
                                    <i class="fa-solid fa-copy mr-1"></i> Dupliquer depuis...
                                </button>
                                <span class="bg-gray-200 text-gray-600 text-xs font-bold px-2 py-1 rounded"><?= count($colonnes) ?> col.</span>
                            </div>
                        </div>
                        
                        <?php if(empty($colonnes)): ?>
                            <div class="p-12 text-center text-gray-400">
                                <i class="fa-solid fa-table-list text-4xl mb-3 opacity-20"></i>
                                <p>Aucune colonne définie pour le moment.</p>
                            </div>
                        <?php else: ?>
                            <table class="w-full text-left text-sm">
                                <thead class="bg-white text-gray-500 uppercase text-xs border-b border-gray-100">
                                    <tr>
                                        <th class="p-4 text-center text-gray-300 w-10">#</th>
                                        <th class="p-4">Code</th>
                                        <th class="p-4">Libellé</th>
                                        <th class="p-4 text-center">Barème</th>
                                        <th class="p-4 text-center">Coeff</th>
                                        <th class="p-4 text-center">Type</th>
                                        <th class="p-4 text-center">Options</th>
                                        <th class="p-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="sortableTable" class="divide-y divide-gray-50">
                                    <?php foreach($colonnes as $col): ?>
                                        <tr data-id="<?= $col['id'] ?>" class="hover:bg-gray-50 transition group bg-white">
                                            
                                            <td class="p-4 text-center text-gray-300 cursor-move handle">
                                                <i class="fa-solid fa-grip-vertical"></i>
                                            </td>

                                            <td class="p-4">
                                                <span class="font-mono font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs border border-blue-100">
                                                    <?= htmlspecialchars($col['code_colonne']) ?>
                                                </span>
                                            </td>
                                            <td class="p-4 font-bold text-gray-700"><?= htmlspecialchars($col['nom_colonne']) ?></td>
                                            <td class="p-4 text-center text-gray-600">/ <?= floatval($col['note_max']) ?></td>
                                            <td class="p-4 text-center">
                                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-bold border border-gray-200">
                                                    x <?= floatval($col['coefficient']) ?>
                                                </span>
                                            </td>
                                            <td class="p-4 text-center">
                                                 <?php 
                                                    $types = [
                                                        'note' => ['text' => 'Note', 'class' => 'text-gray-500'],
                                                        'bonus' => ['text' => 'Bonus', 'class' => 'text-green-600 font-bold'],
                                                        'malus' => ['text' => 'Malus', 'class' => 'text-red-600 font-bold'],
                                                        'info' => ['text' => 'Info', 'class' => 'text-blue-400 italic']
                                                    ];
                                                    $t = $types[$col['type']] ?? $types['note'];
                                                ?>
                                                <span class="<?= $t['class'] ?> text-xs uppercase"><?= $t['text'] ?></span>
                                            </td>
                                            <td class="p-4 text-center">
                                                <?php if($col['obligatoire']): ?>
                                                    <span class="text-xs font-bold text-green-600 border border-green-200 bg-green-50 px-2 py-0.5 rounded-full">Oblig.</span>
                                                <?php else: ?>
                                                    <span class="text-xs text-gray-400">Facultatif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="p-4 text-right flex justify-end gap-2">
                                                <button type="button" 
                                                        onclick='openEditModal(<?= json_encode($col) ?>)'
                                                        class="text-gray-400 hover:text-blue-600 p-2 transition">
                                                    <i class="fa-solid fa-pen"></i>
                                                </button>

                                                <form method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ?');">
                                                    <input type="hidden" name="action" value="delete_column">
                                                    <input type="hidden" name="config_id" value="<?= $col['id'] ?>">
                                                    <button type="submit" class="text-gray-400 hover:text-red-600 p-2 transition">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>

                <?php else: ?>
                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                        <i class="fa-solid fa-arrow-left text-4xl mb-4 animate-bounce"></i>
                        <p class="text-lg font-medium">Sélectionnez une matière à gauche</p>
                        <p class="text-sm">pour configurer ses colonnes de notes.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    </main>
</div>

<div id="modalDuplicate" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-2">Dupliquer une configuration</h3>
        <p class="text-sm text-gray-500 mb-4">Copier les colonnes d'une autre matière vers celle-ci.</p>
        
        <form method="POST">
            <input type="hidden" name="action" value="duplicate_config">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Matière Source</label>
                <select name="source_matiere_id" class="w-full border rounded p-2 text-sm bg-gray-50">
                    <?php foreach($matieres as $m): ?>
                        <?php if($m['id'] !== $current_matiere_id): ?>
                            <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['nom']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalDuplicate').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">Annuler</button>
                <button type="submit" class="bg-slate-900 text-white px-4 py-2 rounded font-bold hover:bg-slate-700">Copier</button>
            </div>
        </form>
    </div>
</div>

<div id="modalEditColumn" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-lg">
        <h3 class="text-lg font-bold mb-4">Modifier la colonne</h3>
        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="edit_column">
            <input type="hidden" name="column_id" id="edit_column_id">
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Libellé</label>
                    <input type="text" name="nom_colonne" id="edit_nom" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code</label>
                    <input type="text" name="code_colonne" id="edit_code" required class="w-full border rounded p-2 text-sm uppercase">
                </div>
            </div>
            
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Max</label>
                    <input type="number" name="note_max" id="edit_max" required class="w-full border rounded p-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Coeff</label>
                    <input type="number" name="coefficient" id="edit_coeff" step="0.1" required class="w-full border rounded p-2 text-sm">
                </div>
                 <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Type</label>
                    <select name="type" id="edit_type" class="w-full border rounded p-2 text-sm">
                        <option value="note">Note</option>
                        <option value="bonus">Bonus</option>
                        <option value="malus">Malus</option>
                        <option value="info">Info</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <button type="button" onclick="document.getElementById('modalEditColumn').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded">Annuler</button>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded font-bold hover:bg-blue-700">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialiser SortableJS si l'élément existe
    const tableElement = document.getElementById('sortableTable');
    if (tableElement) {
        Sortable.create(tableElement, {
            handle: '.handle', // Seul l'icône poignée déclenche le drag
            animation: 150,
            ghostClass: 'bg-blue-50', // Couleur de l'élément fantôme
            onEnd: function (evt) {
                // Créer un tableau avec le nouvel ordre des IDs
                let order = [];
                tableElement.querySelectorAll('tr').forEach(function(row) {
                    order.push(row.getAttribute('data-id'));
                });

                // Envoi AJAX
                const formData = new FormData();
                formData.append('action', 'reorder_columns');
                order.forEach((id, index) => {
                    formData.append(`order[${index}]`, id);
                });

                // Pas besoin de recharger la page, l'ordre est sauvé en silence
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => console.log('Ordre sauvegardé'))
                .catch(err => console.error('Erreur sauvegarde ordre', err));
            }
        });
    }
});

// 2. Fonction pour ouvrir le modal d'édition avec les données
function openEditModal(colData) {
    document.getElementById('edit_column_id').value = colData.id;
    document.getElementById('edit_nom').value = colData.nom_colonne;
    document.getElementById('edit_code').value = colData.code_colonne;
    document.getElementById('edit_max').value = colData.note_max;
    document.getElementById('edit_coeff').value = colData.coefficient;
    document.getElementById('edit_type').value = colData.type;
    
    document.getElementById('modalEditColumn').classList.remove('hidden');
}
</script>

<?php require 'views/layout/footer.php'; ?>