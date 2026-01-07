<?php 

// 1. Sécurité
if (!isset($_SESSION['auth'])) {
    header('Location: index.php?page=login');
    exit();
}
$user = $_SESSION['auth'];

// 2. DONNÉES FICTIVES (Pour simuler la BDD en attendant)
// Dans le futur, ceci sera remplacé par des requêtes SQL (SELECT * FROM notes WHERE student_id = ...)
$student_stats = [
    'moyenne_generale' => 14.5,
    'rang' => '5ème / 42',
    'ects_valides' => 24
];

$bulletin = [
    [
        'matiere' => 'Développement Web (PHP)',
        'ds' => 16,
        'tp' => 18,
        'exam' => 15,
        'moyenne' => 16.3,
        'statut' => 'Validé'
    ],
    [
        'matiere' => 'Base de données (SQL)',
        'ds' => 14,
        'tp' => 15,
        'exam' => 12,
        'moyenne' => 13.6,
        'statut' => 'Validé'
    ],
    [
        'matiere' => 'Algorithmique & C',
        'ds' => 9,
        'tp' => 11,
        'exam' => 8,
        'moyenne' => 9.3,
        'statut' => 'Rattrapage' // Note inférieure à 10
    ],
    [
        'matiere' => 'Gestion de Projet',
        'ds' => 15,
        'tp' => null, // Pas de TP dans cette matière
        'exam' => 14,
        'moyenne' => 14.5,
        'statut' => 'Validé'
    ]
];

$documents = [
    ['nom' => 'Relevé de notes - Semestre 1', 'date' => '15 Jan 2026', 'type' => 'PDF'],
    ['nom' => 'Attestation de réussite', 'date' => '02 Sept 2025', 'type' => 'PDF'],
];

require 'views/layout/header.php'; 
?>

<div class="flex h-screen bg-gray-50 font-sans overflow-hidden">

    <aside class="w-64 bg-white border-r border-gray-200 hidden lg:flex flex-col z-20">
        <div class="h-24 flex items-center justify-center border-b border-gray-100">
            <div class="flex items-center gap-2 text-studify-blue">
                <i class="fa-solid fa-eye text-2xl"></i>
                <span class="text-xl font-extrabold tracking-wide uppercase">STUDIFY</span>
            </div>
        </div>

        <nav class="flex-grow p-4 space-y-2">
            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Scolarité</p>
            
            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-studify-blue text-white rounded-xl shadow-md transition">
                <i class="fa-solid fa-chart-line"></i>
                <span class="font-medium">Tableau de bord</span>
            </a>
            
            <a href="#" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-blue-50 hover:text-studify-blue rounded-xl transition">
                <i class="fa-regular fa-file-pdf"></i>
                <span class="font-medium">Mes Documents</span>
            </a>

            <div class="mt-auto pt-10">
                <a href="index.php?page=logout" class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl transition">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="font-medium">Déconnexion</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="flex-grow flex flex-col h-screen overflow-y-auto">
        
        <header class="h-64 bg-white shadow-sm flex items-center justify-between px-8 sticky top-0 z-10">
            <div class="lg:hidden text-studify-blue font-bold text-xl">STUDIFY</div>
            
            <div class="hidden md:block">
                <h1 class="text-2xl font-bold text-gray-800">Mon Espace Étudiant</h1>
                <p class="text-sm text-gray-500">Semestre 2 • Année 2025/2026</p>
            </div>

            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-700">
                        <?= htmlspecialchars($user['nom'] . ' ' . $user['prenom']) ?>
                    </p>
                    <p class="text-xs text-gray-500 uppercase badge bg-blue-100 text-blue-600 px-2 py-0.5 rounded-full inline-block">
                        <?= htmlspecialchars($user['role']) ?>
                    </p>
                </div>
                <div class="h-[6rem] w-[6rem] flex items-center justify-center shadow-sm py-5">
                    <img src="assets/images/avatar1.svg" 
                    alt="Profil" 
                    class="h-[5rem] w-[5rem] rounded-full object-cover border-[6px] border-studify-blue shadow-sm bg-white">
                </div>
                </div>
        </header>

        <div class="p-6 lg:p-10 space-y-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden group">
                    <div class="relative z-10">
                        <p class="text-sm text-gray-500 font-medium mb-1">Moyenne Générale</p>
                        <p class="text-3xl font-extrabold text-gray-800"><?= $student_stats['moyenne_generale'] ?><span class="text-lg text-gray-400 font-normal">/20</span></p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xl z-10">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div class="absolute right-0 top-0 h-full w-2 bg-green-500"></div>
                </div>

                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between relative overflow-hidden">
                    <div>
                        <p class="text-sm text-gray-500 font-medium mb-1">Classement Promo</p>
                        <p class="text-3xl font-extrabold text-studify-blue"><?= $student_stats['rang'] ?></p>
                    </div>
                    <div class="h-12 w-12 rounded-full bg-blue-100 text-studify-blue flex items-center justify-center text-xl">
                        <i class="fa-solid fa-trophy"></i>
                    </div>
                    <div class="absolute right-0 top-0 h-full w-2 bg-studify-blue"></div>
                </div>

                <div class="bg-gradient-to-br from-studify-blue to-blue-600 p-6 rounded-2xl shadow-md text-white flex flex-col justify-between">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Par rapport à la promo</p>
                            <p class="text-2xl font-bold mt-1">+ 2.5 pts</p>
                        </div>
                        <i class="fa-solid fa-arrow-trend-up text-2xl text-blue-200"></i>
                    </div>
                    <div class="w-full bg-blue-800/50 h-1.5 rounded-full mt-4">
                        <div class="bg-white h-1.5 rounded-full" style="width: 75%"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-800">
                        <i class="fa-solid fa-list-ol mr-2 text-studify-blue"></i> Bulletin de notes
                    </h3>
                    <button class="text-sm text-studify-blue hover:underline font-medium">
                        <i class="fa-solid fa-download mr-1"></i> Télécharger PDF
                    </button>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
                                <th class="p-5 font-semibold">Matière</th>
                                <th class="p-5 font-semibold text-center">DS (Coeff 1)</th>
                                <th class="p-5 font-semibold text-center">TP (Coeff 1)</th>
                                <th class="p-5 font-semibold text-center">Examen (Coeff 2)</th>
                                <th class="p-5 font-semibold text-center">Moyenne</th>
                                <th class="p-5 font-semibold text-center">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            <?php foreach($bulletin as $ligne): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-5 font-bold text-gray-700">
                                    <?= $ligne['matiere'] ?>
                                </td>
                                <td class="p-5 text-center text-gray-600">
                                    <?= $ligne['ds'] ?? '-' ?>
                                </td>
                                <td class="p-5 text-center text-gray-600">
                                    <?= $ligne['tp'] ?? '-' ?>
                                </td>
                                <td class="p-5 text-center text-gray-600 font-medium">
                                    <?= $ligne['exam'] ?>
                                </td>
                                <td class="p-5 text-center">
                                    <span class="font-bold text-base <?= $ligne['moyenne'] >= 10 ? 'text-green-600' : 'text-red-500' ?>">
                                        <?= $ligne['moyenne'] ?>
                                    </span>
                                </td>
                                <td class="p-5 text-center">
                                    <?php if($ligne['moyenne'] >= 10): ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fa-solid fa-check mr-1"></i> Validé
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <i class="fa-solid fa-triangle-exclamation mr-1"></i> Rattrapage
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Documents Officiels</h3>
                    <div class="space-y-3">
                        <?php foreach($documents as $doc): ?>
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-blue-50 transition group cursor-pointer border border-transparent hover:border-blue-100">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 bg-red-100 text-red-500 rounded-lg flex items-center justify-center text-xl">
                                    <i class="fa-solid fa-file-pdf"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-700 group-hover:text-studify-blue transition"><?= $doc['nom'] ?></p>
                                    <p class="text-xs text-gray-400"><?= $doc['date'] ?></p>
                                </div>
                            </div>
                            <i class="fa-solid fa-download text-gray-400 group-hover:text-studify-blue"></i>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">Évolution des résultats</h3>
                    
                    <div class="flex-grow flex items-end justify-between px-4 gap-4 h-40">
                        <div class="w-full flex flex-col items-center gap-2 group">
                            <div class="text-xs font-bold opacity-0 group-hover:opacity-100 transition">12.5</div>
                            <div class="w-full bg-blue-100 rounded-t-lg h-[60%] group-hover:bg-studify-blue transition-colors duration-300"></div>
                            <div class="text-xs text-gray-500 font-bold">Sem 1</div>
                        </div>
                        <div class="w-full flex flex-col items-center gap-2 group">
                            <div class="text-xs font-bold opacity-0 group-hover:opacity-100 transition">13.8</div>
                            <div class="w-full bg-blue-100 rounded-t-lg h-[70%] group-hover:bg-studify-blue transition-colors duration-300"></div>
                            <div class="text-xs text-gray-500 font-bold">Sem 2</div>
                        </div>
                        <div class="w-full flex flex-col items-center gap-2 group">
                            <div class="text-xs font-bold text-studify-blue">14.5</div>
                            <div class="w-full bg-studify-blue rounded-t-lg h-[75%] shadow-lg shadow-blue-200"></div>
                            <div class="text-xs text-studify-blue font-bold">Sem 3</div>
                        </div>
                         <div class="w-full flex flex-col items-center gap-2 opacity-50">
                            <div class="w-full bg-gray-100 rounded-t-lg h-[10%] border border-dashed border-gray-300"></div>
                            <div class="text-xs text-gray-400">Sem 4</div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </main>
</div>

<?php require 'views/layout/footer.php'; ?>