<?php
// modules/patients/modifier.php - Médecin uniquement
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'medecin') {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['id_patient'])) {
            throw new Exception("ID du patient manquant.");
        }

        $sql = "UPDATE patients SET
                nom = :nom, prenom = :prenom, email = :email, telephone = :telephone,
                date_naissance = :date_naissance, adresse = :adresse, groupe_sanguin = :groupe_sanguin,
                allergies = :allergies, antecedents = :antecedents, notes_medecin = :notes_medecin,
                id_medecin_referent = :id_medecin_referent
                WHERE id_patient = :id_patient";

        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
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
            'id_patient' => $_POST['id_patient'],
        ]);

        echo json_encode(['success' => (bool)$result]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
