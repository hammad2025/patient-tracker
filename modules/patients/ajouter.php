<?php
// modules/patients/ajouter.php - Médecin uniquement
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireMedecin($baseUrl);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email'])) {
            throw new Exception("Les champs nom, prénom et email sont obligatoires.");
        }

        $sql = "INSERT INTO patients (nom, prenom, email, telephone, date_naissance, adresse,
                                       groupe_sanguin, allergies, antecedents, notes_medecin, id_medecin_referent)
                VALUES (:nom, :prenom, :email, :telephone, :date_naissance, :adresse,
                        :groupe_sanguin, :allergies, :antecedents, :notes_medecin, :id_medecin_referent)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'nom' => $_POST['nom'],
            'prenom' => $_POST['prenom'],
            'email' => $_POST['email'],
            'telephone' => $_POST['telephone'] ?: null,
            'date_naissance' => $_POST['date_naissance'] ?: null,
            'adresse' => $_POST['adresse'] ?: null,
            'groupe_sanguin' => $_POST['groupe_sanguin'] ?: null,
            'allergies' => $_POST['allergies'] ?: null,
            'antecedents' => $_POST['antecedents'] ?: null,
            'notes_medecin' => $_POST['notes_medecin'] ?: null,
            'id_medecin_referent' => $_POST['id_medecin_referent'] ?: null,
        ]);

        header('Location: liste.php?success=1');
        exit;

    } catch (Exception $e) {
        header('Location: liste.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

header('Location: liste.php');
exit;
