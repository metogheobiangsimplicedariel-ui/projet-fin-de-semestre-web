<?php
// 1. On récupère la page actuelle depuis l'URL (par défaut 'admin_dashboard')
$page = $_GET['page'] ?? 'admin_dashboard';

// 2. On définit les styles CSS dans des variables pour que le code soit plus lisible
$base_classes = "flex items-center gap-3 px-4 py-3 rounded-lg transition";
$active_classes = "bg-red-600 text-white shadow-md hover:bg-red-700";
$inactive_classes = "text-slate-300 hover:bg-slate-800 hover:text-white";

// 3. Petite fonction helper pour garder le HTML propre
function menuActive($target_pages, $current_page, $active, $inactive) {
    // Si la page actuelle est dans la liste des pages cibles, on active
    if (is_array($target_pages)) {
        return in_array($current_page, $target_pages) ? $active : $inactive;
    }
    return ($current_page === $target_pages) ? $active : $inactive;
}
?>

<aside class="w-64 bg-slate-900 text-white hidden lg:flex flex-col z-20 shadow-xl">
    
    <div class="h-20 flex items-center justify-center border-b border-slate-700 bg-slate-950">
        <div class="flex items-center gap-2 text-white">
            <i class="fa-solid fa-user-shield text-2xl text-red-500"></i>
            <span class="text-xl font-extrabold tracking-wide uppercase">ADMIN PANEL</span>
        </div>
    </div>

    <nav class="flex-grow p-4 space-y-2 text-sm">
        
        <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-4">Gestion</p>
        
        <a href="index.php?page=admin_dashboard" 
           class="<?= $base_classes ?> <?= menuActive('admin_dashboard', $page, $active_classes, $inactive_classes) ?>">
            <i class="fa-solid fa-gauge-high"></i>
            <span class="font-medium">Vue d'ensemble</span>
        </a>
        
        <a href="index.php?page=admin_users" 
           class="<?= $base_classes ?> <?= menuActive(['admin_users', 'edit_user', 'add_user'], $page, $active_classes, $inactive_classes) ?>">
            <i class="fa-solid fa-users"></i>
            <span class="font-medium">Utilisateurs</span>
        </a>

        <a href="index.php?page=admin_periods" 
           class="<?= $base_classes ?> <?= menuActive(['admin_periods', 'admin_config_cols'], $page, $active_classes, $inactive_classes) ?>">
            <i class="fa-solid fa-calendar-days"></i> 
            <span class="font-medium">Périodes & Notes</span>
        </a>

        <a href="index.php?page=admin_subjects" 
            class="<?= $base_classes ?> <?= menuActive('admin_subjects', $page, $active_classes, $inactive_classes) ?>">
                <i class="fa-solid fa-book-open"></i>
                <span class="font-medium">Matières</span>
        </a>

        <a href="index.php?page=admin_assignments" 
        class="<?= $base_classes ?> <?= menuActive('admin_assignments', $page, $active_classes, $inactive_classes) ?>">
            <i class="fa-solid fa-users-rectangle"></i>
            <span class="font-medium">Affectations</span>
        </a>

        <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-2 mt-8">Système</p>

        <a href="#" class="<?= $base_classes ?> <?= menuActive('settings', $page, $active_classes, $inactive_classes) ?>">
            <i class="fa-solid fa-gears"></i>
            <span class="font-medium">Paramètres</span>
        </a>
        
        <div class="mt-auto pt-10">
            <a href="index.php?page=logout" class="flex items-center gap-3 px-4 py-3 text-red-400 hover:bg-slate-800 rounded-lg transition">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span class="font-medium">Déconnexion</span>
            </a>
        </div>
    </nav>
</aside>