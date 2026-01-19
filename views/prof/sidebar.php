<?php
// views/prof/sidebar.php
$page = $_GET['page'] ?? 'prof_dashboard';

// Fonction simple pour la classe active
function active($name, $p) {
    return $p === $name ? 'bg-indigo-800 text-white shadow-lg' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white';
}
?>

<aside class="w-64 bg-indigo-900 text-white flex flex-col flex-shrink-0 transition-all duration-300" id="sidebar">
    
    <div class="h-20 flex items-center justify-center border-b border-indigo-800">
        <div class="flex items-center gap-2 font-bold text-xl tracking-wider">
            <i class="fa-solid fa-graduation-cap text-indigo-400"></i>
            <span>MY GRADES</span>
        </div>
    </div>

    <div class="p-6 border-b border-indigo-800 bg-indigo-950/30">
        <div class="flex items-center gap-4">
            <div class="h-10 w-10 rounded-full bg-indigo-500 flex items-center justify-center font-bold border-2 border-indigo-300">
                <?= substr($_SESSION['auth']['prenom'], 0, 1) ?>
            </div>
            <div>
                <p class="text-sm font-bold"><?= htmlspecialchars($_SESSION['auth']['prenom']) ?></p>
                <p class="text-xs text-indigo-300">Professeur</p>
            </div>
        </div>
    </div>

    <nav class="flex-grow p-4 space-y-2 overflow-y-auto">
        <p class="text-xs font-bold text-indigo-400 uppercase px-4 mb-2 mt-2">Enseignement</p>
        
        <a href="index.php?page=prof_dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition <?= active('prof_dashboard', $page) ?>">
            <i class="fa-solid fa-chalkboard-user w-5"></i>
            <span class="font-medium">Mes Matières</span>
        </a>

        </nav>

    <div class="p-4 border-t border-indigo-800">
        <a href="index.php?page=logout" class="flex items-center gap-3 px-4 py-3 text-red-300 hover:bg-indigo-950 hover:text-red-200 rounded-xl transition">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span class="font-medium">Déconnexion</span>
        </a>
    </div>
</aside>