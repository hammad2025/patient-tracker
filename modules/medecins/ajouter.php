<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireMedecin($baseUrl);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['nom']) || empty($_POST['prenom']) || empty($_POST['email'])) {
            throw new Exception("Les champs nom, prénom et email sont obligatoires.");
        }
        $stmt = $pdo->prepare("INSERT INTO medecins (nom, prenom, email, telephone, id_specialite) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['email'],
            $_POST['telephone'] ?: null,
            $_POST['id_specialite'] ?: null
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
