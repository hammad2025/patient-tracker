-- ========================================================
-- PATIENT TRACKER - Script de création de la base de données
-- Compatible MAMP / MySQL
-- ========================================================

CREATE DATABASE IF NOT EXISTS patient_tracker CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE patient_tracker;

-- ========================================================
-- Table : specialites
-- ========================================================
CREATE TABLE specialites (
    id_specialite INT AUTO_INCREMENT PRIMARY KEY,
    nom_specialite VARCHAR(100) NOT NULL UNIQUE
);

-- ========================================================
-- Table : medecins
-- ========================================================
CREATE TABLE medecins (
    id_medecin INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    id_specialite INT DEFAULT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_specialite) REFERENCES specialites(id_specialite) ON DELETE SET NULL ON UPDATE CASCADE
);

-- ========================================================
-- Table : patients
-- ========================================================
CREATE TABLE patients (
    id_patient INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    telephone VARCHAR(20),
    date_naissance DATE,
    adresse TEXT,
    groupe_sanguin ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
    allergies TEXT,
    antecedents TEXT,
    notes_medecin TEXT,
    id_medecin_referent INT DEFAULT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_medecin_referent) REFERENCES medecins(id_medecin) ON DELETE SET NULL ON UPDATE CASCADE
);

-- ========================================================
-- Table : utilisateurs (comptes de connexion)
-- ========================================================
CREATE TABLE utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('medecin','patient') NOT NULL,
    id_medecin INT DEFAULT NULL,
    id_patient INT DEFAULT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_medecin) REFERENCES medecins(id_medecin) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ========================================================
-- Table : rendez_vous
-- ========================================================
CREATE TABLE rendez_vous (
    id_rdv INT AUTO_INCREMENT PRIMARY KEY,
    id_patient INT NOT NULL,
    id_medecin INT NOT NULL,
    date_rdv DATE NOT NULL,
    heure_rdv TIME NOT NULL,
    motif VARCHAR(255) NOT NULL,
    statut ENUM('prevu','termine','annule') DEFAULT 'prevu',
    compte_rendu TEXT,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_medecin) REFERENCES medecins(id_medecin) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ========================================================
-- Table : symptomes (journal de symptômes)
-- ========================================================
CREATE TABLE symptomes (
    id_symptome INT AUTO_INCREMENT PRIMARY KEY,
    id_patient INT NOT NULL,
    date_symptome DATE NOT NULL,
    description TEXT NOT NULL,
    intensite ENUM('leger','modere','severe') DEFAULT 'leger',
    saisi_par ENUM('patient','medecin') DEFAULT 'patient',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ========================================================
-- Table : traitements (médicaments prescrits)
-- ========================================================
CREATE TABLE traitements (
    id_traitement INT AUTO_INCREMENT PRIMARY KEY,
    id_patient INT NOT NULL,
    id_medecin_prescripteur INT DEFAULT NULL,
    nom_medicament VARCHAR(150) NOT NULL,
    dosage VARCHAR(100),
    frequence VARCHAR(100),
    date_debut DATE NOT NULL,
    date_fin DATE DEFAULT NULL,
    instructions TEXT,
    statut ENUM('actif','termine','arrete') DEFAULT 'actif',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_patient) REFERENCES patients(id_patient) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_medecin_prescripteur) REFERENCES medecins(id_medecin) ON DELETE SET NULL ON UPDATE CASCADE
);

-- ========================================================
-- Table : prises_medicament (suivi quotidien de prise)
-- ========================================================
CREATE TABLE prises_medicament (
    id_prise INT AUTO_INCREMENT PRIMARY KEY,
    id_traitement INT NOT NULL,
    date_prise DATE NOT NULL,
    pris TINYINT(1) DEFAULT 0,
    heure_prise TIME DEFAULT NULL,
    FOREIGN KEY (id_traitement) REFERENCES traitements(id_traitement) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE KEY unique_prise_jour (id_traitement, date_prise)
);

-- ========================================================
-- DONNÉES DE TEST
-- ========================================================

INSERT INTO specialites (nom_specialite) VALUES
('Médecine générale'),
('Cardiologie'),
('Pédiatrie'),
('Dermatologie'),
('Endocrinologie');

INSERT INTO medecins (nom, prenom, email, telephone, id_specialite) VALUES
('Lemoine', 'Sophie', 'sophie.lemoine@hopital.fr', '01 42 33 44 55', 1),
('Traoré', 'Amadou', 'amadou.traore@hopital.fr', '01 42 33 44 56', 2),
('Benaissa', 'Nadia', 'nadia.benaissa@hopital.fr', '01 42 33 44 57', 5);

INSERT INTO patients (nom, prenom, email, telephone, date_naissance, adresse, groupe_sanguin, allergies, antecedents, notes_medecin, id_medecin_referent) VALUES
('Dupont', 'Marc', 'marc.dupont@mail.fr', '06 12 34 56 78', '1985-04-12', '12 rue des Lilas, 75011 Paris', 'A+', 'Pénicilline', 'Hypertension légère diagnostiquée en 2020', 'Patient suivi régulièrement, bonne observance du traitement.', 1),
('Girard', 'Léa', 'lea.girard@mail.fr', '06 23 45 67 89', '1992-09-03', '5 avenue Victor Hugo, 75016 Paris', 'O-', 'Aucune connue', 'Asthme depuis l''enfance', 'Surveiller la fréquence des crises en période hivernale.', 2),
('Moreau', 'Julien', 'julien.moreau@mail.fr', '06 34 56 78 90', '1978-11-22', '8 boulevard Saint-Michel, 75005 Paris', 'B+', 'Iode', 'Diabète type 2 depuis 2018', 'Contrôle glycémique à revoir lors du prochain rendez-vous.', 3);

INSERT INTO utilisateurs (email, mot_de_passe, role, id_medecin, id_patient) VALUES
-- mot de passe pour tous les comptes de démo : "password123"
('sophie.lemoine@hopital.fr', '$2y$10$6LmJv.ILr8Tz0f9XuI3S1.m4rXg5Om6rlEymOopQ1oFfw2Hpps8oW', 'medecin', 1, NULL),
('amadou.traore@hopital.fr', '$2y$10$6LmJv.ILr8Tz0f9XuI3S1.m4rXg5Om6rlEymOopQ1oFfw2Hpps8oW', 'medecin', 2, NULL),
('marc.dupont@mail.fr', '$2y$10$6LmJv.ILr8Tz0f9XuI3S1.m4rXg5Om6rlEymOopQ1oFfw2Hpps8oW', 'patient', NULL, 1),
('lea.girard@mail.fr', '$2y$10$6LmJv.ILr8Tz0f9XuI3S1.m4rXg5Om6rlEymOopQ1oFfw2Hpps8oW', 'patient', NULL, 2);

INSERT INTO rendez_vous (id_patient, id_medecin, date_rdv, heure_rdv, motif, statut, compte_rendu) VALUES
(1, 1, '2025-05-10', '09:30:00', 'Contrôle tension artérielle', 'termine', 'Tension stabilisée à 13/8. Poursuite du traitement en cours.'),
(2, 2, '2025-05-15', '14:00:00', 'Suivi asthme', 'termine', 'Fonction respiratoire correcte. RAS.'),
(1, 1, '2025-07-20', '10:00:00', 'Renouvellement ordonnance', 'prevu', NULL),
(3, 3, '2025-07-25', '11:15:00', 'Bilan diabète trimestriel', 'prevu', NULL);

INSERT INTO symptomes (id_patient, date_symptome, description, intensite, saisi_par) VALUES
(1, '2025-06-28', 'Légers maux de tête en fin de journée', 'leger', 'patient'),
(2, '2025-06-30', 'Essoufflement après effort modéré', 'modere', 'patient'),
(3, '2025-07-01', 'Fatigue inhabituelle depuis 3 jours', 'modere', 'patient');

INSERT INTO traitements (id_patient, id_medecin_prescripteur, nom_medicament, dosage, frequence, date_debut, date_fin, instructions, statut) VALUES
(1, 1, 'Amlodipine', '5 mg', '1 comprimé par jour', '2024-01-15', NULL, 'À prendre le matin, de préférence avant le petit-déjeuner.', 'actif'),
(2, 2, 'Ventoline', '100 µg', 'Si besoin, max 4 fois par jour', '2023-06-01', NULL, 'Inhalation en cas de gêne respiratoire.', 'actif'),
(3, 3, 'Metformine', '500 mg', '2 comprimés par jour', '2024-03-10', NULL, 'À prendre au cours des repas (midi et soir).', 'actif');

INSERT INTO prises_medicament (id_traitement, date_prise, pris, heure_prise) VALUES
(1, '2025-07-04', 1, '08:15:00'),
(1, '2025-07-05', 0, NULL),
(3, '2025-07-04', 1, '12:30:00'),
(3, '2025-07-05', 1, '12:45:00');
