<?php require 'views/layout/header.php'; ?>


<?php 
    // On récupère les erreurs s'il y en a
    $errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
    // On récupère les anciennes saisies
    $old = isset($_SESSION['old_input']) ? $_SESSION['old_input'] : [];

    // TRES IMPORTANT : On supprime les données de la session pour la prochaine fois
    unset($_SESSION['errors']);
    unset($_SESSION['old_input']);
?>


<body class="bg-studify-bg flex flex-col font-sans">

    <main class="flex-grow min-h-screen flex flex-col lg:flex-row w-full relative z-10">
        
        <div class="w-full lg:w-5/12 flex-col p-12 bg-studify-bg">
             <div class="flex items-center gap-3 text-gray-800 mb-8">
                <i class="fa-solid fa-eye text-3xl"></i>
                <span class="text-2xl font-extrabold tracking-wide uppercase text-gray-700">STUDIFY</span>
            </div>

             <div class="w-full flex items-center justify-center relative mt-20">
              
                <div class="bg-white rounded-[35px] shadow-xl w-full max-w-md p-8 md:p-10 my-10">
                        
                    <div class="flex mb-10 text-sm font-bold tracking-wide uppercase">
                        <a href="index.php?page=login" class="pb-2 mr-6 tab-inactive hover:text-gray-500 transition">Se Connecter</a>
                        <span class="pb-2 tab-active">S'inscrire</span>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-md animate-pulse">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fa-solid fa-circle-exclamation text-red-500"></i>
                                </div>
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
                        <div class="mb-5">
                            <label class="block text-studify-primary font-bold mb-2 text-sm">Nom Complet</label>
                            <input type="text" 
                                name="fullname" 
                                value="<?= isset($old['fullname']) ? htmlspecialchars($old['fullname']) : '' ?>"
                                placeholder="Alice Dupont" 
                                required 
                                class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-studify-blue/50">
                        </div>

                        <div class="mb-5">
                            <label class="block text-studify-primary font-bold mb-2 text-sm">Email</label>
                            <input type="email" 
                                name="email" 
                                value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>"
                                placeholder="exemple@email.com" 
                                required
                                class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-studify-blue/50">
                        </div>

                        <div class="mb-8">
                            <label class="block text-studify-primary font-bold mb-2 text-sm">Mot de passe</label>
                            <input type="password" name="passwd" placeholder="Mot de passe" class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-studify-blue/50">
                        </div>

                        <div class="mb-8">
                            <label class="block text-studify-primary font-bold mb-2 text-sm">Confirmer votre mot de passe</label>
                            <input type="password" name="confirm_passwd" placeholder="Confirmer votre mot de passe" class="w-full bg-studify-input text-gray-600 rounded px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-studify-blue/50">
                        </div>

                        <button class="w-full bg-studify-primary hover:bg-studify-primaryHover text-white font-bold py-3 rounded-lg shadow-lg transition transform active:scale-95">
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

        <div class="hidden lg:flex flex justify-center w-7/12 bg-studify-blue relative flex-col p-12 rounded-bl-[120px] shadow-lg z-20">
           

            <div class="w-full flex items-center justify-center relative">
                
                <div class="w-full h-3/4 relative">
                     <img src="assets/images/banner2.svg" 
                          alt="Illustration Login" 
                          class="object-contain w-full h-full drop-shadow-2xl">
                </div>
            </div>
        </div>

    </main>




    <?php require 'views/layout/footer.php'; ?>