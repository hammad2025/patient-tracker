<?php
// modules/medecins/liste.php - Médecin uniquement
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireMedecin($baseUrl);

include '../../includes/header.php';

$medecins = $pdo->query("
    SELECT m.*, s.nom_specialite,
    (SELECT COUNT(*) FROM patients p WHERE p.id_medecin_referent = m.id_medecin) AS nb_patients
    FROM medecins m
    LEFT JOIN specialites s ON m.id_specialite = s.id_specialite
    ORDER BY m.nom
")->fetchAll();

$specialites = $pdo->query("SELECT * FROM specialites ORDER BY nom_specialite")->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="page-title"><i class="fas fa-user-doctor"></i>Médecins</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutMedecinModal">
            <i class="fas fa-plus me-2"></i>Ajouter un médecin
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Spécialité</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Patients suivis</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($medecins as $m): ?>
                            <tr>
                                <td><strong>Dr <?php echo htmlspecialchars($m['prenom'] . ' ' . $m['nom']); ?></strong></td>
                                <td><?php echo htmlspecialchars($m['nom_specialite'] ?: '—'); ?></td>
                                <td><?php echo htmlspecialchars($m['email']); ?></td>
                                <td><?php echo htmlspecialchars($m['telephone'] ?: '—'); ?></td>
                                <td><span class="badge-statut badge-prevu"><?php echo $m['nb_patients']; ?></span></td>
                                <td class="text-nowrap">
                                    <button class="btn btn-sm btn-primary me-1" onclick="editMedecin(<?php echo $m['id_medecin']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmerSuppression('supprimer.php', 'id_medecin', <?php echo $m['id_medecin']; ?>, 'Supprimer ce médecin ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($medecins)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Aucun médecin enregistré.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'modal_ajout.php'; ?>
<?php include 'modal_edit.php'; ?>
<?php include '../../includes/footer.php'; ?>

<script>
function editMedecin(id) {
    fetch(`get_medecin.php?id=${id}`)
        .then(response => response.json())
        .then(m => {
            document.getElementById('edit_id_medecin').value = m.id_medecin;
            document.getElementById('edit_nom').value = m.nom;
            document.getElementById('edit_prenom').value = m.prenom;
            document.getElementById('edit_email').value = m.email;
            document.getElementById('edit_telephone').value = m.telephone || '';
            document.getElementById('edit_id_specialite').value = m.id_specialite || '';
            new bootstrap.Modal(document.getElementById('editMedecinModal')).show();
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('formEditMedecin').addEventListener('submit', function (e) {
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
