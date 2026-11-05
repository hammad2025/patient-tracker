<?php
// modules/traitements/marquer_prise.php
// Patient uniquement : marque la prise du jour (pris = 1 ou 0) pour SON propre traitement
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'patient') {
    echo json_encode(['success' => false, 'message' => 'Action réservée au patient.']);
    exit;
}
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $idTraitement = (int)($_POST['id_traitement'] ?? 0);
        $pris = isset($_POST['pris']) && $_POST['pris'] == '1' ? 1 : 0;

        if (!$idTraitement) {
            throw new Exception("ID du traitement manquant.");
        }

        // Sécurité : vérifier que le traitement appartient bien au patient connecté
        $stmt = $pdo->prepare("SELECT id_patient FROM traitements WHERE id_traitement = ?");
        $stmt->execute([$idTraitement]);
        $row = $stmt->fetch();

        if (!$row || (int)$row['id_patient'] !== (int)$user['id_patient']) {
            throw new Exception("Action non autorisée.");
        }

        // Insère ou met à jour la prise du jour (grâce à la contrainte UNIQUE)
        $sql = "INSERT INTO prises_medicament (id_traitement, date_prise, pris, heure_prise)
                VALUES (:id_traitement, CURDATE(), :pris, :heure)
                ON DUPLICATE KEY UPDATE pris = :pris2, heure_prise = :heure2";
        $stmt = $pdo->prepare($sql);
        $heure = $pris ? date('H:i:s') : null;
        $result = $stmt->execute([
            'id_traitement' => $idTraitement,
            'pris' => $pris,
            'heure' => $heure,
            'pris2' => $pris,
            'heure2' => $heure
        ]);

        echo json_encode(['success' => (bool)$result]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Méthode non autorisée.']);
