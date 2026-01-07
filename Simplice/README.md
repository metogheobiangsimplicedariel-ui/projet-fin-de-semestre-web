# Système de Gestion de Notes (SGN)

## Description

Ce projet est une plateforme de gestion scolaire permettant de gérer le cycle de vie des notes, depuis la configuration des semestres et des matières par l'administration, jusqu'à la saisie de type tableur par les professeurs.

L'architecture repose sur le modèle **MVC (Modèle-Vue-Contrôleur)** sans framework lourd, garantissant performance et clarté du code.

---

## Architecture du Projet

Le projet est organisé de manière modulaire pour séparer la logique métier (Controllers), l'interface utilisateur (Views) et la configuration.

### Structure des Dossiers
```
/project-root
│
├── config/              # Configuration globale (Base de données)
├── controllers/         # Logique métier (Le "Cerveau" de l'application)
│   ├── admin/           # Contrôleurs dédiés à l'Espace Administrateur
│   └── prof/            # Contrôleurs dédiés à l'Espace Professeur
│
├── views/               # Interface utilisateur (Fichiers HTML/PHP)
│   ├── layout/          # Éléments communs (Header, Footer)
│   ├── admin/           # Vues de l'administration
│   └── prof/            # Vues des professeurs
│
├── assets/              # Fichiers statiques
│   └── js/              # Scripts JavaScript (Logique front-end)
│
├── index.php            # Point d'entrée unique (Routeur)
└── database.sql         # Structure de la base de données
```

---

## Détail des Fichiers

### 1. Racine (/)

* **index.php** : **Le Routeur**. C'est le point d'entrée unique de l'application. Il intercepte toutes les requêtes, vérifie quel contrôleur appeler via le paramètre `?page=...` et charge le fichier correspondant.
* **database.sql** : Contient le script SQL complet pour créer les tables (`utilisateurs`, `matieres`, `notes`, etc.) et insérer les données de base.

### 2. Configuration (/config)

* **database.php** : Initialise la connexion à la base de données MySQL via **PDO**. Ce fichier est inclus au début de chaque contrôleur.

### 3. Contrôleurs (/controllers)

C'est ici que se trouve l'intelligence de l'application.

#### Espace Admin (/controllers/admin/)

* **dashboardController.php** : Gère la vue d'ensemble, calcule les statistiques (KPIs) et récupère les 5 dernières inscriptions.
* **usersListController.php** : Gère le CRUD (Création, Lecture, Mise à jour, Suppression) des utilisateurs (Profs, Étudiants, Admins). Gère la sécurité des suppressions (cascades).
* **periodsController.php** : Gère les périodes académiques (Semestres). Permet d'ouvrir/fermer/publier la saisie des notes.
* **subjectsController.php** : Gère le catalogue des matières, les coefficients, les crédits ECTS et le rattachement aux filières.
* **assignmentsController.php** : Gère l'affectation ("Qui enseigne quoi ?") et la constitution des groupes d'étudiants ("Qui étudie quoi ?").
* **configColumnsController.php** : Permet de définir la structure des notes pour une matière (ex: créer une colonne "DS1" sur 20, coeff 2). Gère l'ordre via Drag & Drop.

#### Espace Prof (/controllers/prof/)
* **dashboardController.php** : Affiche les matières dont le prof est responsable pour la période active. Détecte automatiquement la période courante.
* **gradesController.php** : Gère l'affichage de la grille de notation et le traitement **AJAX** pour la sauvegarde automatique des notes.

### 4. Vues (/views)

C'est ce que l'utilisateur voit à l'écran.

#### Layout (/views/layout/)
* **header.php** : Contient le `<head>`, les liens CSS (Tailwind, FontAwesome) et le début du `<body>`.
* **footer.php** : Contient la fermeture des balises et les scripts JS globaux.

#### Vues Admin (/views/admin/)
* **sidebar.php** : Le menu latéral de navigation (se met en surbrillance selon la page active).
* **dashboard.php** : La page d'accueil avec les statistiques et les actions rapides.
* **users_list.php** : Tableau des utilisateurs avec modales d'édition et de suppression.
* **periods/index.php** : Affichage des périodes sous forme de cartes avec boutons d'état (Ouvrir/Fermer).
* **periods/config_columns.php** : Interface complexe pour ajouter/modifier/trier les colonnes de notes par matière.
* **subjects/index.php** : Liste des matières et formulaires d'ajout.
* **assignments/index.php** : Interface Maître-Détail pour assigner profs et étudiants aux cours.

#### Vues Prof (/views/prof/)
* **sidebar.php** : Menu latéral simplifié spécifique aux professeurs.
* **dashboard.php** : Liste des cours assignés avec visualisation de la structure des notes (lecture seule).
* **grades.php** : **L'interface Tableur**. Contient la grille de saisie, la barre de progression et les indicateurs de sauvegarde.

### 5. Assets (/assets/js)

* **spreadsheet.js** : Le moteur de la saisie des notes. Il gère :
    * La navigation au clavier (Flèches, Entrée).
    * La validation des données (Max note, Codes ABS/DIS).
    * L'appel AJAX pour la sauvegarde automatique.

---

## Installation

1.  **Base de données** :
    * Créez une base de données nommée `gestion_notes`.
    * Importez le fichier `database.sql`.
2.  **Configuration** :
    * Ouvrez `config/database.php`.
    * Modifiez `$host`, `$dbname`, `$username`, `$password` selon votre serveur local (WAMP/XAMPP/MAMP).
3.  **Lancement** :
    * Placez le dossier dans votre serveur web (`htdocs` ou `www`).
    * Accédez via `http://localhost/votre-dossier/`.
    * Compte Admin par défaut (selon votre insertion SQL) : `admin@ecole.com` / `admin`.

---

## Technologies

* **Langage** : PHP 8+ (Vanilla, sans framework).
* **Base de données** : MySQL / MariaDB.
* **Front-end** : HTML5, TailwindCSS (via CDN).
* **Icônes** : FontAwesome 6.
* **Interactivité** : JavaScript (Vanilla) & SortableJS (Drag & Drop).

---

## Fonctionnalités Clés

* **Architecture MVC** propre et maintenable.
* **Saisie type Tableur** : Navigation fluide et sauvegarde temps réel.
* **Gestion des Périodes** : Contrôle strict des dates de saisie (Ouverture/Fermeture).
* **Configuration Dynamique** : Chaque matière peut avoir son propre schéma de notation (DS, TP, Projet...).
* **Sécurité** : Hachage des mots de passe, requêtes préparées (PDO), protection CSRF basique via POST.