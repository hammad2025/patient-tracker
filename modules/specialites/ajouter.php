<?php
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireMedecin($baseUrl);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (empty($_POST['nom_specialite'])) {
            throw new Exception("Le nom de la spécialité est obligatoire.");
        }
        $stmt = $pdo->prepare("INSERT INTO specialites (nom_specialite) VALUES (?)");
        $stmt->execute([$_POST['nom_specialite']]);
        header('Location: liste.php?success=1');
        exit;
    } catch (Exception $e) {
        header('Location: liste.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}
header('Location: liste.php');
exit;
