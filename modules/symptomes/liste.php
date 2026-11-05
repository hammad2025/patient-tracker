<?php
// modules/symptomes/liste.php
// Patient : voit et ajoute SES symptômes
// Médecin : consulte le journal d'un patient précis (?id_patient=) et peut aussi ajouter une entrée
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireLogin($baseUrl);
$user = $_SESSION['user'];

$idPatient = resolvePatientScope($baseUrl);

if ($user['role'] === 'medecin' && !$idPatient) {
    header("Location: ../patients/liste.php?error=" . urlencode("Sélectionnez un patient pour consulter son journal de symptômes."));
    exit;
}
checkPatientAccess($idPatient, $baseUrl);

include '../../includes/header.php';

$stmtPatient = $pdo->prepare("SELECT nom, prenom FROM patients WHERE id_patient = ?");
$stmtPatient->execute([$idPatient]);
$patientInfo = $stmtPatient->fetch();

$stmt = $pdo->prepare("SELECT * FROM symptomes WHERE id_patient = ? ORDER BY date_symptome DESC, id_symptome DESC");
$stmt->execute([$idPatient]);
$symptomes = $stmt->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="page-title">
            <i class="fas fa-notes-medical"></i>
            <?php echo $user['role'] === 'medecin' ? 'Journal de ' . htmlspecialchars($patientInfo['prenom'] . ' ' . $patientInfo['nom']) : 'Mon journal de symptômes'; ?>
        </h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutSymptomeModal">
            <i class="fas fa-plus me-2"></i>Signaler un symptôme
        </button>
    </div>

    <?php if ($user['role'] === 'medecin'): ?>
        <a href="../patients/fiche.php?id=<?php echo $idPatient; ?>" class="btn btn-outline-secondary btn-sm mb-3"><i class="fas fa-arrow-left me-1"></i>Retour à la fiche patient</a>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (empty($symptomes)): ?>
                <p class="text-muted mb-0 text-center py-4">Aucun symptôme signalé pour le moment.</p>
            <?php else: ?>
                <?php foreach ($symptomes as $s): ?>
                    <div class="timeline-item">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div>
                                <strong><?php echo date('d/m/Y', strtotime($s['date_symptome'])); ?></strong>
                                <span class="badge-statut badge-prevu ms-2">
                                    <?php echo $s['saisi_par'] === 'medecin' ? 'Saisi par le médecin' : 'Saisi par le patient'; ?>
                                </span>
                            </div>
                            <div>
                                <span class="intensite-<?php echo $s['intensite']; ?> me-3"><?php echo ucfirst($s['intensite']); ?></span>
                                <button class="btn btn-sm btn-danger" onclick="confirmerSuppression('supprimer.php', 'id_symptome', <?php echo $s['id_symptome']; ?>, 'Supprimer cette entrée ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($s['description'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Ajout -->
<div class="modal fade" id="ajoutSymptomeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-notes-medical me-2"></i>Signaler un symptôme</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="ajouter.php" method="POST">
                <input type="hidden" name="id_patient" value="<?php echo $idPatient; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" name="date_symptome" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" required placeholder="Décrivez le symptôme ressenti..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Intensité</label>
                        <select class="form-select" name="intensite" required>
                            <option value="leger">Léger</option>
                            <option value="modere">Modéré</option>
                            <option value="severe">Sévère</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
