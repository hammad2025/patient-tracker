<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireMedecin($baseUrl);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $required = ['id_patient', 'id_medecin', 'date_rdv', 'heure_rdv', 'motif'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                throw new Exception("Le champ $field est requis.");
            }
        }
        $stmt = $pdo->prepare("INSERT INTO rendez_vous (id_patient, id_medecin, date_rdv, heure_rdv, motif) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['id_patient'],
            $_POST['id_medecin'],
            $_POST['date_rdv'],
            $_POST['heure_rdv'],
            $_POST['motif']
        ]);
        header('Location: liste.php?success=1');
        exit;
    } catch (Exception $e) {
        header('Location: liste.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
header('Location: liste.php');
exit;
