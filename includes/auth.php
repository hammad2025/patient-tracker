<?php
// includes/auth.php
// Gestion de la session, des rôles et du contrôle d'accès (RBAC)
// À inclure APRÈS session_start() et APRÈS config/database.php

/**
 * Bloque l'accès si l'utilisateur n'est pas connecté.
 */
function requireLogin($baseUrl) {
    if (!isset($_SESSION['user'])) {
        header("Location: " . $baseUrl . "/login.php");
        exit;
    }
}

/**
 * Bloque l'accès si l'utilisateur connecté n'est pas médecin.
 */
function requireMedecin($baseUrl) {
    requireLogin($baseUrl);
    if ($_SESSION['user']['role'] !== 'medecin') {
        header("Location: " . $baseUrl . "/index.php?error=" . urlencode("Accès réservé au personnel médical."));
        exit;
    }
}

/**
 * Vérifie qu'un patient ne consulte QUE sa propre fiche.
 * Un médecin a accès à tous les patients sans restriction.
 *
 * @param int $idPatientDemande L'id_patient de la ressource consultée
 */
function checkPatientAccess($idPatientDemande, $baseUrl) {
    requireLogin($baseUrl);
    $user = $_SESSION['user'];

    if ($user['role'] === 'medecin') {
        return; // Le médecin a accès à tous les patients
    }

    if ($user['role'] === 'patient' && (int)$user['id_patient'] === (int)$idPatientDemande) {
        return; // Le patient consulte bien sa propre fiche
    }

    // Toute autre situation = accès refusé
    header("Location: " . $baseUrl . "/index.php?error=" . urlencode("Accès non autorisé à ce dossier."));
    exit;
}

/**
 * Retourne l'id_patient "effectif" pour les pages de type liste/journal :
 * - si médecin : utilise l'id passé en GET (obligatoire)
 * - si patient : force son propre id, ignore toute tentative de GET id différent
 */
function resolvePatientScope($baseUrl) {
    $user = $_SESSION['user'];
    if ($user['role'] === 'patient') {
        return (int)$user['id_patient'];
    }
    // Médecin : id_patient attendu en paramètre GET
    if (isset($_GET['id_patient']) && is_numeric($_GET['id_patient'])) {
        return (int)$_GET['id_patient'];
    }
    return null;
}

/**
 * Petit helper d'affichage du rôle courant (pour le menu, etc.)
 */
function isMedecin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'medecin';
}

function isPatient() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'patient';
}
