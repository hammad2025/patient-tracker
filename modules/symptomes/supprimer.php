<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Accès non autorisé.']);
    exit;
}
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['id_symptome'])) {
            throw new Exception("ID manquant.");
        }

        // Vérifier à qui appartient ce symptôme avant suppression (sécurité patient)
        $stmt = $pdo->prepare("SELECT id_patient FROM symptomes WHERE id_symptome = ?");
        $stmt->execute([$_POST['id_symptome']]);
        $row = $stmt->fetch();

        if (!$row) {
            throw new Exception("Symptôme introuvable.");
        }
        if ($user['role'] === 'patient' && (int)$row['id_patient'] !== (int)$user['id_patient']) {
            throw new Exception("Action non autorisée.");
        }

        $del = $pdo->prepare("DELETE FROM symptomes WHERE id_symptome = ?");
        $result = $del->execute([$_POST['id_symptome']]);
        echo json_encode(['success' => (bool)$result]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
