<?php
session_start();
require_once 'config/database.php';

$message = "";
$messageType = "danger";

$specialites = $pdo->query("SELECT * FROM specialites ORDER BY nom_specialite")->fetchAll();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role = $_POST['role'] ?? '';
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($nom) || empty($prenom) || empty($email) || empty($password) || !in_array($role, ['medecin', 'patient'])) {
        $message = "Merci de remplir tous les champs obligatoires.";
    } else {
        try {
            $checkStmt = $pdo->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = ?");
            $checkStmt->execute([$email]);
            if ($checkStmt->fetch()) {
                throw new Exception("Un compte existe déjà avec cet email.");
            }

            $pdo->beginTransaction();
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            if ($role === 'medecin') {
                $idSpecialite = !empty($_POST['id_specialite']) ? $_POST['id_specialite'] : null;

                $stmt = $pdo->prepare("INSERT INTO medecins (nom, prenom, email, telephone, id_specialite) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $email, $telephone, $idSpecialite]);
                $idMedecin = $pdo->lastInsertId();

                $stmt2 = $pdo->prepare("INSERT INTO utilisateurs (email, mot_de_passe, role, id_medecin) VALUES (?, ?, 'medecin', ?)");
                $stmt2->execute([$email, $hashedPassword, $idMedecin]);
            } else {
                $dateNaissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;

                $stmt = $pdo->prepare("INSERT INTO patients (nom, prenom, email, telephone, date_naissance) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$nom, $prenom, $email, $telephone, $dateNaissance]);
                $idPatient = $pdo->lastInsertId();

                $stmt2 = $pdo->prepare("INSERT INTO utilisateurs (email, mot_de_passe, role, id_patient) VALUES (?, ?, 'patient', ?)");
                $stmt2->execute([$email, $hashedPassword, $idPatient]);
            }

            $pdo->commit();
            $message = "Compte créé avec succès. Vous pouvez maintenant vous connecter.";
            $messageType = "success";

        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $message = "Erreur : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - MedTrack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body>
    <div class="auth-wrapper">
        <div class="card auth-card" style="max-width: 480px;">
            <div class="card-body p-5">
                <div class="auth-logo"><i class="fas fa-heart-pulse"></i></div>
                <h2 class="auth-title">Créer un compte</h2>

                <?php if ($message): ?>
                    <div class="alert alert-<?php echo $messageType; ?>">
                        <i class="fas <?php echo $messageType === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation'; ?> me-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <label class="form-label d-block">Je suis :</label>
                    <div class="role-toggle">
                        <input type="radio" name="role" id="role_patient" value="patient" checked>
                        <label for="role_patient"><i class="fas fa-user me-1"></i>Patient</label>
                        <input type="radio" name="role" id="role_medecin" value="medecin">
                        <label for="role_medecin"><i class="fas fa-user-doctor me-1"></i>Médecin</label>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label for="prenom" class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label for="nom" class="form-label">Nom</label>
                            <input type="text" class="form-control" id="nom" name="nom" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone">
                    </div>

                    <div id="champsMedecin" style="display:none;" class="mb-3">
                        <label for="id_specialite" class="form-label">Spécialité</label>
                        <select class="form-select" id="id_specialite" name="id_specialite">
                            <option value="">Sélectionner une spécialité</option>
                            <?php foreach ($specialites as $s): ?>
                                <option value="<?php echo $s['id_specialite']; ?>"><?php echo htmlspecialchars($s['nom_specialite']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="champsPatient" class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance">
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required minlength="6">
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>Créer mon compte
                    </button>
                    <div class="text-center mt-3">
                        <a href="login.php">Déjà un compte ? Se connecter</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/js/scripts.js"></script>
</body>
</html>
