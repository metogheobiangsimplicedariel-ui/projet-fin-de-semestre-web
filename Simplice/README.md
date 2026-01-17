# **Plan de Projet Express : Application de Gestion des Notes**

## **1. CONTEXTE ET OBJECTIF**

**Durée :** 8 jours (4 au 12 Janvier 2026)  
**Équipe :** 4 développeurs  
**Objectif :** Développer un MVP fonctionnel d'un système de gestion des notes avec configuration dynamique.

### **MVP (Minimum Viable Product)**
- Admin configure des colonnes de notes
- Professeur saisit des notes dans un tableau dynamique
- Calcul automatique des moyennes
- Étudiant consulte ses notes
- Génération PDF basique

---

## **2. RÉPARTITION DES TÂCHES**

### **DÉVELOPPEUR 1 : ADMINISTRATION**
**Responsable :** Configuration système
```
Jour 1 : Authentification + Dashboard admin
Jour 2 : Gestion des périodes (CRUD)
Jour 3-4 : Configurateur colonnes (interface simple)
Jour 5 : Gestion matières + affectation profs
Jour 6-7 : Tests et intégration
```

### **DÉVELOPPEUR 2 : INTERFACE PROFESSEUR**
**Responsable :** Saisie des notes
```
Jour 1 : Page "Mes matières"
Jour 2-4 : Tableau de saisie dynamique (priorité absolue)
Jour 5 : Validation basique (0-20)
Jour 6-7 : Tests et intégration
```

### **DÉVELOPPEUR 3 : MOTEUR & BASE DE DONNÉES**
**Responsable :** Calculs et données
```
Jour 1 : Schéma base de données
Jour 2 : DAO principales
Jour 3-5 : Parser formules (addition/soustraction)
Jour 6 : Service calcul moyennes
Jour 7 : Tests
```

### **DÉVELOPPEUR 4 : VISUALISATION**
**Responsable :** Consultation et documents
```
Jour 1 : Interface étudiant
Jour 2-3 : Génération PDF basique
Jour 4 : Dashboard admin simple
Jour 5 : Notifications (interface)
Jour 6-7 : Tests et intégration
```

---

## **3. CALENDRIER DÉTAILLÉ**

### **JOUR 1 (4/01) - FONDATIONS**
**Matin (9h-12h) - Tous ensemble :**
- Setup environnement (XAMPP/WAMP)
- Structure projet MVC
- Git repository
- Conventions de code

**Après-midi (13h-18h) :**
- D1 : Login/Logout + sessions
- D2 : HTML/CSS interface prof
- D3 : Création tables MySQL
- D4 : HTML/CSS interface étudiant

### **JOUR 2 (5/01) - ENTITÉS DE BASE**
- D1 : CRUD périodes
- D2 : Récupération matières depuis BDD
- D3 : DAO notes + configuration
- D4 : Affichage notes étudiant

### **JOUR 3 (6/01) - CŒUR SYSTÈME**
- D1 : Formulaire configuration colonnes
- D2 : Génération tableau HTML dynamique
- D3 : Parser mathématique basique
- D4 : Template HTML pour PDF

### **JOUR 4 (7/01) - PREMIÈRE INTÉGRATION**
**Test du workflow complet :**
1. Admin crée période
2. Admin configure 2 colonnes
3. Prof saisit des notes
4. Étudiant voit les notes

### **JOUR 5 (8/01) - AMÉLIORATIONS**
- D1 : Affectation professeurs
- D2 : Validation saisie + erreurs
- D3 : Calcul automatique
- D4 : Statistiques dashboard

### **JOUR 6 (9/01) - INTÉGRATION FINALE**
**Test exhaustif :**
- Tous les cas d'utilisation MVP
- Validation données
- Performance basique

### **JOUR 7 (10/01) - FINALISATION**
- Correction bugs
- Optimisation
- Documentation
- Préparation présentation

### **JOUR 8 (11-12/01) - PRÉSENTATION**
- Démo finale
- Slides
- Livraison

---

## **4. FONCTIONNALITÉS PAR PRIORITÉ**

### **PRIORITÉ 1 : OBLIGATOIRE (MVP)**
1. Admin → Créer période
2. Admin → Configurer 3 colonnes max
3. Prof → Saisir notes dans tableau
4. Système → Calculer moyenne
5. Étudiant → Voir ses notes
6. Système → Générer PDF simple

### **PRIORITÉ 2 : SI TEMPS**
1. Import/Export Excel
2. Parser avec multiplication
3. Validation par professeur
4. Dashboard avancé

### **PRIORITÉ 3 : OPTIONNEL**
1. Drag & drop colonnes
2. Notifications email
3. Historique complet
4. Statistiques complexes

---

## **5. STACK TECHNIQUE**

### **Recommandation : PHP Natif**
```
Frontend : HTML5, CSS3, JavaScript Vanilla
Backend  : PHP 8.1+
Base de données : MySQL 8
PDF : TCPDF ou HTML → PDF
Serveur : Apache (XAMPP/WAMP)
```

### **Structure des fichiers :**
```
/main/
├── /config/          # Configuration
├── /controllers/     # Contrôleurs
├── /models/         # Modèles/DAO
├── /views/          # Vues
├── /assets/         # CSS/JS/Images
├── /lib/            # Bibliothèques (TCPDF)
└── index.php        # Point d'entrée
```

---

## **6. SCHEMA BDD MINIMAL**

### **Tables essentielles :**
```sql
CREATE DATABASE IF NOT EXISTS gestion_notes;
USE gestion_notes;

-- 2. LA TABLE MANQUANTE (Indispensable)
CREATE TABLE utilisateurs (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    nom                 VARCHAR(100) NOT NULL,
    prenom              VARCHAR(100) NOT NULL,
    email               VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe        VARCHAR(255) NOT NULL, -- Stocker le hash, pas le clair !
    role                ENUM('admin', 'professeur', 'etudiant', 'scolarite') NOT NULL,
    actif               BOOLEAN DEFAULT TRUE,
    date_creation       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- =============================================
-- TABLE DES PÉRIODES DE NOTATION
-- =============================================
CREATE TABLE periodes (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    nom                 VARCHAR(100) NOT NULL,
    code                VARCHAR(20) UNIQUE NOT NULL,  -- "S1-2024", "RAT-2025"
    annee_universitaire VARCHAR(9) NOT NULL,  -- "2024-2025"
    type                ENUM('semestre', 'trimestre', 'session', 'rattrapage') NOT NULL,
    date_debut_saisie   DATETIME NOT NULL,
    date_fin_saisie     DATETIME NOT NULL,
    statut              ENUM('a_venir', 'ouverte', 'fermee', 'publiee') DEFAULT 'a_venir',
    date_publication    DATETIME,
    date_creation       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLE DES FILIÈRES
-- =============================================
CREATE TABLE filieres (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    code                VARCHAR(20) UNIQUE NOT NULL,
    nom                 VARCHAR(150) NOT NULL,
    niveau              VARCHAR(20),  -- "Licence", "Master"
    responsable_id      INT,
    date_creation       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =============================================
-- TABLE DES MATIÈRES
-- =============================================
CREATE TABLE matieres (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    code                VARCHAR(20) UNIQUE NOT NULL,
    nom                 VARCHAR(150) NOT NULL,
    filiere_id          INT NOT NULL,
    coefficient         DECIMAL(3,1) DEFAULT 1,
    credits             INT,
    seuil_validation    DECIMAL(4,2) DEFAULT 10,
    date_creation       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (filiere_id) REFERENCES filieres(id)
);

-- =============================================
-- TABLE DES AFFECTATIONS PROFESSEURS-MATIÈRES
-- =============================================
CREATE TABLE affectations_profs (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    professeur_id       INT NOT NULL,
    matiere_id          INT NOT NULL,
    periode_id          INT NOT NULL,
    groupe              VARCHAR(50),  -- "Groupe A", "Tous"
    FOREIGN KEY (professeur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (matiere_id) REFERENCES matieres(id),
    FOREIGN KEY (periode_id) REFERENCES periodes(id),
    UNIQUE KEY unique_affectation (professeur_id, matiere_id, periode_id, groupe)
);

-- =============================================
-- TABLE DE CONFIGURATION DES COLONNES (DYNAMIQUE)
-- C'est ici que la magie opère !
-- =============================================
CREATE TABLE configuration_colonnes (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    matiere_id          INT NOT NULL,
    periode_id          INT NOT NULL,
    nom_colonne         VARCHAR(50) NOT NULL,  -- "DS1", "TP2", "Examen"
    code_colonne        VARCHAR(20) NOT NULL,  -- "DS1", "TP2" (pour les formules)
    type                ENUM('note', 'bonus', 'malus', 'info') DEFAULT 'note',
    note_max            DECIMAL(5,2) DEFAULT 20,
    coefficient         DECIMAL(3,1) DEFAULT 1,
    obligatoire         BOOLEAN DEFAULT TRUE,
    ordre               INT NOT NULL,
    date_creation       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id),
    FOREIGN KEY (periode_id) REFERENCES periodes(id),
    UNIQUE KEY unique_colonne (matiere_id, periode_id, code_colonne)
);-- =============================================
-- TABLE DES FORMULES DE CALCUL
-- =============================================
CREATE TABLE formules (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    matiere_id          INT NOT NULL,
    periode_id          INT NOT NULL,
    formule             TEXT NOT NULL,  -- "(DS1 + DS2 + Examen*2) / 4"
    description         VARCHAR(255),
    date_creation       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id),
    FOREIGN KEY (periode_id) REFERENCES periodes(id),
    UNIQUE KEY unique_formule (matiere_id, periode_id)
);

-- =============================================
-- TABLE DES INSCRIPTIONS ÉTUDIANTS AUX MATIÈRES
-- =============================================
CREATE TABLE inscriptions_matieres (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id         INT NOT NULL,
    matiere_id          INT NOT NULL,
    periode_id          INT NOT NULL,
    groupe              VARCHAR(50),
    dispense            BOOLEAN DEFAULT FALSE,
    date_inscription    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (matiere_id) REFERENCES matieres(id),
    FOREIGN KEY (periode_id) REFERENCES periodes(id),
    UNIQUE KEY unique_inscription (etudiant_id, matiere_id, periode_id)
);

-- =============================================
-- TABLE DES NOTES (DONNÉES SAISIES)
-- Stockage flexible :  une ligne par note saisie
-- =============================================
CREATE TABLE notes (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id         INT NOT NULL,
    colonne_id          INT NOT NULL,  -- Référence à configuration_colonnes
    valeur              DECIMAL(5,2),  -- NULL si absence/non saisi
    statut              ENUM('saisie', 'absent', 'dispense', 'defaillant') DEFAULT 'saisie',
    saisi_par           INT NOT NULL,
    date_saisie         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification   TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (colonne_id) REFERENCES configuration_colonnes(id),
    FOREIGN KEY (saisi_par) REFERENCES utilisateurs(id),
    UNIQUE KEY unique_note (etudiant_id, colonne_id)
);

-- =============================================
-- TABLE DES MOYENNES CALCULÉES
-- Résultats pré-calculés pour performance
-- =============================================
CREATE TABLE moyennes (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    etudiant_id         INT NOT NULL,
    matiere_id          INT NOT NULL,
    periode_id          INT NOT NULL,
    moyenne             DECIMAL(5,2),
    rang                INT,
    decision            ENUM('valide', 'non_valide', 'rattrapage', 'en_attente') DEFAULT 'en_attente',
    credits_obtenus     INT,
    date_calcul         TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (etudiant_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (matiere_id) REFERENCES matieres(id),
    FOREIGN KEY (periode_id) REFERENCES periodes(id),
    UNIQUE KEY unique_moyenne (etudiant_id, matiere_id, periode_id)
);

-- =============================================
-- TABLE DE PROGRESSION DE SAISIE
-- Suivi de l'avancement par matière
-- =============================================
CREATE TABLE progression_saisie (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    matiere_id          INT NOT NULL,
    periode_id          INT NOT NULL,
    professeur_id       INT NOT NULL,
    total_etudiants     INT NOT NULL,
    total_notes_attendues INT NOT NULL,  -- nb_etudiants × nb_colonnes
    notes_saisies       INT DEFAULT 0,
    pourcentage         DECIMAL(5,2) DEFAULT 0,
    valide_par_prof     BOOLEAN DEFAULT FALSE,
    date_validation     DATETIME,
    date_mise_a_jour    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id),
    FOREIGN KEY (periode_id) REFERENCES periodes(id),
    FOREIGN KEY (professeur_id) REFERENCES utilisateurs(id),
    UNIQUE KEY unique_progression (matiere_id, periode_id)
);

-- =============================================
-- TABLE D'HISTORIQUE DES MODIFICATIONS
-- Audit trail complet
-- =============================================
CREATE TABLE historique_notes (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    note_id             INT NOT NULL,
    ancienne_valeur     DECIMAL(5,2),
    nouvelle_valeur     DECIMAL(5,2),
    ancien_statut       VARCHAR(20),
    nouveau_statut      VARCHAR(20),
    modifie_par         INT NOT NULL,
    motif               TEXT,
    adresse_ip          VARCHAR(45),
    date_modification   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id),
    FOREIGN KEY (modifie_par) REFERENCES utilisateurs(id)
);

-- =============================================
-- TABLE DES TEMPLATES DE FORMULES
-- Bibliothèque de formules réutilisables
-- =============================================
CREATE TABLE templates_formules (
    id                  INT PRIMARY KEY AUTO_INCREMENT,
    nom                 VARCHAR(100) NOT NULL,
    description         TEXT,
    colonnes_requises   JSON NOT NULL,  -- ["DS1", "DS2", "Examen"]
    formule             TEXT NOT NULL,
    categorie           VARCHAR(50),  -- "Standard", "Avec bonus", etc.
    date_creation       TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Données initiales :  templates de formules courantes
INSERT INTO templates_formules (nom, description, colonnes_requises, formule, categorie) VALUES
('Moyenne simple', 'Moyenne arithmétique de toutes les notes', 
 '["Note1", "Note2"]', 'MOYENNE(Note1, Note2)', 'Standard'),
('DS + Examen', 'DS coefficient 1, Examen coefficient 2', 
 '["DS", "Examen"]', '(DS + Examen * 2) / 3', 'Standard'),
('Meilleure des deux', 'Garde la meilleure note entre deux évaluations', 
 '["Note1", "Note2"]', 'MAX(Note1, Note2)', 'Spécial'),
('TP + Projet + Examen', 'Moyenne TP 30%, Projet 30%, Examen 40%', 
 '["TP", "Projet", "Examen"]', 'TP * 0.3 + Projet * 0.3 + Examen * 0.4', 'Standard');

-- =============================================
-- INDEX POUR PERFORMANCE
-- =============================================
CREATE INDEX idx_notes_etudiant ON notes(etudiant_id);
CREATE INDEX idx_notes_colonne ON notes(colonne_id);
CREATE INDEX idx_config_matiere_periode ON configuration_colonnes(matiere_id, periode_id);
CREATE INDEX idx_moyennes_periode ON moyennes(periode_id);
```

---

## **7. POINTS DE SYNCHRONISATION**

### **Points de rencontre obligatoires :**
1. **Jour 1** : Kick-off + conventions techniques
2. **Jour 2** : Revue schéma BDD
3. **Jour 4** : Première intégration complète
4. **Jour 6** : Test final avant livraison

### **Communication quotidienne :**
- **9h00** : Stand-up rapide (15 min)
- **13h00** : Point problèmes techniques
- **17h00** : Revue avancement

---

## **8. LIVRABLES ATTENDUS**

### **Pour le 12 Janvier 18h00 :**
1. **Code source** complet sur Git
2. **Base de données** avec données de test
3. **Documentation** d'installation
4. **Présentation** PowerPoint (10 min)
5. **Démo** fonctionnelle (5 min)

### **Contenu de la démo :**
```
1. Connexion administrateur
2. Création d'une période "Semestre 1"
3. Configuration : Mathématiques → DS1, DS2, Examen
4. Connexion professeur
5. Saisie notes pour 3 étudiants
6. Visualisation notes par étudiant
7. Génération PDF relevé
8. Calcul et affichage moyennes
```

---

## **9. RISQUES ET MITIGATIONS**

### **Risque 1 : Parser trop complexe**
- **Mitigation** : Commencer avec moyenne arithmétique simple
- **Fallback** : Calcul manuel si parser non prêt

### **Risque 2 : Tableau saisie buggé**
- **Mitigation** : Version simplifiée d'abord (formulaire par étudiant)
- **Fallback** : Saisie individuelle plutôt que tableau

### **Risque 3 : Problèmes d'intégration**
- **Mitigation** : Tests d'intégration quotidiens
- **Fallback** : Branches Git séparées pour chaque fonctionnalité

### **Risque 4 : Génération PDF**
- **Mitigation** : HTML d'abord, conversion PDF ensuite
- **Fallback** : Page HTML imprimable

---

## **10. CONSEILS PRATIQUES**

### **Pour tenir les délais :**
1. **Simplifier au maximum**
   - Pas de drag & drop → Formulaire fixe
   - Pas de multiples formules → Moyenne simple
   - Pas d'emails → Notifications internes

2. **Prioriser le fonctionnel sur le beau**
   - Bootstrap pour aller vite
   - Design minimal mais propre
   - Focus UX sur les parcours critiques

3. **Tester immédiatement**
   - Tester chaque fonction après codage
   - Intégration continue même basique
   - Pair programming sur parties complexes

4. **Documenter au fur et à mesure**
   - Commentaires dans le code
   - README avec instructions installation
   - Journal des décisions techniques

### **Répartition optimale du temps :**
- **70%** : Fonctionnalités MVP
- **20%** : Tests et intégration
- **10%** : Documentation et préparation présentation

---

## **11. CHECKLIST FINALE**

### **Avant livraison, vérifier :**
- [ ] Login fonctionne pour les 3 types d'utilisateurs
- [ ] Admin peut configurer colonnes
- [ ] Prof peut saisir et sauvegarder notes
- [ ] Étudiant peut consulter ses notes
- [ ] Moyenne se calcule automatiquement
- [ ] PDF basique généré
- [ ] Pas d'erreurs PHP/MySQL apparentes
- [ ] Code commenté et propre
- [ ] README avec instructions
- [ ] Présentation préparée

---

## **12. CONTACTS ET RESPONSABILITÉS**

### **Coordination :** D1 (Admin)
- Gestion planning
- Points de synchronisation
- Communication externe

### **Qualité :** D3 (BDD)
- Revue code
- Tests intégration
- Performance

### **Documentation :** D4 (Visualisation)
- README
- Présentation
- Guide utilisateur

### **Démo :** D2 (Saisie)
- Préparation démo
- Scénario de test
- Support présentation

---

**Date de livraison : 12 Janvier 2026, 18h00**  
**Lieu de dépôt :** Git repository  
**Format :** Archive ZIP contenant code + BDD + documentation

*Ce plan garantit la livraison d'un MVP fonctionnel en 8 jours avec une équipe de 4 développeurs. L'accent est mis sur l'essentiel et la collaboration efficace.*

---

**Signature de l'équipe :**

_________________________  
Développeur 1 - Administration

_________________________  
Développeur 2 - Interface Professeur

_________________________  
Développeur 3 - Moteur & BDD

_________________________  
Développeur 4 - Visualisation

**Date : 4 Janvier 2026**