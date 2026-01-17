-- Table pour stocker les préférences des professeurs
CREATE TABLE preferences_professeurs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    professeur_id INT NOT NULL,
    preference VARCHAR(50) NOT NULL,
    valeur TEXT,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (professeur_id) REFERENCES utilisateurs(id),
    UNIQUE KEY unique_preference (professeur_id, preference)
);

-- Table pour les templates d'import personnalisés
CREATE TABLE templates_import_prof (
    id INT PRIMARY KEY AUTO_INCREMENT,
    professeur_id INT NOT NULL,
    matiere_id INT NOT NULL,
    nom_template VARCHAR(100) NOT NULL,
    configuration JSON NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (professeur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (matiere_id) REFERENCES matieres(id)
);

-- Table pour les commentaires sur les notes
CREATE TABLE commentaires_notes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    note_id INT NOT NULL,
    professeur_id INT NOT NULL,
    commentaire TEXT,
    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (note_id) REFERENCES notes(id),
    FOREIGN KEY (professeur_id) REFERENCES utilisateurs(id)
);