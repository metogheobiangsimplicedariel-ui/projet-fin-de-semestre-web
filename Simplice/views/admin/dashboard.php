<?php 

$title = "Tableau de bord";

require 'views/layout/header.php'; 
?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">

<?php require 'views/admin/sidebar.php' ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">
        
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <span class="text-slate-400 font-normal">Dashboard /</span> Vue d'ensemble
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

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                
                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Étudiants</p>
                        <p class="text-2xl font-extrabold text-gray-800"><?= $countStudents ?></p>
                    </div>
                    <div class="h-10 w-10 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center text-lg">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-purple-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Professeurs</p>
                        <p class="text-2xl font-extrabold text-gray-800"><?= $countProfs ?></p>
                    </div>
                    <div class="h-10 w-10 bg-purple-50 text-purple-500 rounded-lg flex items-center justify-center text-lg">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-orange-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">En attente</p>
                        <p class="text-2xl font-extrabold text-gray-800"><?= $pending_users ?></p>
                    </div>
                    <div class="h-10 w-10 bg-orange-50 text-orange-500 rounded-lg flex items-center justify-center text-lg animate-pulse">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wide">Classes</p>
                        <p class="text-2xl font-extrabold text-gray-800"><?= $classes ?></p>
                    </div>
                    <div class="h-10 w-10 bg-green-50 text-green-500 rounded-lg flex items-center justify-center text-lg">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                
                <button onclick="document.getElementById('modalAddStudent').classList.remove('hidden')" 
                        class="flex items-center justify-center gap-4 p-6 bg-white border-2 border-dashed border-slate-300 rounded-xl text-slate-600 font-bold hover:border-blue-500 hover:text-blue-600 hover:bg-blue-50 transition duration-200 group">
                    <div class="h-10 w-10 rounded-full bg-slate-100 group-hover:bg-blue-200 flex items-center justify-center transition">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <span>Ajouter un étudiant</span>
                </button>

                <button onclick="document.getElementById('modalAddProf').classList.remove('hidden')" 
                        class="flex items-center justify-center gap-4 p-6 bg-white border-2 border-dashed border-slate-300 rounded-xl text-slate-600 font-bold hover:border-purple-500 hover:text-purple-600 hover:bg-purple-50 transition duration-200 group">
                    <div class="h-10 w-10 rounded-full bg-slate-100 group-hover:bg-purple-200 flex items-center justify-center transition">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                    <span>Ajouter un professeur</span>
                </button>

                <button onclick="alert('Module Annonces à venir !')" 
                        class="flex items-center justify-center gap-4 p-6 bg-white border-2 border-dashed border-slate-300 rounded-xl text-slate-600 font-bold hover:border-orange-500 hover:text-orange-600 hover:bg-orange-50 transition duration-200 group">
                    <div class="h-10 w-10 rounded-full bg-slate-100 group-hover:bg-orange-200 flex items-center justify-center transition">
                        <i class="fa-solid fa-bullhorn"></i>
                    </div>
                    <span>Publier une annonce</span>
                </button>

            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Dernières inscriptions</h3>
                    <a href="index.php?page=admin_users" class="text-sm text-blue-600 font-bold hover:underline">Voir tout</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr>
                                <th class="p-4">Utilisateur</th>
                                <th class="p-4">Rôle</th>
                                <th class="p-4">Date</th>
                                <th class="p-4">Statut</th>
                                <th class="p-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            
                            <?php if(empty($latest_users)): ?>
                                <tr><td colspan="5" class="p-6 text-center text-gray-400">Aucune inscription récente.</td></tr>
                            <?php else: ?>
                                <?php foreach($latest_users as $u): ?>
                                <tr class="hover:bg-gray-50 transition">
                                    
                                    <td class="p-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center font-bold text-xs uppercase border border-slate-200">
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
                                                'etudiant'   => 'bg-blue-50 text-blue-700 border-blue-100',
                                                'professeur' => 'bg-purple-50 text-purple-700 border-purple-100',
                                                'admin'      => 'bg-red-50 text-red-700 border-red-100'
                                            ];
                                            $css = $badges[$u['role']] ?? 'bg-gray-50 text-gray-600';
                                        ?>
                                        <span class="<?= $css ?> px-2 py-1 rounded-md text-xs font-bold uppercase border">
                                            <?= htmlspecialchars($u['role']) ?>
                                        </span>
                                    </td>

                                    <td class="p-4 text-gray-600 font-medium">
                                        <?= $u['date_fmt'] ?>
                                    </td>

                                    <td class="p-4">
                                        <?php if($u['actif']): ?>
                                            <div class="flex items-center gap-1.5">
                                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                                <span class="text-green-700 font-bold text-xs">Actif</span>
                                            </div>
                                        <?php else: ?>
                                            <div class="flex items-center gap-1.5">
                                                <span class="h-2 w-2 rounded-full bg-orange-400"></span>
                                                <span class="text-orange-600 font-bold text-xs">En attente</span>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="p-4 text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="index.php?page=admin_users&edit=<?= $u['id'] ?>" class="text-gray-400 hover:text-blue-600 p-1">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="index.php?page=delete_user&id=<?= $u['id'] ?>" onclick="return confirm('Supprimer ?')" class="text-gray-400 hover:text-red-600 p-1">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
                                    </td>

                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </tbody>
                    </table>
                </div>
            </div>

        </div>


        <div id="modalAddStudent" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Nouvel Étudiant</h2>
                    <button onclick="document.getElementById('modalAddStudent').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <form action="index.php?page=admin_users" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create_user">
                    <input type="hidden" name="role" value="etudiant"> <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom</label>
                            <input type="text" name="nom" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Prénom</label>
                            <input type="text" name="prenom" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                        <input type="email" name="email" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mot de passe provisoire</label>
                        <input type="password" name="password" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-blue-500 outline-none">
                    </div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalAddStudent').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg font-bold">Annuler</button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700">Créer l'étudiant</button>
                    </div>
                </form>
            </div>
        </div>

        <div id="modalAddProf" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-lg">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-800">Nouveau Professeur</h2>
                    <button onclick="document.getElementById('modalAddProf').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-xl"></i>
                    </button>
                </div>
                <form action="index.php?page=admin_users" method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create_user">
                    <input type="hidden" name="role" value="professeur"> <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom</label>
                            <input type="text" name="nom" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Prénom</label>
                            <input type="text" name="prenom" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-purple-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email</label>
                        <input type="email" name="email" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Mot de passe provisoire</label>
                        <input type="password" name="password" required class="w-full border border-gray-200 rounded-lg p-2.5 focus:ring-2 focus:ring-purple-500 outline-none">
                    </div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('modalAddProf').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:bg-gray-100 rounded-lg font-bold">Annuler</button>
                        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-purple-700">Créer le professeur</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<?php require 'views/layout/footer.php'; ?>