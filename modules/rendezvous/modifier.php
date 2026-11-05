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
            throw new Exception("ID du rendez-vous manquant.");
        }
        $sql = "UPDATE rendez_vous SET id_patient = :id_patient, id_medecin = :id_medecin,
                date_rdv = :date_rdv, heure_rdv = :heure_rdv, motif = :motif,
                statut = :statut, compte_rendu = :compte_rendu
                WHERE id_rdv = :id_rdv";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'id_patient' => $_POST['id_patient'],
            'id_medecin' => $_POST['id_medecin'],
            'date_rdv' => $_POST['date_rdv'],
            'heure_rdv' => $_POST['heure_rdv'],
            'motif' => $_POST['motif'],
            'statut' => $_POST['statut'],
            'compte_rendu' => $_POST['compte_rendu'] ?: null,
            'id_rdv' => $_POST['id_rdv']
        ]);
        echo json_encode(['success' => (bool)$result]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
