<?php
// modules/traitements/liste.php
// Patient : voit SES traitements + peut cocher "pris aujourd'hui"
// Médecin : consulte/gère les traitements d'un patient précis (?id_patient=)
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireLogin($baseUrl);
$user = $_SESSION['user'];

$idPatient = resolvePatientScope($baseUrl);

if ($user['role'] === 'medecin' && !$idPatient) {
    header("Location: ../patients/liste.php?error=" . urlencode("Sélectionnez un patient pour consulter ses traitements."));
    exit;
}
checkPatientAccess($idPatient, $baseUrl);

include '../../includes/header.php';

$stmtPatient = $pdo->prepare("SELECT nom, prenom FROM patients WHERE id_patient = ?");
$stmtPatient->execute([$idPatient]);
$patientInfo = $stmtPatient->fetch();

// Traitements + info prise du jour
$stmt = $pdo->prepare("
    SELECT t.*, m.nom AS m_nom, m.prenom AS m_prenom,
           (SELECT pris FROM prises_medicament pm WHERE pm.id_traitement = t.id_traitement AND pm.date_prise = CURDATE()) AS pris_aujourdhui
    FROM traitements t
    LEFT JOIN medecins m ON t.id_medecin_prescripteur = m.id_medecin
    WHERE t.id_patient = ?
    ORDER BY t.statut = 'actif' DESC, t.date_debut DESC
");
$stmt->execute([$idPatient]);
$traitements = $stmt->fetchAll();

if ($user['role'] === 'medecin') {
    $medecins = $pdo->query("SELECT * FROM medecins ORDER BY nom")->fetchAll();
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="page-title">
            <i class="fas fa-pills"></i>
            <?php echo $user['role'] === 'medecin' ? 'Traitements de ' . htmlspecialchars($patientInfo['prenom'] . ' ' . $patientInfo['nom']) : 'Mes traitements'; ?>
        </h1>
        <?php if ($user['role'] === 'medecin'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutTraitementModal">
                <i class="fas fa-plus me-2"></i>Prescrire un traitement
            </button>
        <?php endif; ?>
    </div>

    <?php if ($user['role'] === 'medecin'): ?>
        <a href="../patients/fiche.php?id=<?php echo $idPatient; ?>" class="btn btn-outline-secondary btn-sm mb-3"><i class="fas fa-arrow-left me-1"></i>Retour à la fiche patient</a>
    <?php endif; ?>

    <div class="row g-4">
        <?php if (empty($traitements)): ?>
            <div class="col-12">
                <div class="card"><div class="card-body text-center text-muted py-4">Aucun traitement enregistré.</div></div>
            </div>
        <?php endif; ?>

        <?php foreach ($traitements as $t): ?>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0"><i class="fas fa-capsules me-2" style="color: var(--medical-blue);"></i><?php echo htmlspecialchars($t['nom_medicament']); ?></h5>
                            <span class="badge-statut badge-<?php echo $t['statut']; ?>"><?php echo ucfirst($t['statut']); ?></span>
                        </div>
                        <p class="mb-1"><strong>Dosage :</strong> <?php echo htmlspecialchars($t['dosage'] ?: '—'); ?></p>
                        <p class="mb-1"><strong>Fréquence :</strong> <?php echo htmlspecialchars($t['frequence'] ?: '—'); ?></p>
                        <p class="mb-1"><strong>Prescrit par :</strong> <?php echo $t['m_nom'] ? 'Dr ' . htmlspecialchars($t['m_prenom'] . ' ' . $t['m_nom']) : '—'; ?></p>
                        <p class="mb-1"><strong>Début :</strong> <?php echo date('d/m/Y', strtotime($t['date_debut'])); ?>
                            <?php if ($t['date_fin']): ?> — <strong>Fin :</strong> <?php echo date('d/m/Y', strtotime($t['date_fin'])); ?><?php endif; ?>
                        </p>
                        <?php if ($t['instructions']): ?>
                            <p class="text-muted small mb-2"><i class="fas fa-circle-info me-1"></i><?php echo htmlspecialchars($t['instructions']); ?></p>
                        <?php endif; ?>

                        <?php if ($t['statut'] === 'actif'): ?>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>
                                    <?php if ($t['pris_aujourdhui'] === null): ?>
                                        <span class="status-dot status-gris"></span>Prise du jour non renseignée
                                    <?php elseif ($t['pris_aujourdhui']): ?>
                                        <span class="status-dot status-vert"></span>Pris aujourd'hui
                                    <?php else: ?>
                                        <span class="status-dot status-rouge"></span>Non pris aujourd'hui
                                    <?php endif; ?>
                                </span>
                                <?php if ($user['role'] === 'patient'): ?>
                                    <div>
                                        <button class="btn btn-sm btn-teal me-1" onclick="marquerPrise(<?php echo $t['id_traitement']; ?>, 1)">
                                            <i class="fas fa-check me-1"></i>Pris
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="marquerPrise(<?php echo $t['id_traitement']; ?>, 0)">
                                            <i class="fas fa-xmark me-1"></i>Oublié
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($user['role'] === 'medecin'): ?>
                            <hr>
                            <div class="text-end">
                                <button class="btn btn-sm btn-primary me-1" onclick='editTraitement(<?php echo json_encode($t, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                    <i class="fas fa-edit me-1"></i>Modifier
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmerSuppression('supprimer.php', 'id_traitement', <?php echo $t['id_traitement']; ?>, 'Supprimer ce traitement ?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($user['role'] === 'medecin'): ?>
    <?php include 'modal_ajout.php'; ?>
    <?php include 'modal_edit.php'; ?>
<?php endif; ?>

<?php include '../../includes/footer.php'; ?>

<script>
<?php if ($user['role'] === 'patient'): ?>
function marquerPrise(idTraitement, pris) {
    fetch('marquer_prise.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_traitement=' + idTraitement + '&pris=' + pris
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) location.reload();
        else alert(data.message || 'Erreur lors de l\'enregistrement');
    })
    .catch(() => alert('Erreur réseau'));
}
<?php endif; ?>

<?php if ($user['role'] === 'medecin'): ?>
function editTraitement(t) {
    document.getElementById('edit_id_traitement').value = t.id_traitement;
    document.getElementById('edit_nom_medicament').value = t.nom_medicament;
    document.getElementById('edit_dosage').value = t.dosage || '';
    document.getElementById('edit_frequence').value = t.frequence || '';
    document.getElementById('edit_date_debut').value = t.date_debut;
    document.getElementById('edit_date_fin').value = t.date_fin || '';
    document.getElementById('edit_instructions').value = t.instructions || '';
    document.getElementById('edit_statut_traitement').value = t.statut;
    document.getElementById('edit_id_medecin_prescripteur').value = t.id_medecin_prescripteur || '';
    new bootstrap.Modal(document.getElementById('editTraitementModal')).show();
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('formEditTraitement').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('modifier.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) location.reload();
                else alert(data.message || 'Erreur lors de la modification');
            });
    });
});
<?php endif; ?>
</script>
