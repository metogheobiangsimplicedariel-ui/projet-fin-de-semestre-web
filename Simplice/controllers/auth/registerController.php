<?php
// controllers/auth/registerController.php

if(!empty($_POST)){

    // 1. Connexion à la BDD
    // On utilise __DIR__ pour être sûr de trouver le fichier peu importe où on est
    require_once __DIR__ . '/../../config/database.php';

    $errors = array();
    // Récupération des données du formulaire
    $user_name = $_POST['fullname']; 
    $user_email = $_POST['email'];
    $user_passwd = $_POST['passwd'];
    $user_confirmed_passwd = $_POST['confirm_passwd']; // Attention : name="confirm_passwd" dans le HTML précédent

    // --- VOS VÉRIFICATIONS ---
    
    // a) Séparation du nom complet (Alice Merveille -> Prénom: Alice, Nom: Merveille)
    $parts = explode(' ', $user_name, 2);
    $prenom = $parts[0];
    $nom = isset($parts[1]) ? $parts[1] : '';


    // 1. Vérif Nom
    if(empty($user_name) || !preg_match('/^[a-zA-Z0-9_ \-\p{L}]+$/u', $user_name) ){
        $errors['username'] = "Le nom contient des caractères invalides (seules les lettres, chiffres, tirets et espaces sont autorisés).";
    } 

    // 2. Vérif Email
    if(empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)){
        $errors['email'] = "Email invalide.";
    } else {
       
        $check = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = :email");
    
        $check->execute([':email' => $user_email]);
        
        if($check->fetch()){
            $errors['email'] = "Cet email est déjà utilisé sur un autre compte.";
        }
    }

    // 3. Vérif Mot de passe (Simple correspondance)
    if (empty($user_passwd) || $user_passwd !== $user_confirmed_passwd) {
        $errors['passwd'] = "Les mots de passe ne correspondent pas.";
    }
    

    // --- DÉCISION ---
    if(empty($errors)){
        // SUCCÈS : Aucune erreur, on insère en BDD
        

        try {
            // 2. Préparation des données
            
           

            // b) Sécurisation du mot de passe (Hachage)
            $hash = password_hash($user_passwd, PASSWORD_ARGON2ID);

            // 3. La Requête SQL
            // On utilise la syntaxe standard (colonnes) VALUES (valeurs)
            $req = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (:nom, :prenom, :email, :mdp, :role)");

            // 4. Exécution
            $req->execute([
                ':nom'    => $nom,
                ':prenom' => $prenom,
                ':email'  => $user_email,
                ':mdp'    => $hash,
                ':role'   => 'etudiant' // Valeur par défaut
            ]);

            

            // 5. Redirection vers le login
            $_SESSION['success'] = "Votre compte a été créé !"; 
            header('Location: index.php?page=login');
            exit();

        } catch (PDOException $e) {
            // Si la BDD plante (ex: email déjà pris non géré avant), on l'attrape ici
            $errors['system'] = "Erreur technique : " . $e->getMessage();
            // On laisse le script continuer vers le bloc 'else' pour afficher l'erreur
        }
    }

    // SI ON ARRIVE ICI, C'EST QU'IL Y A DES ERREURS (Soit validation, soit SQL catch)
    if (!empty($errors)) {
        // ÉCHEC : On sauvegarde les erreurs dans la SESSION
        $_SESSION['errors'] = $errors;
        
        // BONUS : On sauvegarde aussi ce que l'utilisateur a écrit pour ne pas qu'il retape tout
        $_SESSION['old_input'] = $_POST;

        // On redirige l'utilisateur vers la page d'inscription
        header('Location: index.php?page=register');
        exit();
    }
}