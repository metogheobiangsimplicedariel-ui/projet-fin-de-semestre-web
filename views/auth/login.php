<?php
// views/auth/login.php

// Le titre est utilisé par le header.php pour la balise <title>
$title = "Studify - Connexion";
require 'views/layout/header.php';

// 1. Récupération des messages flash via la session (initialisée dans index.php)
$success_msg = $_SESSION['success'] ?? null;
$errors = $_SESSION['errors'] ?? [];
$old_email = $_SESSION['old_input_login']['email'] ?? '';

// 2. Nettoyage immédiat pour que les messages ne s'affichent qu'une fois
unset($_SESSION['success'], $_SESSION['errors'], $_SESSION['old_input_login']);
?>

<body class="bg-studify-bg min-h-screen flex flex-col font-sans">

    <main class="flex-grow h-screen flex flex-col lg:flex-row w-full relative z-10">

        <div class="lg:hidden w-full lg:w-5/12 flex-col p-12 bg-studify-bg">
            <div class="flex items-center gap-3 text-gray-800 mb-8">
                <i class="fa-solid fa-eye text-3xl"></i>
                <span class="text-2xl font-extrabold tracking-wide uppercase text-gray-700">STUDIFY</span>
            </div>
        </div>

        <div class="hidden lg:flex w-7/12 bg-studify-blue relative flex-col p-12 rounded-br-[120px] shadow-lg z-20">
            <div class="flex items-center gap-3 text-gray-800 ">
                <i class="fa-solid fa-eye text-3xl"></i>
                <span class="text-2xl font-extrabold tracking-wide uppercase text-gray-700">STUDIFY</span>
            </div>

            <div class="w-full flex items-start justify-center relative">
                <div class="w-full h-3/4 relative">
                    <img src="assets/images/banner.svg"
                        alt="Illustration Login"
                        class="object-contain w-full h-full drop-shadow-2xl">
                </div>
            </div>
        </div>

        <div class="w-full flex flex-col justify-center items-center lg:min-h-0 lg:w-5/12 lg:flex lg:justify-center lg:items-center p-6 bg-studify-bg">

            <div class="bg-white rounded-[35px] shadow-xl w-full max-w-md p-8 md:p-10 my-10">

                <div class="flex mb-10 text-sm font-bold tracking-wide uppercase">
                    <span class="pb-2 mr-6 tab-active border-b-2 border-studify-primary cursor-default">
                        Se Connecter
                    </span>
                    <a href="index.php?page=register" class="pb-2 tab-inactive hover:text-gray-500 transition">
                        S'inscrire
                    </a>
                </div>

                <?php if ($success_msg): ?>
                    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r shadow-sm">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-circle-check text-green-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-green-800"><?= $success_msg ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (isset($errors['login'])): ?>
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r shadow-sm animate-pulse">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-circle-exclamation text-red-500 text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-bold text-red-800"><?= $errors['login'] ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form action="index.php?page=login_verify" method="post">

                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                    <div class="mb-5">
                        <label class="block text-studify-primary font-bold mb-2 text-sm">Email</label>
                        <input type="email" name="email"
                            value="<?= htmlspecialchars($old_email) ?>"
                            placeholder="exemple@email.com"
                            required
                            class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-studify-blue/50">
                    </div>

                    <div class="mb-8">
                        <label for="login_pass" class="block text-studify-primary font-bold mb-2 text-sm">
                            Mot de passe
                        </label>
                        <div class="relative">
                            <input
                                type="password"
                                name="passwd"
                                id="login_pass"
                                placeholder="Mot de passe"
                                required
                                autocomplete="current-password"
                                class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-studify-blue/50 pr-12">

                            <button
                                type="button"
                                class="toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400 hover:text-studify-primary focus:outline-none focus:text-studify-primary"
                                data-target="#login_pass"
                                aria-label="Afficher le mot de passe">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-studify-primary hover:bg-studify-primaryHover text-white font-bold py-3 rounded-xl shadow-md transition transform active:scale-95 text-lg">
                        Se Connecter
                    </button>

                    <div class="mt-6 text-center">
                        <a href="#" class="text-studify-red text-xs font-bold hover:underline">
                            Vous Avez Oublié Votre Mot De Passe ?
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <?php require 'views/layout/footer.php'; ?>