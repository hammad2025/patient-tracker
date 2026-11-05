<?php
// modules/traitements/ajouter.php - Médecin uniquement (prescription)
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireMedecin($baseUrl);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPatient = (int)($_POST['id_patient'] ?? 0);
    try {
        if (empty($_POST['nom_medicament']) || empty($_POST['date_debut']) || empty($idPatient)) {
            throw new Exception("Le nom du médicament et la date de début sont obligatoires.");
        }
        $stmt = $pdo->prepare("
            INSERT INTO traitements (id_patient, id_medecin_prescripteur, nom_medicament, dosage, frequence, date_debut, date_fin, instructions)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $idPatient,
            $_POST['id_medecin_prescripteur'] ?: null,
            $_POST['nom_medicament'],
            $_POST['dosage'] ?: null,
            $_POST['frequence'] ?: null,
            $_POST['date_debut'],
            $_POST['date_fin'] ?: null,
            $_POST['instructions'] ?: null
        ]);
        header('Location: liste.php?id_patient=' . $idPatient . '&success=1');
        exit;
    } catch (Exception $e) {
        header('Location: liste.php?id_patient=' . $idPatient . '&error=' . urlencode($e->getMessage()));
        exit;
    }
}
header('Location: ../patients/liste.php');
exit;
