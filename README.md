# MedTrack — Plateforme de suivi de patients

Application web de suivi médical avec deux espaces distincts : **médecin** (accès complet à tous les dossiers) et **patient** (accès restreint à ses propres données).

Construite avec la même stack que le projet INVADER (gestion de stock) : **PHP procédural + PDO + MySQL (MAMP) + Bootstrap 5**, sans framework.

---

## Fonctionnalités

### Côté médecin
- Tableau de bord : nombre de patients, RDV du jour, traitements actifs, symptômes récents
- Gestion complète des patients (fiche détaillée : groupe sanguin, allergies, antécédents, notes du médecin)
- Gestion des médecins et des spécialités
- Planification de rendez-vous avec statut (prévu / terminé / annulé) et compte-rendu de consultation
- Prescription de traitements (dosage, fréquence, prescripteur, instructions)
- Consultation du journal de symptômes de n'importe quel patient

### Côté patient
- Espace personnel : ses RDV, ses traitements, ses symptômes uniquement
- Journal de symptômes (date, description, intensité léger/modéré/sévère)
- Suivi de prise quotidienne des médicaments (« Pris » / « Oublié »)
- Consultation de sa propre fiche médicale

### Sécurité (RBAC)
- Comptes distincts médecin/patient (table `utilisateurs` avec rôle)
- Mots de passe hashés (bcrypt via `password_hash`)
- Un patient ne peut jamais accéder aux données d'un autre patient, même en manipulant les URLs ou les requêtes AJAX (vérification côté serveur sur chaque endpoint)

---

## Installation (MAMP)

1. **Copier le dossier** `patient_tracker` dans le répertoire `htdocs` de MAMP
   (ex : `/Applications/MAMP/htdocs/patient_tracker`)

2. **Créer la base de données** :
   - Ouvrir phpMyAdmin (http://localhost:8888/phpMyAdmin)
   - Onglet « Importer » → sélectionner le fichier `sql/patient_tracker.sql` → Exécuter
   - (Le script crée la base `patient_tracker`, les 8 tables et les données de démo)

3. **Vérifier la configuration** dans `config/database.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'patient_tracker');
   define('DB_USER', 'root');
   define('DB_PASS', 'root');
   define('DB_PORT', '8889'); // Port MySQL de MAMP
   ```

4. **Lancer** : http://localhost:8888/patient_tracker/login.php

---

## Comptes de démonstration

Mot de passe pour tous les comptes : **`password123`**

| Rôle | Email |
|---|---|
| Médecin | sophie.lemoine@hopital.fr |
| Médecin | amadou.traore@hopital.fr |
| Patient | marc.dupont@mail.fr |
| Patient | lea.girard@mail.fr |

Il est aussi possible de créer un nouveau compte (médecin ou patient) via la page d'inscription.

---

## Structure du projet

```
patient_tracker/
├── config/
│   └── database.php          # Connexion PDO (MAMP)
├── includes/
│   ├── auth.php              # Contrôle d'accès par rôle (RBAC)
│   ├── header.php            # Navigation adaptative selon le rôle
│   └── footer.php
├── assets/
│   ├── css/styles.css        # Thème hospitalier (bleu médical)
│   └── js/scripts.js
├── modules/
│   ├── patients/             # CRUD + fiche détaillée
│   ├── medecins/             # CRUD médecins
│   ├── specialites/          # CRUD spécialités
│   ├── rendezvous/           # RDV avec statut + compte-rendu
│   ├── symptomes/            # Journal de symptômes
│   └── traitements/          # Prescriptions + suivi de prise quotidienne
├── sql/
│   └── patient_tracker.sql   # Script complet (tables + données de démo)
├── index.php                 # Dashboard (adaptatif médecin/patient)
├── login.php
├── register.php
└── logout.php
```

---

## Base de données

8 tables : `utilisateurs`, `medecins`, `patients`, `specialites`, `rendez_vous`, `symptomes`, `traitements`, `prises_medicament`.

Relations principales :
- `utilisateurs` → lie un compte à une fiche `medecins` OU `patients` selon le rôle
- `patients.id_medecin_referent` → médecin référent
- `rendez_vous` → patient + médecin
- `traitements` → patient + médecin prescripteur ; `prises_medicament` → suivi quotidien (contrainte UNIQUE traitement/jour)
