<?php
// modules/patients/fiche.php
// Accessible par : le médecin (pour n'importe quel patient) OU le patient (uniquement sa propre fiche)
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireLogin($baseUrl);

$idPatient = isset($_GET['id']) ? (int)$_GET['id'] : 0;
checkPatientAccess($idPatient, $baseUrl); // bloque si patient tente de voir un autre dossier

include '../../includes/header.php';

$stmt = $pdo->prepare("
    SELECT p.*, m.nom AS medecin_nom, m.prenom AS medecin_prenom
    FROM patients p
    LEFT JOIN medecins m ON p.id_medecin_referent = m.id_medecin
    WHERE p.id_patient = ?
");
$stmt->execute([$idPatient]);
$patient = $stmt->fetch();

if (!$patient) {
    echo '<div class="container mt-4"><div class="alert alert-danger">Patient introuvable.</div></div>';
    include '../../includes/footer.php';
    exit;
}

$stmtRdv = $pdo->prepare("
    SELECT rv.*, m.nom AS m_nom, m.prenom AS m_prenom
    FROM rendez_vous rv JOIN medecins m ON rv.id_medecin = m.id_medecin
    WHERE rv.id_patient = ? ORDER BY rv.date_rdv DESC LIMIT 5
");
$stmtRdv->execute([$idPatient]);
$rdvs = $stmtRdv->fetchAll();

$stmtSympt = $pdo->prepare("SELECT * FROM symptomes WHERE id_patient = ? ORDER BY date_symptome DESC LIMIT 5");
$stmtSympt->execute([$idPatient]);
$symptomes = $stmtSympt->fetchAll();

$stmtTrait = $pdo->prepare("SELECT * FROM traitements WHERE id_patient = ? ORDER BY statut = 'actif' DESC, date_debut DESC LIMIT 5");
$stmtTrait->execute([$idPatient]);
$traitements = $stmtTrait->fetchAll();

$age = $patient['date_naissance'] ? floor((time() - strtotime($patient['date_naissance'])) / 31556926) : null;
?>

<div class="container mt-4">
    <?php if (isMedecin()): ?>
        <a href="liste.php" class="btn btn-outline-secondary btn-sm mb-3"><i class="fas fa-arrow-left me-1"></i>Retour à la liste</a>
    <?php endif; ?>

    <div class="patient-header d-flex align-items-center flex-wrap gap-3">
        <div class="patient-avatar">
            <?php echo strtoupper(substr($patient['prenom'], 0, 1) . substr($patient['nom'], 0, 1)); ?>
        </div>
        <div>
            <h2 class="mb-1"><?php echo htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']); ?></h2>
            <p class="mb-0 opacity-75">
                <?php echo $age !== null ? $age . ' ans · ' : ''; ?>
                <?php echo $patient['medecin_nom'] ? 'Suivi par Dr ' . htmlspecialchars($patient['medecin_prenom'] . ' ' . $patient['medecin_nom']) : 'Aucun médecin référent'; ?>
            </p>
        </div>
        <?php if (isMedecin()): ?>
            <button class="btn btn-teal ms-auto" onclick="editPatient(<?php echo $patient['id_patient']; ?>)">
                <i class="fas fa-edit me-1"></i>Modifier la fiche
            </button>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-id-card me-2"></i>Informations générales</div>
                <div class="card-body">
                    <div class="info-label">Email</div>
                    <div class="info-value"><?php echo htmlspecialchars($patient['email']); ?></div>

                    <div class="info-label">Téléphone</div>
                    <div class="info-value"><?php echo htmlspecialchars($patient['telephone'] ?: 'Non renseigné'); ?></div>

                    <div class="info-label">Date de naissance</div>
                    <div class="info-value"><?php echo $patient['date_naissance'] ? date('d/m/Y', strtotime($patient['date_naissance'])) : 'Non renseignée'; ?></div>

                    <div class="info-label">Adresse</div>
                    <div class="info-value"><?php echo nl2br(htmlspecialchars($patient['adresse'] ?: 'Non renseignée')); ?></div>

                    <div class="info-label">Groupe sanguin</div>
                    <div class="info-value">
                        <?php if ($patient['groupe_sanguin']): ?>
                            <span class="badge-statut badge-prevu"><i class="fas fa-droplet me-1"></i><?php echo htmlspecialchars($patient['groupe_sanguin']); ?></span>
                        <?php else: ?>
                            Non renseigné
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-triangle-exclamation me-2"></i>Allergies</div>
                <div class="card-body">
                    <?php if (!empty($patient['allergies'])): ?>
                        <?php foreach (explode(',', $patient['allergies']) as $allergie): ?>
                            <span class="allergy-tag"><i class="fas fa-triangle-exclamation me-1"></i><?php echo htmlspecialchars(trim($allergie)); ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Aucune allergie connue.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isMedecin() && !empty($patient['notes_medecin'])): ?>
                <div class="note-box mb-4">
                    <strong><i class="fas fa-note-sticky me-2"></i>Notes du médecin</strong>
                    <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($patient['notes_medecin'])); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header"><i class="fas fa-notes-medical me-2"></i>Antécédents médicaux</div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($patient['antecedents'] ?: 'Aucun antécédent renseigné.')); ?></p>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-pills me-2"></i>Traitements récents</span>
                    <a href="../traitements/liste.php<?php echo isMedecin() ? '?id_patient=' . $idPatient : ''; ?>" class="text-white small">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="card-body">
                    <?php if (empty($traitements)): ?>
                        <p class="text-muted mb-0">Aucun traitement enregistré.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($traitements as $t): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <strong><?php echo htmlspecialchars($t['nom_medicament']); ?></strong>
                                        <div class="text-muted small"><?php echo htmlspecialchars($t['dosage'] . ' — ' . $t['frequence']); ?></div>
                                    </div>
                                    <span class="badge-statut badge-<?php echo $t['statut']; ?>"><?php echo ucfirst($t['statut']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-calendar-check me-2"></i>Rendez-vous récents</span>
                    <a href="../rendezvous/liste.php<?php echo isMedecin() ? '?id_patient=' . $idPatient : ''; ?>" class="text-white small">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="card-body">
                    <?php if (empty($rdvs)): ?>
                        <p class="text-muted mb-0">Aucun rendez-vous enregistré.</p>
                    <?php else: ?>
                        <ul class="list-group list-group-flush">
                            <?php foreach ($rdvs as $rdv): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                    <div>
                                        <strong>Dr <?php echo htmlspecialchars($rdv['m_prenom'] . ' ' . $rdv['m_nom']); ?></strong>
                                        <div class="text-muted small"><?php echo htmlspecialchars($rdv['motif']); ?> — <?php echo date('d/m/Y', strtotime($rdv['date_rdv'])); ?></div>
                                    </div>
                                    <span class="badge-statut badge-<?php echo $rdv['statut']; ?>"><?php echo ucfirst($rdv['statut']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-heart-pulse me-2"></i>Journal de symptômes</span>
                    <a href="../symptomes/liste.php<?php echo isMedecin() ? '?id_patient=' . $idPatient : ''; ?>" class="text-white small">Voir tout <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                <div class="card-body">
                    <?php if (empty($symptomes)): ?>
                        <p class="text-muted mb-0">Aucun symptôme signalé.</p>
                    <?php else: ?>
                        <?php foreach ($symptomes as $s): ?>
                            <div class="timeline-item">
                                <div class="d-flex justify-content-between">
                                    <strong><?php echo date('d/m/Y', strtotime($s['date_symptome'])); ?></strong>
                                    <span class="intensite-<?php echo $s['intensite']; ?>"><?php echo ucfirst($s['intensite']); ?></span>
                                </div>
                                <p class="mb-0 text-muted"><?php echo htmlspecialchars($s['description']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (isMedecin()):
    $medecins = $pdo->query("SELECT * FROM medecins ORDER BY nom")->fetchAll();
    include 'modal_edit.php';
endif; ?>

<?php include '../../includes/footer.php'; ?>

<?php if (isMedecin()): ?>
<script>
function editPatient(id) {
    fetch(`get_patient.php?id=${id}`)
        .then(response => response.json())
        .then(patient => {
            document.getElementById('edit_id_patient').value = patient.id_patient;
            document.getElementById('edit_nom').value = patient.nom;
            document.getElementById('edit_prenom').value = patient.prenom;
            document.getElementById('edit_email').value = patient.email;
            document.getElementById('edit_telephone').value = patient.telephone || '';
            document.getElementById('edit_date_naissance').value = patient.date_naissance || '';
            document.getElementById('edit_adresse').value = patient.adresse || '';
            document.getElementById('edit_groupe_sanguin').value = patient.groupe_sanguin || '';
            document.getElementById('edit_allergies').value = patient.allergies || '';
            document.getElementById('edit_antecedents').value = patient.antecedents || '';
            document.getElementById('edit_notes_medecin').value = patient.notes_medecin || '';
            document.getElementById('edit_id_medecin_referent').value = patient.id_medecin_referent || '';
            new bootstrap.Modal(document.getElementById('editPatientModal')).show();
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('formEditPatient').addEventListener('submit', function (e) {
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
</script>
<?php endif; ?>
