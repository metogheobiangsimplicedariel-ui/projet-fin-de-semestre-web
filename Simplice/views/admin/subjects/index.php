<?php $title = "Studify - Gestion des Matières"; ?>
<?php require 'views/layout/header.php'; ?>

<div class="flex h-screen bg-gray-100 font-sans overflow-hidden">
    <?php require 'views/admin/sidebar.php'; ?>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">
        <header class="h-20 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">
                <span class="text-gray-400 font-normal">Administration /</span> Matières
            </h1>
        </header>

        <div class="p-6 lg:p-10 space-y-8">

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                    <?= $_SESSION['error'];
                    unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 h-fit">
                    <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Nouvelle Matière</h2>

                    <form action="index.php?page=admin_subjects" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                        <input type="hidden" name="action" value="create_subject">

                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Code Matière</label>
                                <input type="text" name="code" placeholder="MATH101" required
                                    class="w-full bg-gray-50 border border-gray-200 rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none uppercase">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nom Complet</label>
                                <input type="text" name="nom" placeholder="Analyse Mathématique" required
                                    class="w-full bg-gray-50 border border-gray-200 rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Filière</label>
                                <select name="filiere_id" required class="w-full bg-gray-50 border border-gray-200 rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                                    <?php foreach ($filieres as $f): ?>
                                        <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['code'] . ' - ' . $f['nom']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Coeff.</label>
                                    <input type="number" step="0.1" name="coefficient" value="1.0" required
                                        class="w-full bg-gray-50 border border-gray-200 rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Crédits</label>
                                    <input type="number" name="credits" value="3" required
                                        class="w-full bg-gray-50 border border-gray-200 rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Seuil Validation</label>
                                <input type="number" step="0.5" name="seuil_validation" value="10" required
                                    class="w-full bg-gray-50 border border-gray-200 rounded p-2 focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>

                            <button type="submit" class="w-full bg-slate-800 text-white font-bold py-2 rounded hover:bg-slate-700 transition">
                                Ajouter au catalogue
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
                        <h3 class="font-bold text-gray-700">Catalogue (<?= count($matieres) ?>)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-100 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="p-4">Code</th>
                                    <th class="p-4">Matière</th>
                                    <th class="p-4">Filière</th>
                                    <th class="p-4 text-center">Coeff/ECTS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php foreach ($matieres as $m): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-4 font-mono font-bold text-blue-600"><?= htmlspecialchars($m['code']) ?></td>
                                        <td class="p-4 font-bold text-gray-800"><?= htmlspecialchars($m['nom']) ?></td>
                                        <td class="p-4 text-gray-600">
                                            <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-bold">
                                                <?= htmlspecialchars($m['code_filiere']) ?>
                                            </span>
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="text-gray-900 font-bold"><?= $m['coefficient'] ?></span>
                                            <span class="text-gray-400 mx-1">/</span>
                                            <span class="text-gray-600"><?= $m['credits'] ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>
<?php require 'views/layout/footer.php'; ?>