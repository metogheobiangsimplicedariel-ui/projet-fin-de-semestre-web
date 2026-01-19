# RAPPORT DE PROJET : Plateforme de Gestion Académique (Studify)

## 1. Titre du Projet

**Conception et Développement d'une Application Web de Gestion de Scolarité et de Délibération**

## 2. Membres du Groupe

Ce projet a été réalisé par l'équipe suivante :

- **METOGHE OBIANG Simplice Dariel**
- **noura temssamani**
- **TIOMO Charly**
- **Nisrine Eddahak**

---

## 3. Objectifs du Travail

L'objectif principal était de développer une solution centralisée (**Studify**) pour digitaliser le processus de gestion des notes et des délibérations au sein d'un établissement universitaire. Le système répond aux besoins de trois acteurs clés :

- **L'Administration :** Superviser la structure académique, configurer les évaluations, gérer les utilisateurs et contrôler le cycle de vie des périodes (ouverture, délibération, publication).
- **Le Corps Enseignant :** Saisir les notes via une interface fluide et suivre la progression de leurs classes.
- **Les Étudiants :** Consulter leurs résultats en temps réel (sous condition de publication) et obtenir leurs documents officiels.

---

## 4. Spécifications Fonctionnelles et Techniques Implémentées

Le projet repose sur une architecture **MVC (Modèle-Vue-Contrôleur)** stricte, garantissant la séparation des données, de la logique métier et de l'affichage. Voici les fonctionnalités clés développées :

### A. Gestion Avancée des Périodes (Cycle de Délibération)

Nous avons implémenté un système de **double contrôle** pour gérer l'état d'une période (Semestre/Session) :

1.  **Statut de Saisie (`statut`) :**
    - `ouverte` : Les professeurs peuvent saisir et modifier les notes.
    - `verrouillee` (Fermée) : La saisie est bloquée pour le jury/délibération.
2.  **Visibilité Étudiant (`resultats_publies`) :**
    - `0` (Masqué) : Les notes sont invisibles pour les étudiants (même si saisies).
    - `1` (Publié) : Les étudiants accèdent à leurs bulletins définitifs.
    - _Implémentation technique :_ Le `ResultController` gère ces deux états indépendamment, permettant à l'administration de préparer les résultats en toute confidentialité avant la publication officielle.

### B. Système de Notation Dynamique

L'application ne fige pas les types de notes. L'administrateur peut configurer dynamiquement pour chaque matière :

- Le nombre de colonnes (DS, TP, Projet, Exam...).
- Le type de colonne (Note standard, Bonus, Malus, Info).
- Le coefficient et le barème (Note sur 20, 10, 100...).
- _Implémentation technique :_ Le `GradeController` génère la grille de saisie dynamiquement en s'appuyant sur le modèle `ColumnConfig`.

### C. Espace Étudiant Intelligent

Le tableau de bord étudiant a été conçu pour gérer les cas limites :

- **Récupération Contextuelle :** L'algorithme cherche en priorité la dernière période _publiée_ où l'étudiant est inscrit, évitant d'afficher une période future vide ou une période passée non pertinente.
- **Sécurité d'Affichage :** Si les résultats ne sont pas publiés, un écran de verrouillage ("Résultats non disponibles") masque les données sensibles.
- **Génération de PDF :** Intégration d'un bouton permettant de télécharger le bulletin officiel au format PDF via la librairie `Dompdf`, généré à la volée.

### D. Sécurité et Gestion des Utilisateurs

- Authentification sécurisée avec hachage des mots de passe (`password_hash` / `password_verify`).
- Protection contre le vol de session (`session_regenerate_id`).
- Système de rôles (Admin, Professeur, Étudiant) avec redirection automatique au login.

---

## 5. Technologies Utilisées

- **Langage Backend :** PHP 8 (Natif, Orienté Objet).
- **Base de Données :** MySQL (Relations : Utilisateurs, Notes, Matières, Périodes).
- **Frontend :**
  - **HTML5 / CSS3** pour la structure sémantique.
  - **Tailwind CSS** (via CDN) pour le design responsive et moderne.
  - **JavaScript (Vanilla)** pour les interactions dynamiques (modales, AJAX).
  - **SortableJS** pour le Drag & Drop des colonnes de notes.
- **Dépendances PHP :**
  - `dompdf/dompdf` : Génération des PV et Bulletins.
  - `Composer` : Gestionnaire de dépendances.
- **Serveur :** Apache (via XAMPP/WAMP).

---

## 6. Instructions pour Exécuter le Projet

Suivez ces étapes pour déployer l'application localement :

### Prérequis

- Avoir installé **XAMPP**, **WAMP** ou MAMP (PHP 8.0+ et MySQL requis).
- Avoir installé **Composer** sur votre machine (obligatoire pour générer le dossier `vendor`).

### Installation

1.  **Mise en place des fichiers :**
    - Copiez le dossier du projet (ex: `web/`) dans le répertoire racine de votre serveur (`htdocs` pour XAMPP ou `www` pour WAMP).

2.  **Base de Données :**
    - Lancez votre serveur MySQL et ouvrez **phpMyAdmin** (généralement `http://localhost/phpmyadmin`).
    - Créez une nouvelle base de données (ex: `studify_db`).
    - Importez le fichier SQL fourni (`database.sql`) pour créer les tables et les données de test.

3.  **Installation des dépendances (CRUCIAL) :**
    - Le dossier `vendor/` n'est pas inclus dans le code source. Il faut le générer.
    - Ouvrez un terminal (PowerShell ou Cmd).
    - Naviguez vers le dossier du projet : `cd C:\xampp\htdocs\web`
    - Lancez la commande suivante :
      ```bash
      composer require dompdf/dompdf
      ```
    - _Cela va créer le dossier `vendor` et le fichier `autoload.php`._

4.  **Configuration :**
    - Ouvrez le fichier `Config/Database.php`.
    - Vérifiez que les identifiants (DB_NAME, DB_USER, DB_PASS) correspondent à votre configuration locale.

### Lancement

1.  Ouvrez votre navigateur web.
2.  Accédez à l'URL : `http://localhost/web/index.php`.
3.  Connectez-vous avec les identifiants par défaut :
    - **Admin :** `admin@gmail.com` / `admin`
    - **Professeur :** `merlin@gmail.com` / `123456`
    - **Étudiant :** `gamesjacsons@gmail.com` / `azerty12`

---

## 7. Structure du Projet (Architecture MVC)

L'application suit une structure stricte pour séparer les responsabilités :

```text
/web
 ├── /Config
 │    └── Database.php       # Connexion à la BDD (Singleton PDO)
 ├── /controllers            # LOGIQUE MÉTIER
 │    ├── /admin             # Contrôleurs Admin (Dashboard, Users, Results...)
 │    ├── /auth              # Authentification (Login, Register, Logout)
 │    ├── /prof              # Contrôleurs Prof (Saisie des notes, Matières)
 │    └── /student           # Contrôleur Étudiant (Bulletin, PDF)
 ├── /models                 # ACCÈS AUX DONNÉES (CRUD)
 │    ├── User.php           # Gestion des utilisateurs
 │    ├── NoteModel.php      # Gestion des notes (Insert/Update)
 │    ├── Period.php         # Gestion des semestres
 │    ├── Result.php         # Calculs des délibérations
 │    └── ... (Subject, Assignment, Formula)
 ├── /vendor                 # Librairies externes (Dompdf) - Généré par Composer
 ├── /views                  # INTERFACE UTILISATEUR
 │    ├── /admin             # Pages Admin
 │    ├── /auth              # Pages Login/Register
 │    ├── /layout            # Header et Footer communs
 │    ├── /prof              # Pages Professeur
 │    └── dashboard.php      # Page d'accueil Étudiant
 └── index.php               # ROUTEUR PRINCIPAL (Point d'entrée unique)
```
