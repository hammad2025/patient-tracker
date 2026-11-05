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
        if (empty($_POST['id_medecin'])) {
            throw new Exception("ID du médecin manquant.");
        }
        $stmt = $pdo->prepare("UPDATE medecins SET nom = ?, prenom = ?, email = ?, telephone = ?, id_specialite = ? WHERE id_medecin = ?");
        $result = $stmt->execute([
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['email'],
            $_POST['telephone'] ?: null,
            $_POST['id_specialite'] ?: null,
            $_POST['id_medecin']
        ]);
        echo json_encode(['success' => (bool)$result]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
