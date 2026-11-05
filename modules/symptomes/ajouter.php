<?php
// modules/symptomes/ajouter.php
// Un patient ne peut ajouter que pour lui-même. Un médecin peut ajouter pour n'importe quel patient.
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireLogin($baseUrl);
$user = $_SESSION['user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $idPatient = (int)($_POST['id_patient'] ?? 0);

        // Sécurité : un patient ne peut soumettre que pour son propre id
        if ($user['role'] === 'patient' && $idPatient !== (int)$user['id_patient']) {
            throw new Exception("Action non autorisée.");
        }

        if (empty($_POST['description']) || empty($_POST['date_symptome']) || empty($idPatient)) {
            throw new Exception("Tous les champs sont obligatoires.");
        }

        $saisiPar = $user['role']; // 'patient' ou 'medecin'

        $stmt = $pdo->prepare("INSERT INTO symptomes (id_patient, date_symptome, description, intensite, saisi_par) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $idPatient,
            $_POST['date_symptome'],
            $_POST['description'],
            $_POST['intensite'] ?: 'leger',
            $saisiPar
        ]);

        $redirect = 'liste.php' . ($user['role'] === 'medecin' ? '?id_patient=' . $idPatient : '');
        header('Location: ' . $redirect . (strpos($redirect, '?') ? '&' : '?') . 'success=1');
        exit;

    } catch (Exception $e) {
        header('Location: liste.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
header('Location: liste.php');
exit;
