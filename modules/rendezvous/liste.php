<?php
// modules/rendezvous/liste.php
// Médecin : voit TOUS les RDV (ou filtrés par patient via ?id_patient=)
// Patient : voit UNIQUEMENT ses propres RDV
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireLogin($baseUrl);
$user = $_SESSION['user'];

include '../../includes/header.php';

$filtrePatientId = null;

if ($user['role'] === 'medecin') {
    $filtrePatientId = isset($_GET['id_patient']) && is_numeric($_GET['id_patient']) ? (int)$_GET['id_patient'] : null;

    $sql = "SELECT rv.*, p.nom AS p_nom, p.prenom AS p_prenom, m.nom AS m_nom, m.prenom AS m_prenom
            FROM rendez_vous rv
            JOIN patients p ON rv.id_patient = p.id_patient
            JOIN medecins m ON rv.id_medecin = m.id_medecin";
    $params = [];
    if ($filtrePatientId) {
        $sql .= " WHERE rv.id_patient = :id_patient";
        $params['id_patient'] = $filtrePatientId;
    }
    $sql .= " ORDER BY rv.date_rdv DESC, rv.heure_rdv DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rdvs = $stmt->fetchAll();

    $patients = $pdo->query("SELECT * FROM patients ORDER BY nom")->fetchAll();
    $medecins = $pdo->query("SELECT * FROM medecins ORDER BY nom")->fetchAll();

} else {
    $idPatient = (int)$user['id_patient'];
    $stmt = $pdo->prepare("
        SELECT rv.*, m.nom AS m_nom, m.prenom AS m_prenom
        FROM rendez_vous rv
        JOIN medecins m ON rv.id_medecin = m.id_medecin
        WHERE rv.id_patient = ?
        ORDER BY rv.date_rdv DESC, rv.heure_rdv DESC
    ");
    $stmt->execute([$idPatient]);
    $rdvs = $stmt->fetchAll();
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="page-title">
            <i class="fas fa-calendar-check"></i>
            <?php echo $user['role'] === 'medecin' ? 'Rendez-vous' : 'Mes rendez-vous'; ?>
        </h1>
        <?php if ($user['role'] === 'medecin'): ?>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutRdvModal">
                <i class="fas fa-plus me-2"></i>Planifier un rendez-vous
            </button>
        <?php endif; ?>
    </div>

    <?php if ($user['role'] === 'medecin' && $filtrePatientId): ?>
        <a href="liste.php" class="btn btn-outline-secondary btn-sm mb-3"><i class="fas fa-filter-circle-xmark me-1"></i>Retirer le filtre patient</a>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <?php if ($user['role'] === 'medecin'): ?><th>Patient</th><?php endif; ?>
                            <th>Médecin</th>
                            <th>Date</th>
                            <th>Heure</th>
                            <th>Motif</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rdvs as $rdv): ?>
                            <tr>
                                <?php if ($user['role'] === 'medecin'): ?>
                                    <td><strong><?php echo htmlspecialchars($rdv['p_prenom'] . ' ' . $rdv['p_nom']); ?></strong></td>
                                <?php endif; ?>
                                <td>Dr <?php echo htmlspecialchars($rdv['m_prenom'] . ' ' . $rdv['m_nom']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($rdv['date_rdv'])); ?></td>
                                <td><?php echo substr($rdv['heure_rdv'], 0, 5); ?></td>
                                <td><?php echo htmlspecialchars($rdv['motif']); ?></td>
                                <td><span class="badge-statut badge-<?php echo $rdv['statut']; ?>"><?php echo ucfirst($rdv['statut']); ?></span></td>
                                <td class="text-nowrap">
                                    <button class="btn btn-sm btn-teal me-1" onclick="voirDetail(<?php echo htmlspecialchars(json_encode($rdv), ENT_QUOTES); ?>)" title="Détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if ($user['role'] === 'medecin'): ?>
                                        <button class="btn btn-sm btn-primary me-1" onclick="editRdv(<?php echo htmlspecialchars(json_encode($rdv), ENT_QUOTES); ?>)" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="confirmerSuppression('supprimer.php', 'id_rdv', <?php echo $rdv['id_rdv']; ?>, 'Supprimer ce rendez-vous ?')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($rdvs)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Aucun rendez-vous.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($user['role'] === 'medecin'): ?>
    <?php include 'modal_ajout.php'; ?>
    <?php include 'modal_edit.php'; ?>
<?php endif; ?>

<!-- Modal Détail (lecture seule, pour tous) -->
<div class="modal fade" id="detailRdvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-day me-2"></i>Détails du rendez-vous</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="detailRdvContent"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>

<script>
const userRole = "<?php echo $user['role']; ?>";

function voirDetail(rdv) {
    const statutLabels = { prevu: 'Prévu', termine: 'Terminé', annule: 'Annulé' };
    let html = `
        <p><strong>Date :</strong> ${new Date(rdv.date_rdv).toLocaleDateString('fr-FR')} à ${rdv.heure_rdv.substring(0,5)}</p>
        <p><strong>Motif :</strong> ${rdv.motif}</p>
        <p><strong>Statut :</strong> <span class="badge-statut badge-${rdv.statut}">${statutLabels[rdv.statut]}</span></p>
    `;
    if (rdv.compte_rendu) {
        html += `<p><strong>Compte-rendu :</strong><br>${rdv.compte_rendu.replace(/\n/g, '<br>')}</p>`;
    } else {
        html += `<p class="text-muted">Aucun compte-rendu disponible.</p>`;
    }
    document.getElementById('detailRdvContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('detailRdvModal')).show();
}

<?php if ($user['role'] === 'medecin'): ?>
function editRdv(rdv) {
    document.getElementById('edit_id_rdv').value = rdv.id_rdv;
    document.getElementById('edit_id_patient_rdv').value = rdv.id_patient;
    document.getElementById('edit_id_medecin_rdv').value = rdv.id_medecin;
    document.getElementById('edit_date_rdv').value = rdv.date_rdv;
    document.getElementById('edit_heure_rdv').value = rdv.heure_rdv.substring(0,5);
    document.getElementById('edit_motif').value = rdv.motif;
    document.getElementById('edit_statut').value = rdv.statut;
    document.getElementById('edit_compte_rendu').value = rdv.compte_rendu || '';
    new bootstrap.Modal(document.getElementById('editRdvModal')).show();
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('formEditRdv').addEventListener('submit', function (e) {
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
