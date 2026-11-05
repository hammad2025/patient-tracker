<?php
// modules/patients/get_patient.php - Médecin uniquement
session_start();
require_once '../../config/database.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'medecin') {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé.']);
    exit;
}

if (isset($_GET['id'])) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM patients WHERE id_patient = ?");
        $stmt->execute([$_GET['id']]);
        $patient = $stmt->fetch();
        echo json_encode($patient);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID manquant.']);
}
