<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">

    <?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">

        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200 flex-shrink-0">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="text-slate-400 font-normal">Dashboard /</span> Utilisateurs
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

        <div class="p-6 lg:p-10 space-y-8">
            
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-200">
                <div class="flex items-center gap-2">
                    <h3 class="text-lg font-bold text-gray-800">Membres</h3>
                    <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full text-xs font-bold">
                        <?= count($all_users) ?>
                    </span>
                </div>

                <form action="index.php" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="page" value="admin_users">
                    
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" 
                               name="q" 
                               value="<?= isset($_GET['q']) ? htmlspecialchars($_GET['q']) : '' ?>" 
                               placeholder="Rechercher..." 
                               class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-blue-500 w-64 transition">
                    </div>
                    
                    <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white px-4 py-2 rounded-lg text-sm font-bold transition">
                        Filtrer
                    </button>
                    
                    <?php if(isset($_GET['q'])): ?>
                        <a href="index.php?page=admin_users" class="text-red-500 text-sm hover:underline ml-2">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase sticky top-0">
                            <tr>
                                <th class="p-4">Identité</th>
                                <th class="p-4">Rôle</th>
                                <th class="p-4">Inscription</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php foreach($all_users as $u): ?>
                            <tr class="hover:bg-gray-50 transition group">
                                
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-9 w-9 rounded bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs uppercase">
                                            <?= substr($u['prenom'], 0, 1) . substr($u['nom'], 0, 1) ?>
                                        </div>
                                        <div>
                                            <p class="font-bold text-gray-800"><?= htmlspecialchars($u['nom'] . ' ' . $u['prenom']) ?></p>
                                            <p class="text-xs text-gray-500"><?= htmlspecialchars($u['email']) ?></p>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4">
                                    <?php 
                                        $badges = [
                                            'admin' => 'bg-red-100 text-red-700 border-red-200',
                                            'professeur' => 'bg-purple-100 text-purple-700 border-purple-200',
                                            'etudiant' => 'bg-blue-100 text-blue-700 border-blue-200'
                                        ];
                                        $cssClass = $badges[$u['role']] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                                    ?>
                                    <span class="<?= $cssClass ?> border px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                                        <?= htmlspecialchars($u['role']) ?>
                                    </span>
                                </td>

                                <td class="p-4 text-gray-600">
                                    <i class="fa-regular fa-calendar mr-2 text-gray-400"></i>
                                    <?= isset($u['date_inscription']) ? $u['date_inscription'] : 'N/A' ?>
                                </td>

                               <td class="p-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        
                                        <button 
                                            onclick="openEditModal(this)"
                                            data-id="<?= $u['id'] ?>"
                                            data-nom="<?= htmlspecialchars($u['nom']) ?>"
                                            data-prenom="<?= htmlspecialchars($u['prenom']) ?>"
                                            data-email="<?= htmlspecialchars($u['email']) ?>"
                                            data-role="<?= htmlspecialchars($u['role']) ?>"
                                            class="p-2 bg-white border border-gray-200 rounded hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition" 
                                            title="Éditer">
                                            <i class="fa-solid fa-pen"></i>
                                        </button>
                                        
                                        <form method="POST" action="index.php?page=admin_users" onsubmit="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                            
                                            <button type="submit" class="p-2 bg-white border border-gray-200 rounded hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition" title="Supprimer">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="modelEditUser" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg transform transition-all scale-100">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Modification Utilisateur</h2>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="update_user">
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom</label>
                    <input type="text" name="nom" id="edit_nom" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Prénom</label>
                    <input type="text" name="prenom" id="edit_prenom" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                <input type="email" name="email" id="edit_email" required class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Rôle</label>
                <select name="role" id="edit_role" class="w-full bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="etudiant">Etudiant</option>
                    <option value="professeur">Professeur</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>
    
            <div class="pt-4 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 text-gray-600 font-bold hover:bg-gray-100 rounded-lg transition">Annuler</button>
                <button type="submit" class="bg-studify-blue text-white px-5 py-2.5 rounded-lg font-bold shadow-md hover:bg-blue-700 transition">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(button) {
    const id = button.getAttribute('data-id');
    const nom = button.getAttribute('data-nom');
    const prenom = button.getAttribute('data-prenom');
    const email = button.getAttribute('data-email');
    const role = button.getAttribute('data-role');

    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_nom').value = nom;
    document.getElementById('edit_prenom').value = prenom;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;

    document.getElementById('modelEditUser').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('modelEditUser').classList.add('hidden');
}
</script>

<?php require 'views/layout/footer.php'; ?>