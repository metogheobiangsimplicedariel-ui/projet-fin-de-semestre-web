<?php
// 1. Définition des variables de configuration
$db_host = 'localhost';
$db_name = 'gestion_notes';      // Remplacez par le nom de votre BDD
$db_user = 'root';
$db_pass = 'root';      // Sur Windows (XAMPP/WAMP), c'est souvent vide : ''

try {
    // 2. Création de la chaîne de connexion (DSN)
    // On insère les variables $db_host et $db_name directement dans la chaîne
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

    // 3. Connexion à la base de données
    $pdo = new PDO($dsn, $db_user, $db_pass);

    // 4. Configuration des options PDO (comme dans votre image)
    // Active les erreurs (Exceptions) pour voir les problèmes SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Récupère les données sous forme d'objets ($user->email) plutôt que tableaux
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

} catch (PDOException $e) {
    // En cas d'erreur, on arrête le script et on affiche le message
    die("Erreur de connexion : " . $e->getMessage());
}
?>