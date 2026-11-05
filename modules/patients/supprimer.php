<?php
// modules/patients/supprimer.php - Médecin uniquement
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
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
        $stmt = $pdo->prepare("DELETE FROM patients WHERE id_patient = :id_patient");
        $result = $stmt->execute(['id_patient' => $_POST['id_patient']]);
        echo json_encode(['success' => (bool)$result]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
