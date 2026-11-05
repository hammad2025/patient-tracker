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
        if (empty($_POST['id_rdv'])) {
            throw new Exception("ID manquant.");
        }
        $stmt = $pdo->prepare("DELETE FROM rendez_vous WHERE id_rdv = ?");
        $result = $stmt->execute([$_POST['id_rdv']]);
        echo json_encode(['success' => (bool)$result]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
