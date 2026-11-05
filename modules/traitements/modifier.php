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
        if (empty($_POST['id_traitement'])) {
            throw new Exception("ID du traitement manquant.");
        }
        $sql = "UPDATE traitements SET
                nom_medicament = :nom_medicament, dosage = :dosage, frequence = :frequence,
                date_debut = :date_debut, date_fin = :date_fin, instructions = :instructions,
                statut = :statut, id_medecin_prescripteur = :id_medecin_prescripteur
                WHERE id_traitement = :id_traitement";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'nom_medicament' => $_POST['nom_medicament'],
            'dosage' => $_POST['dosage'] ?: null,
            'frequence' => $_POST['frequence'] ?: null,
            'date_debut' => $_POST['date_debut'],
            'date_fin' => $_POST['date_fin'] ?: null,
            'instructions' => $_POST['instructions'] ?: null,
            'statut' => $_POST['statut'],
            'id_medecin_prescripteur' => $_POST['id_medecin_prescripteur'] ?: null,
            'id_traitement' => $_POST['id_traitement']
        ]);
        echo json_encode(['success' => (bool)$result]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
