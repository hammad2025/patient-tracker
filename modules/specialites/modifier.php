<?php
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
        if (empty($_POST['id_specialite']) || empty($_POST['nom_specialite'])) {
            throw new Exception("Données manquantes.");
        }
        $stmt = $pdo->prepare("UPDATE specialites SET nom_specialite = ? WHERE id_specialite = ?");
        $result = $stmt->execute([$_POST['nom_specialite'], $_POST['id_specialite']]);
        echo json_encode(['success' => (bool)$result]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
