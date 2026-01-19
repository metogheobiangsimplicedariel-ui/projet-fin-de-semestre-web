-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 19 jan. 2026 à 03:26
-- Version du serveur : 8.0.44
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestion_notes`
--

-- --------------------------------------------------------

--
-- Structure de la table `affectations_profs`
--

CREATE DATABASE IF NOT EXISTS `gestion_notes` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `gestion_notes`;

CREATE TABLE IF NOT EXISTS `affectations_profs` (
  `id` int NOT NULL,
  `professeur_id` int NOT NULL,
  `matiere_id` int NOT NULL,
  `periode_id` int NOT NULL,
  `groupe` varchar(50) DEFAULT NULL,
  `date_affectation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `affectations_profs`
--

INSERT INTO `affectations_profs` (`id`, `professeur_id`, `matiere_id`, `periode_id`, `groupe`, `date_affectation`) VALUES
(6, 18, 2, 2, 'Tous', '2026-01-06 20:14:56'),
(8, 16, 2, 2, 'Tous', '2026-01-06 20:14:56'),
(10, 18, 1, 2, 'Tous', '2026-01-06 20:14:56'),
(11, 17, 3, 2, 'Tous', '2026-01-06 20:14:56'),
(12, 18, 3, 2, 'Tous', '2026-01-06 20:14:56'),
(13, 18, 2, 4, 'CM', '2026-01-16 23:32:51'),
(14, 17, 1, 5, 'CM3', '2026-01-16 23:45:06');

-- --------------------------------------------------------

--
-- Structure de la table `configuration_colonnes`
--

CREATE TABLE IF NOT EXISTS `configuration_colonnes` (
  `id` int NOT NULL,
  `matiere_id` int NOT NULL,
  `periode_id` int NOT NULL,
  `nom_colonne` varchar(50) NOT NULL,
  `code_colonne` varchar(20) NOT NULL,
  `type` enum('note','bonus','malus','info') DEFAULT 'note',
  `note_max` decimal(5,2) DEFAULT '20.00',
  `coefficient` decimal(3,1) DEFAULT '1.0',
  `ponderation` decimal(5,2) DEFAULT '100.00',
  `obligatoire` tinyint(1) DEFAULT '1',
  `ordre` int NOT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `configuration_colonnes`
--

INSERT INTO `configuration_colonnes` (`id`, `matiere_id`, `periode_id`, `nom_colonne`, `code_colonne`, `type`, `note_max`, `coefficient`, `ponderation`, `obligatoire`, `ordre`, `date_creation`) VALUES
(4, 1, 2, 'Controle mi-semestre', 'CMS-1', 'note', 20.00, 0.3, 30.00, 1, 1, '2026-01-06 20:41:56'),
(5, 1, 2, 'devoir surveillé', 'DS5', 'note', 10.00, 0.2, 20.00, 1, 2, '2026-01-06 21:31:51'),
(8, 2, 4, 'Controle mi-semestre', 'CMS-7', 'note', 20.00, 0.4, 40.00, 1, 1, '2026-01-06 23:23:45'),
(9, 2, 4, 'devoir surveillé', 'DS2', 'note', 20.00, 0.2, 20.00, 1, 2, '2026-01-06 23:23:45'),
(10, 2, 2, 'Projet de mi-semestre', 'PMI-2', 'note', 20.00, 1.0, 100.00, 1, 1, '2026-01-06 23:33:08'),
(11, 1, 4, 'Controle mi-semestre', 'CMS-7', 'note', 20.00, 0.4, 100.00, 1, 1, '2026-01-16 22:52:12'),
(12, 1, 4, 'devoir surveillé', 'DS2', 'note', 20.00, 0.2, 100.00, 1, 2, '2026-01-16 22:52:12'),
(14, 1, 2, 'Projet Final', 'PF5', 'note', 20.00, 1.0, 100.00, 1, 3, '2026-01-16 23:38:08'),
(15, 1, 5, 'Projet de mi-semestre', 'PM3-SP', 'note', 20.00, 1.0, 100.00, 1, 1, '2026-01-16 23:44:45'),
(17, 2, 2, 'devoir surveillé', 'DS5', 'note', 10.00, 1.0, 100.00, 0, 2, '2026-01-18 19:57:20'),
(18, 2, 2, 'Projet Final', 'PF5', 'note', 20.00, 1.0, 100.00, 1, 3, '2026-01-18 19:57:20');

-- --------------------------------------------------------

--
-- Structure de la table `filieres`
--

CREATE TABLE IF NOT EXISTS `filieres` (
  `id` int NOT NULL,
  `code` varchar(20) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `niveau` varchar(20) DEFAULT NULL,
  `responsable_id` int DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `filieres`
--

INSERT INTO `filieres` (`id`, `code`, `nom`, `niveau`, `responsable_id`, `actif`, `date_creation`) VALUES
(1, 'L1-INFO', 'Licence 1 Informatique', 'Licence', NULL, 1, '2026-01-06 20:14:56'),
(2, 'L2-INFO', 'Licence 2 Informatique', 'Licence', NULL, 1, '2026-01-06 20:14:56'),
(3, 'L3-INFO', 'Licence 3 Informatique', 'Licence', NULL, 1, '2026-01-06 20:14:56'),
(4, 'M1-DEV', 'Master 1 Développement', 'Master', NULL, 1, '2026-01-06 20:14:56');

-- --------------------------------------------------------

--
-- Structure de la table `formules`
--

CREATE TABLE IF NOT EXISTS `formules` (
  `id` int NOT NULL,
  `matiere_id` int NOT NULL,
  `periode_id` int NOT NULL,
  `formule` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `formules`
--

INSERT INTO `formules` (`id`, `matiere_id`, `periode_id`, `formule`, `description`, `actif`, `date_creation`, `date_modification`) VALUES
(1, 1, 4, 'MOYENNE( CMS-7,DS2 )', '', 1, '2026-01-16 22:55:00', '2026-01-16 22:55:00'),
(2, 2, 2, 'MOYENNE(Note1, Note2)', '', 1, '2026-01-18 19:56:39', '2026-01-18 19:56:39');

-- --------------------------------------------------------

--
-- Structure de la table `historique_notes`
--

CREATE TABLE IF NOT EXISTS `historique_notes` (
  `id` int NOT NULL,
  `note_id` int NOT NULL,
  `ancienne_valeur` decimal(5,2) DEFAULT NULL,
  `nouvelle_valeur` decimal(5,2) DEFAULT NULL,
  `ancien_statut` varchar(20) DEFAULT NULL,
  `nouveau_statut` varchar(20) DEFAULT NULL,
  `modifie_par` int NOT NULL,
  `motif` text,
  `adresse_ip` varchar(45) DEFAULT NULL,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions_matieres`
--

CREATE TABLE IF NOT EXISTS `inscriptions_matieres` (
  `id` int NOT NULL,
  `etudiant_id` int NOT NULL,
  `matiere_id` int NOT NULL,
  `periode_id` int NOT NULL,
  `groupe` varchar(50) DEFAULT NULL,
  `dispense` tinyint(1) DEFAULT '0',
  `date_inscription` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `inscriptions_matieres`
--

INSERT INTO `inscriptions_matieres` (`id`, `etudiant_id`, `matiere_id`, `periode_id`, `groupe`, `dispense`, `date_inscription`) VALUES
(2, 10, 1, 2, 'TD1', 0, '2026-01-06 21:03:00'),
(3, 29, 1, 2, 'TD1', 0, '2026-01-06 21:03:00'),
(4, 27, 1, 2, 'TD1', 0, '2026-01-06 21:03:00'),
(5, 24, 1, 2, 'TD1', 0, '2026-01-06 21:03:00'),
(6, 8, 1, 2, 'TD1', 0, '2026-01-06 21:33:20'),
(9, 8, 2, 2, 'TD1', 0, '2026-01-06 21:55:14'),
(10, 30, 2, 2, 'TD1', 0, '2026-01-06 21:55:14'),
(11, 21, 2, 2, 'TD1', 0, '2026-01-06 21:55:14'),
(12, 29, 2, 2, 'TD1', 0, '2026-01-06 21:55:14'),
(18, 21, 3, 2, 'TD1', 0, '2026-01-06 23:36:10'),
(19, 10, 3, 2, 'TD1', 0, '2026-01-06 23:36:10'),
(20, 29, 3, 2, 'TD1', 0, '2026-01-06 23:36:10'),
(21, 22, 3, 2, 'TD1', 0, '2026-01-06 23:36:10'),
(22, 23, 3, 2, 'TD1', 0, '2026-01-06 23:36:10'),
(23, 30, 2, 4, 'CM', 0, '2026-01-16 23:33:10'),
(24, 21, 2, 4, 'CM', 0, '2026-01-16 23:33:10'),
(25, 8, 2, 4, 'CM', 0, '2026-01-16 23:33:10'),
(26, 10, 2, 4, 'CM', 0, '2026-01-16 23:33:10'),
(27, 29, 2, 4, 'CM', 0, '2026-01-16 23:33:10'),
(28, 20, 1, 5, 'CM3', 0, '2026-01-16 23:45:25'),
(29, 22, 1, 5, 'CM3', 0, '2026-01-16 23:45:25'),
(30, 23, 1, 5, 'CM3', 0, '2026-01-16 23:45:25'),
(31, 28, 1, 5, 'CM3', 0, '2026-01-16 23:45:25');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE IF NOT EXISTS `matieres` (
  `id` int NOT NULL,
  `code` varchar(20) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `filiere_id` int NOT NULL,
  `coefficient` decimal(3,1) DEFAULT '1.0',
  `credits` int DEFAULT NULL,
  `seuil_validation` decimal(4,2) DEFAULT '10.00',
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `code`, `nom`, `filiere_id`, `coefficient`, `credits`, `seuil_validation`, `actif`, `date_creation`) VALUES
(1, 'ALGO1', 'Algorithme avancé', 1, 1.5, 3, 10.00, 1, '2026-01-06 20:18:25'),
(2, 'AR', 'Réseaux avancés', 2, 1.0, 4, 10.00, 1, '2026-01-06 21:54:52'),
(3, 'OS2', 'Linux', 3, 1.0, 1, 12.00, 1, '2026-01-06 23:35:32'),
(4, 'SE01', 'Système Embarqué', 1, 1.0, 1, 12.00, 1, '2026-01-16 23:39:50');

-- --------------------------------------------------------

--
-- Structure de la table `moyennes`
--

CREATE TABLE IF NOT EXISTS `moyennes` (
  `id` int NOT NULL,
  `etudiant_id` int NOT NULL,
  `matiere_id` int NOT NULL,
  `periode_id` int NOT NULL,
  `moyenne` decimal(5,2) DEFAULT NULL,
  `note_sur` decimal(5,2) DEFAULT '20.00',
  `rang` int DEFAULT NULL,
  `decision` enum('valide','non_valide','rattrapage','en_attente') DEFAULT 'en_attente',
  `credits_obtenus` int DEFAULT NULL,
  `date_calcul` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `moyennes`
--

INSERT INTO `moyennes` (`id`, `etudiant_id`, `matiere_id`, `periode_id`, `moyenne`, `note_sur`, `rang`, `decision`, `credits_obtenus`, `date_calcul`) VALUES
(1, 8, 1, 2, 15.47, 20.00, NULL, 'en_attente', NULL, '2026-01-18 22:26:27'),
(5, 20, 1, 5, 16.00, 20.00, NULL, 'en_attente', NULL, '2026-01-18 20:12:31'),
(14, 30, 2, 2, 15.00, 20.00, NULL, 'en_attente', NULL, '2026-01-18 22:26:38'),
(15, 10, 1, 2, 11.93, 20.00, NULL, 'valide', NULL, '2026-01-18 22:26:27'),
(22, 21, 2, 2, 16.67, 20.00, NULL, 'en_attente', NULL, '2026-01-18 22:26:38'),
(23, 8, 2, 2, 15.25, 20.00, NULL, 'en_attente', NULL, '2026-01-18 22:26:38');

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE IF NOT EXISTS `notes` (
  `id` int NOT NULL,
  `etudiant_id` int NOT NULL,
  `colonne_id` int NOT NULL,
  `valeur` decimal(5,2) DEFAULT NULL,
  `note_normalisee` decimal(5,2) DEFAULT NULL,
  `statut` enum('saisie','absent','dispense','defaillant') DEFAULT 'saisie',
  `saisie_par` int NOT NULL,
  `date_saisie` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modification` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `etudiant_id`, `colonne_id`, `valeur`, `note_normalisee`, `statut`, `saisie_par`, `date_saisie`, `date_modification`) VALUES
(1, 8, 4, 10.00, NULL, 'saisie', 18, '2026-01-18 19:22:13', '2026-01-18 19:22:13'),
(2, 8, 11, 15.00, NULL, 'saisie', 18, '2026-01-18 19:22:16', '2026-01-18 19:22:16'),
(3, 8, 15, 16.00, NULL, 'saisie', 18, '2026-01-18 19:22:21', '2026-01-18 19:22:21'),
(4, 8, 5, 11.00, NULL, 'saisie', 18, '2026-01-18 19:22:23', '2026-01-18 19:22:23'),
(5, 8, 14, 18.00, NULL, 'saisie', 18, '2026-01-18 19:22:25', '2026-01-18 19:22:25'),
(6, 8, 12, 19.75, NULL, 'saisie', 18, '2026-01-18 19:22:37', '2026-01-18 19:22:37'),
(7, 20, 4, 11.00, NULL, 'saisie', 18, '2026-01-18 19:38:27', '2026-01-18 19:38:27'),
(8, 20, 11, 18.00, NULL, 'saisie', 18, '2026-01-18 19:29:16', '2026-01-18 19:29:16'),
(9, 20, 15, 16.00, NULL, 'saisie', 18, '2026-01-18 19:29:19', '2026-01-18 19:29:19'),
(10, 20, 5, 15.00, NULL, 'saisie', 18, '2026-01-18 19:38:25', '2026-01-18 19:38:25'),
(11, 20, 12, 11.00, NULL, 'saisie', 18, '2026-01-18 19:29:25', '2026-01-18 19:29:25'),
(12, 20, 14, 18.00, NULL, 'saisie', 18, '2026-01-18 19:38:21', '2026-01-18 19:38:21'),
(13, 30, 10, 20.00, NULL, 'saisie', 16, '2026-01-18 19:54:21', '2026-01-18 19:54:21'),
(14, 21, 10, 16.00, NULL, 'saisie', 18, '2026-01-18 20:52:57', '2026-01-18 20:52:57'),
(15, 30, 17, 15.00, NULL, 'saisie', 16, '2026-01-18 20:08:09', '2026-01-18 20:08:09'),
(16, 30, 18, 10.00, NULL, 'saisie', 16, '2026-01-18 19:58:41', '2026-01-18 19:58:41'),
(17, 10, 4, 15.00, NULL, 'saisie', 18, '2026-01-18 20:37:24', '2026-01-18 20:37:24'),
(18, 10, 5, 12.00, NULL, 'saisie', 18, '2026-01-18 20:37:27', '2026-01-18 20:37:27'),
(19, 10, 14, 11.00, NULL, 'saisie', 18, '2026-01-18 20:37:29', '2026-01-18 20:37:29'),
(20, 21, 17, 17.00, NULL, 'saisie', 18, '2026-01-18 20:54:18', '2026-01-18 20:54:18'),
(21, 21, 18, 17.00, NULL, 'saisie', 18, '2026-01-18 20:55:38', '2026-01-18 20:55:38'),
(22, 8, 10, 20.00, NULL, 'saisie', 18, '2026-01-18 20:57:11', '2026-01-18 20:57:11'),
(23, 8, 17, 11.00, NULL, 'saisie', 18, '2026-01-18 20:57:13', '2026-01-18 20:57:13'),
(24, 8, 18, 14.75, NULL, 'saisie', 18, '2026-01-18 20:57:16', '2026-01-18 20:57:16');

-- --------------------------------------------------------

--
-- Structure de la table `periodes`
--

CREATE TABLE IF NOT EXISTS `periodes` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `annee_universitaire` varchar(9) NOT NULL,
  `type` enum('semestre','trimestre','session','rattrapage') NOT NULL,
  `date_debut_saisie` datetime NOT NULL,
  `date_fin_saisie` datetime NOT NULL,
  `statut` enum('a_venir','ouverte','fermee','publiee') DEFAULT 'a_venir',
  `date_publication` datetime DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `justification_ouverture` text,
  `resultats_publies` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `periodes`
--

INSERT INTO `periodes` (`id`, `nom`, `code`, `annee_universitaire`, `type`, `date_debut_saisie`, `date_fin_saisie`, `statut`, `date_publication`, `actif`, `date_creation`, `justification_ouverture`, `resultats_publies`) VALUES
(2, 'Semestre 2 - Session principale', 'S2-2026', '2025-2026', 'semestre', '2026-01-01 08:30:00', '2026-06-30 00:00:00', 'publiee', '2026-01-07 00:10:11', 1, '2026-01-06 18:41:16', NULL, 1),
(4, 'Semestre 2 - Session rattrapage', 'S2R-2026', '2025-2026', 'rattrapage', '2026-01-19 08:30:00', '2026-01-25 20:00:00', 'fermee', '2026-01-07 00:29:14', 1, '2026-01-06 21:30:46', NULL, 0),
(5, 'Semestre 3 - Session principale', 'S3-2026', '2025-2026', 'semestre', '2026-01-05 08:30:00', '2026-07-31 18:00:00', 'ouverte', NULL, 1, '2026-01-06 23:34:46', 'NULLE PART', 0);

-- --------------------------------------------------------

--
-- Structure de la table `progression_saisie`
--

CREATE TABLE IF NOT EXISTS `progression_saisie` (
  `id` int NOT NULL,
  `matiere_id` int NOT NULL,
  `periode_id` int NOT NULL,
  `professeur_id` int NOT NULL,
  `total_etudiants` int NOT NULL,
  `total_notes_attendues` int NOT NULL,
  `notes_saisies` int DEFAULT '0',
  `pourcentage` decimal(5,2) DEFAULT '0.00',
  `valide_par_prof` tinyint(1) DEFAULT '0',
  `date_validation` datetime DEFAULT NULL,
  `date_mise_a_jour` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `progression_saisie`
--

INSERT INTO `progression_saisie` (`id`, `matiere_id`, `periode_id`, `professeur_id`, `total_etudiants`, `total_notes_attendues`, `notes_saisies`, `pourcentage`, `valide_par_prof`, `date_validation`, `date_mise_a_jour`) VALUES
(1, 1, 2, 18, 5, 15, 9, 60.00, 0, NULL, '2026-01-18 20:37:29'),
(2, 1, 5, 18, 4, 4, 2, 50.00, 0, NULL, '2026-01-18 19:29:19'),
(3, 2, 2, 18, 4, 12, 9, 75.00, 0, NULL, '2026-01-18 20:57:16');

-- --------------------------------------------------------

--
-- Structure de la table `templates_formules`
--

CREATE TABLE IF NOT EXISTS `templates_formules` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text,
  `colonnes_requises` json NOT NULL,
  `formule` text NOT NULL,
  `categorie` varchar(50) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `templates_formules`
--

INSERT INTO `templates_formules` (`id`, `nom`, `description`, `colonnes_requises`, `formule`, `categorie`, `actif`, `date_creation`) VALUES
(1, 'Moyenne simple', 'Moyenne arithmétique de toutes les notes', '[\"Note1\", \"Note2\"]', 'MOYENNE(Note1, Note2)', 'Standard', 1, '2026-01-03 23:28:24'),
(2, 'DS + Examen', 'DS coefficient 1, Examen coefficient 2', '[\"DS\", \"Examen\"]', '(DS + Examen * 2) / 3', 'Standard', 1, '2026-01-03 23:28:24'),
(3, 'Meilleure des deux', 'Garde la meilleure note entre deux évaluations', '[\"Note1\", \"Note2\"]', 'MAX(Note1, Note2)', 'Spécial', 1, '2026-01-03 23:28:24'),
(4, 'TP + Projet + Examen', 'Moyenne TP 30%, Projet 30%, Examen 40%', '[\"TP\", \"Projet\", \"Examen\"]', 'TP * 0.3 + Projet * 0.3 + Examen * 0.4', 'Standard', 1, '2026-01-03 23:28:24');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('admin','professeur','etudiant','scolarite') NOT NULL,
  `numero_etudiant` varchar(20) DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT '1',
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `numero_etudiant`, `date_naissance`, `telephone`, `actif`, `derniere_connexion`, `date_creation`) VALUES
(8, 'Jackson', 'David', 'darkeljacksons@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$RmhlTGZGZFBJNkprUG9jbg$zqY6VgRjguoadfIpxbuaHRzle1GkvU+KmZ7vt+cc1Q0', 'etudiant', 'ETU001', '2000-05-15', '+33612345678', 1, NULL, '2026-01-06 08:11:47'),
(10, 'Lomani', 'Alice', 'gamesjacsons@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$bnhtZTFoN3o5bUNqVUlVMg$Lbc30i1TuBppUQuERLAUGDKm6LIQ/TPEkRib/c+nBCE', 'etudiant', 'ETU002', '2001-03-22', '+33623456789', 1, NULL, '2026-01-06 09:04:39'),
(11, 'Laurance', 'Mark', 'admin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$RFZnd09jTUJuOTJmZnVXWA$NtICLWhoNoLcc94/EBQWHylxYUJSaGeeOI2leFS08pQ', 'admin', NULL, NULL, NULL, 1, NULL, '2026-01-06 11:47:50'),
(13, 'Dubois', 'Marc', 'admin@studify.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'admin', NULL, NULL, NULL, 1, NULL, '2025-01-01 07:00:00'),
(14, 'Lefebvre', 'Claire', 'claire.admin@studify.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'admin', NULL, NULL, NULL, 1, NULL, '2025-01-02 08:30:00'),
(15, 'Moreau', 'Jean', 'jean.moreau@studify.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'professeur', NULL, NULL, NULL, 1, NULL, '2025-08-20 13:00:00'),
(16, 'Girard', 'Isabelle', 'isa.girard@studify.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'professeur', NULL, NULL, NULL, 1, NULL, '2025-08-21 09:15:00'),
(17, 'Bernard', 'Thomas', 'thomas.b@studify.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'professeur', NULL, NULL, NULL, 1, NULL, '2025-08-22 10:00:00'),
(18, 'Leroy', 'Sophie', 'merlin@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'professeur', NULL, NULL, NULL, 1, NULL, '2025-08-23 08:45:00'),
(19, 'Roux', 'Patrick', 'p.roux@studify.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'professeur', NULL, NULL, NULL, 1, NULL, '2025-08-25 15:20:00'),
(20, 'Petit', 'Lucas', 'lucas.petit@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU003', '2002-07-10', NULL, 1, NULL, '2025-09-05 07:00:00'),
(21, 'Durand', 'Emma', 'emma.durand@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU004', '2001-11-30', NULL, 1, NULL, '2025-09-05 07:05:00'),
(22, 'Richard', 'Léo', 'leo.richard@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU005', '2000-12-25', NULL, 1, NULL, '2025-09-05 07:10:00'),
(23, 'Simon', 'Chloé', 'chloe.simon@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU006', '2002-02-14', NULL, 1, NULL, '2025-09-05 07:15:00'),
(24, 'Michel', 'Louis', 'louis.michel@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU007', '2001-09-08', NULL, 1, NULL, '2025-09-05 07:20:00'),
(25, 'Perrin', 'Eva', 'eva.perrin@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU008', '2002-04-18', NULL, 1, NULL, '2026-01-02 09:15:00'),
(26, 'Morin', 'Liam', 'liam.morin@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU009', '2001-06-05', NULL, 1, NULL, '2026-01-03 10:30:00'),
(27, 'Mathieu', 'Louise', 'louise.mathieu@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU010', '2000-08-12', NULL, 1, NULL, '2026-01-04 13:00:00'),
(28, 'Vincent', 'Ethan', 'ethan.vincent@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU011', '2002-01-20', NULL, 1, NULL, '2026-01-05 08:45:00'),
(29, 'Masson', 'Anna', 'anna.masson@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU012', '2001-10-03', NULL, 1, NULL, '2026-01-05 15:20:00'),
(30, 'Dumont', 'Tom', 'tom.dumont@email.com', '$argon2id$v=19$m=65536,t=4,p=1$OWJRekpPOXY5R2tEcDF0Vg$tcYyKBR2RaEn9UpyrUkCv24o8k4gno2E5ZOFv2mtVL4', 'etudiant', 'ETU013', '2002-03-17', NULL, 1, NULL, '2026-01-06 09:00:00'),
(31, 'Stark', 'Tony', 'prof@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$WEJNclBHNEVNN05iRlBmTg$hi8qt3Pm86xzBXtgVoiPZPLqUA0OUUSw9Hoee8koUWM', 'professeur', NULL, NULL, NULL, 1, NULL, '2026-01-06 22:56:14'),
(32, 'Tiomo', 'Pierre', 'prof2@gmail.com', '$2y$10$I59ZF1FfqjKyoMnQbOSdK.nFvyjI/vhim5BcxZAjK1KlxvftHJ/c.', 'professeur', NULL, NULL, NULL, 1, NULL, '2026-01-06 23:22:37'),
(33, 'Takes', 'Dom', 'dom@gmail.com', '$2y$10$apeBFqdyy7wmNgC9C3TCvu6PACGuRUXdy40gEOwVITfj8PGZn8E/O', 'etudiant', NULL, NULL, NULL, 1, NULL, '2026-01-18 21:03:43');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `affectations_profs`
--
ALTER TABLE `affectations_profs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_affectation` (`professeur_id`,`matiere_id`,`periode_id`,`groupe`),
  ADD KEY `matiere_id` (`matiere_id`),
  ADD KEY `periode_id` (`periode_id`);

--
-- Index pour la table `configuration_colonnes`
--
ALTER TABLE `configuration_colonnes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_colonne` (`matiere_id`,`periode_id`,`code_colonne`),
  ADD KEY `periode_id` (`periode_id`),
  ADD KEY `idx_config_matiere_periode` (`matiere_id`,`periode_id`);

--
-- Index pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `responsable_id` (`responsable_id`);

--
-- Index pour la table `formules`
--
ALTER TABLE `formules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_formule` (`matiere_id`,`periode_id`),
  ADD KEY `periode_id` (`periode_id`);

--
-- Index pour la table `historique_notes`
--
ALTER TABLE `historique_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `note_id` (`note_id`),
  ADD KEY `modifie_par` (`modifie_par`),
  ADD KEY `idx_date_modification` (`date_modification`);

--
-- Index pour la table `inscriptions_matieres`
--
ALTER TABLE `inscriptions_matieres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscription` (`etudiant_id`,`matiere_id`,`periode_id`),
  ADD KEY `matiere_id` (`matiere_id`),
  ADD KEY `periode_id` (`periode_id`),
  ADD KEY `idx_inscription_etudiant` (`etudiant_id`);

--
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `filiere_id` (`filiere_id`),
  ADD KEY `idx_matiere_actif` (`actif`);

--
-- Index pour la table `moyennes`
--
ALTER TABLE `moyennes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_moyenne` (`etudiant_id`,`matiere_id`,`periode_id`),
  ADD KEY `matiere_id` (`matiere_id`),
  ADD KEY `idx_moyennes_periode` (`periode_id`),
  ADD KEY `idx_moyennes_etudiant` (`etudiant_id`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_note` (`etudiant_id`,`colonne_id`),
  ADD KEY `saisi_par` (`saisie_par`),
  ADD KEY `idx_notes_etudiant` (`etudiant_id`),
  ADD KEY `idx_notes_colonne` (`colonne_id`),
  ADD KEY `idx_notes_statut` (`statut`);

--
-- Index pour la table `periodes`
--
ALTER TABLE `periodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `idx_periode_statut` (`statut`),
  ADD KEY `idx_periode_actif` (`actif`);

--
-- Index pour la table `progression_saisie`
--
ALTER TABLE `progression_saisie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_progression` (`matiere_id`,`periode_id`),
  ADD KEY `periode_id` (`periode_id`),
  ADD KEY `professeur_id` (`professeur_id`);

--
-- Index pour la table `templates_formules`
--
ALTER TABLE `templates_formules`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `numero_etudiant` (`numero_etudiant`),
  ADD KEY `idx_utilisateur_role` (`role`),
  ADD KEY `idx_utilisateur_actif` (`actif`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `affectations_profs`
--
ALTER TABLE `affectations_profs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `configuration_colonnes`
--
ALTER TABLE `configuration_colonnes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `filieres`
--
ALTER TABLE `filieres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `formules`
--
ALTER TABLE `formules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `historique_notes`
--
ALTER TABLE `historique_notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `inscriptions_matieres`
--
ALTER TABLE `inscriptions_matieres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `moyennes`
--
ALTER TABLE `moyennes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `periodes`
--
ALTER TABLE `periodes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `progression_saisie`
--
ALTER TABLE `progression_saisie`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `templates_formules`
--
ALTER TABLE `templates_formules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affectations_profs`
--
ALTER TABLE `affectations_profs`
  ADD CONSTRAINT `affectations_profs_ibfk_1` FOREIGN KEY (`professeur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `affectations_profs_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `affectations_profs_ibfk_3` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `configuration_colonnes`
--
ALTER TABLE `configuration_colonnes`
  ADD CONSTRAINT `configuration_colonnes_ibfk_1` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `configuration_colonnes_ibfk_2` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD CONSTRAINT `filieres_ibfk_1` FOREIGN KEY (`responsable_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `formules`
--
ALTER TABLE `formules`
  ADD CONSTRAINT `formules_ibfk_1` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `formules_ibfk_2` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `historique_notes`
--
ALTER TABLE `historique_notes`
  ADD CONSTRAINT `historique_notes_ibfk_1` FOREIGN KEY (`note_id`) REFERENCES `notes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historique_notes_ibfk_2` FOREIGN KEY (`modifie_par`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscriptions_matieres`
--
ALTER TABLE `inscriptions_matieres`
  ADD CONSTRAINT `inscriptions_matieres_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_matieres_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_matieres_ibfk_3` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD CONSTRAINT `matieres_ibfk_1` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `moyennes`
--
ALTER TABLE `moyennes`
  ADD CONSTRAINT `moyennes_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `moyennes_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `moyennes_ibfk_3` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`colonne_id`) REFERENCES `configuration_colonnes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notes_ibfk_3` FOREIGN KEY (`saisie_par`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `progression_saisie`
--
ALTER TABLE `progression_saisie`
  ADD CONSTRAINT `progression_saisie_ibfk_1` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `progression_saisie_ibfk_2` FOREIGN KEY (`periode_id`) REFERENCES `periodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `progression_saisie_ibfk_3` FOREIGN KEY (`professeur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
