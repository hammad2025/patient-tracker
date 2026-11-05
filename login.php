<?php
session_start();
require_once 'config/database.php';

$message = "";

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($email) && !empty($password)) {
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($password, $utilisateur['mot_de_passe'])) {
            $nomAffiche = '';

            if ($utilisateur['role'] === 'medecin') {
                $stmtM = $pdo->prepare("SELECT nom, prenom FROM medecins WHERE id_medecin = ?");
                $stmtM->execute([$utilisateur['id_medecin']]);
                $medecin = $stmtM->fetch();
                $nomAffiche = 'Dr ' . $medecin['nom'];
            } else {
                $stmtP = $pdo->prepare("SELECT nom, prenom FROM patients WHERE id_patient = ?");
                $stmtP->execute([$utilisateur['id_patient']]);
                $patient = $stmtP->fetch();
                $nomAffiche = $patient['prenom'] . ' ' . $patient['nom'];
            }

            $_SESSION['user'] = [
                'id' => $utilisateur['id_utilisateur'],
                'role' => $utilisateur['role'],
                'id_medecin' => $utilisateur['id_medecin'],
                'id_patient' => $utilisateur['id_patient'],
                'nom_affiche' => $nomAffiche
            ];

            header("Location: index.php");
            exit;
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    } else {
        $message = "Merci de renseigner tous les champs.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - MedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="card auth-card">
            <div class="card-body p-5">
                <div class="auth-logo"><i class="fas fa-heart-pulse"></i></div>
                <h2 class="auth-title">MedTrack</h2>
                <p class="text-center text-muted mb-4">Connectez-vous à votre espace</p>

                <?php if ($message): ?>
                    <div class="alert alert-danger"><i class="fas fa-triangle-exclamation me-2"></i><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-right-to-bracket me-2"></i>Se connecter
                    </button>
                    <div class="text-center mt-3">
                        <a href="register.php">Créer un compte</a>
                    </div>
                </form>

                <hr>
                <small class="text-muted d-block text-center">
                    Comptes de démo (mot de passe : <code>password123</code>)<br>
                    Médecin : sophie.lemoine@hopital.fr<br>
                    Patient : marc.dupont@mail.fr
                </small>
            </div>
        </div>
    </div>
</body>
</html>
