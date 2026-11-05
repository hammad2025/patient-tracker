<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$baseUrl = '/patient_tracker';
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedTrack - Suivi Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="<?php echo $baseUrl; ?>/assets/css/styles.css" rel="stylesheet">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22><text y=%22.9em%22 font-size=%2220%22>➕</text></svg>">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="<?php echo $baseUrl; ?>/index.php">
                <i class="fas fa-heart-pulse me-2"></i>MedTrack
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <?php if ($user && $user['role'] === 'medecin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/index.php">
                                <i class="fas fa-gauge me-1"></i>Tableau de bord
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/modules/patients/liste.php">
                                <i class="fas fa-hospital-user me-1"></i>Patients
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/modules/rendezvous/liste.php">
                                <i class="fas fa-calendar-check me-1"></i>Rendez-vous
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/modules/medecins/liste.php">
                                <i class="fas fa-user-doctor me-1"></i>Médecins
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/modules/specialites/liste.php">
                                <i class="fas fa-tags me-1"></i>Spécialités
                            </a>
                        </li>
                    <?php elseif ($user && $user['role'] === 'patient'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/index.php">
                                <i class="fas fa-gauge me-1"></i>Mon espace
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/modules/symptomes/liste.php">
                                <i class="fas fa-notes-medical me-1"></i>Mes symptômes
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/modules/traitements/liste.php">
                                <i class="fas fa-pills me-1"></i>Mes traitements
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/modules/rendezvous/liste.php">
                                <i class="fas fa-calendar-check me-1"></i>Mes rendez-vous
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($user): ?>
                        <li class="nav-item ms-lg-3">
                            <span class="role-badge <?php echo $user['role']; ?>">
                                <i class="fas <?php echo $user['role'] === 'medecin' ? 'fa-user-doctor' : 'fa-user'; ?> me-1"></i>
                                <?php echo htmlspecialchars($user['nom_affiche']); ?>
                            </span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $baseUrl; ?>/logout.php">
                                <i class="fas fa-right-from-bracket me-1"></i>Déconnexion
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <?php if (isset($_GET['success'])): ?>
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show alert-auto-dismiss" role="alert">
                <i class="fas fa-circle-check me-2"></i>Opération effectuée avec succès.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['error'])): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    <?php endif; ?>
