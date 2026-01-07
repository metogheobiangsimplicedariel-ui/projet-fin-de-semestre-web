<?php
// controllers/auth/loginController.php

require_once __DIR__ . '/../../config/database.php'; // Connexion BDD

if (!empty($_POST)) {
    
    // 1. Récupération des données
    $email_posted = $_POST['email'];
    $password_posted = $_POST['passwd'];
    $errors = [];

    // 2. Vérification basique (champs vides)
    if (empty($email_posted) || empty($password_posted)) {
        $errors['login'] = "Veuillez remplir tous les champs.";
    } else {
        
        // 3. On cherche l'utilisateur par son EMAIL
        $req = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = :email");
        $req->execute([':email' => $email_posted]);
        
        $user = $req->fetch(); // On récupère l'utilisateur sous forme d'objet

        // 4. LA VÉRIFICATION MAGIQUE
        // On vérifie deux choses en même temps :
        // A. Est-ce qu'on a trouvé un utilisateur ? ($user)
        // B. Est-ce que le mot de passe correspond au Hash ? (password_verify)
        if ($user && password_verify($password_posted, $user->mot_de_passe)) {
            
            
            // On stocke les infos importantes dans la session
            $_SESSION['auth'] = [
                'id' => $user->id,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'email' => $user->email,
                'role' => $user->role
            ];

            // --- REDIRECTION INTELLIGENTE SELON LE RÔLE ---
            switch ($user->role) {
                case 'admin':
                    header('Location: index.php?page=admin_dashboard');
                    break;
                    
                case 'professeur':
                    // On n'a pas encore créé cette page, mais on prépare le terrain
                    header('Location: index.php?page=prof_dashboard'); 
                    break;
                    
                case 'etudiant':
                default:
                    // Par défaut, on envoie vers l'espace étudiant
                    header('Location: index.php?page=dashboard');
                    break;
            }
            
            exit();

        } else {
            // --- ÉCHEC ---
            // Sécurité : On affiche un message flou. 
            // Ne dites PAS "Mot de passe faux", dites "Identifiants incorrects".
            // Cela empêche les pirates de savoir si l'email existe ou non.
            $errors['login'] = "Email ou mot de passe incorrect.";
        }
    }

    // 5. Gestion des erreurs
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old_input_login'] = ['email' => $email_posted]; // Pour remettre l'email
        header('Location: index.php?page=login');
        exit();
    }
}