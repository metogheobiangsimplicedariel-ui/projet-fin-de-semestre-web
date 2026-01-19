<?php
// views/auth/register.php
$title = "Studify - Inscription";
require 'views/layout/header.php';

$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];

unset($_SESSION['errors'], $_SESSION['old_input']);
?>

<body class="bg-studify-bg flex flex-col font-sans">
    <main class="flex-grow min-h-screen flex flex-col lg:flex-row w-full relative z-10">

        <div class="w-full lg:w-5/12 flex-col p-12 bg-studify-bg">
            <div class="flex items-center gap-3 text-gray-800 mb-8">
                <i class="fa-solid fa-eye text-3xl"></i>
                <span class="text-2xl font-extrabold tracking-wide uppercase text-gray-700">STUDIFY</span>
            </div>

            <div class="w-full flex items-center justify-center relative mt-10">
                <div class="bg-white rounded-[35px] shadow-xl w-full max-w-md p-8 md:p-10 my-10">
                    <div class="flex mb-10 text-sm font-bold tracking-wide uppercase">
                        <a href="index.php?page=login" class="pb-2 mr-6 tab-inactive hover:text-gray-500 transition">Se Connecter</a>
                        <span class="pb-2 tab-active border-b-2 border-studify-primary">S'inscrire</span>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md">
                            <div class="flex">
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-red-800">Oups !</h3>
                                    <ul class="list-disc list-inside text-sm text-red-700 mt-1">
                                        <?php foreach ($errors as $err): ?>
                                            <li><?= $err; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <form action="index.php?page=register_verify" method="POST">
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <label class="block text-studify-primary font-bold mb-2 text-sm">Nom</label>
                                <input type="text" name="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" placeholder="Dupont" required
                                    class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:ring-2 focus:ring-studify-blue/50 outline-none">
                            </div>
                            <div>
                                <label class="block text-studify-primary font-bold mb-2 text-sm">Prénom</label>
                                <input type="text" name="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" placeholder="Alice" required
                                    class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:ring-2 focus:ring-studify-blue/50 outline-none">
                            </div>
                        </div>

                        <div class="mb-5">
                            <label class="block text-studify-primary font-bold mb-2 text-sm">Email</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="exemple@email.com" required
                                class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:ring-2 focus:ring-studify-blue/50 outline-none">
                        </div>

                        <div class="mb-5">
                            <label class="block text-studify-primary font-bold mb-2 text-sm">Mot de passe</label>
                            <div class="relative">
                                <input type="password" name="passwd" id="reg_password" placeholder="Min. 6 caractères" required
                                    class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:ring-2 focus:ring-studify-blue/50 outline-none pr-12">
                                <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400" data-target="#reg_password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-8">
                            <label class="block text-studify-primary font-bold mb-2 text-sm">Confirmer</label>
                            <div class="relative">
                                <input type="password" name="confirm_passwd" id="confirm_password" placeholder="Répétez le mot de passe" required
                                    class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:ring-2 focus:ring-studify-blue/50 outline-none pr-12">
                                <button type="button" class="toggle-password absolute inset-y-0 right-0 flex items-center px-4 text-gray-400" data-target="#confirm_password">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="w-full bg-studify-primary hover:bg-studify-primaryHover text-white font-bold py-3 rounded-xl shadow-lg transition transform active:scale-95">
                            S'inscrire
                        </button>

                        <div class="mt-6 text-center">
                            <a href="index.php?page=login" class="text-studify-red text-xs font-bold hover:underline">
                                Vous Avez Déjà Un Compte ?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="hidden lg:flex w-7/12 bg-studify-blue relative flex-col p-12 rounded-bl-[120px] shadow-lg z-20">
            <div class="w-full flex items-center justify-center relative h-full">
                <img src="assets/images/banner2.svg" alt="Illustration" class="object-contain w-full h-3/4 drop-shadow-2xl">
            </div>
        </div>
    </main>

    <?php require 'views/layout/footer.php'; ?>