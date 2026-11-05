<?php
// modules/patients/liste.php - Accessible aux médecins uniquement
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
session_start();
requireMedecin($baseUrl);

include '../../includes/header.php';

$sql = "SELECT p.*, m.nom AS medecin_nom, m.prenom AS medecin_prenom
        FROM patients p
        LEFT JOIN medecins m ON p.id_medecin_referent = m.id_medecin
        ORDER BY p.nom, p.prenom";
$patients = $pdo->query($sql)->fetchAll();

$medecins = $pdo->query("SELECT * FROM medecins ORDER BY nom")->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="page-title"><i class="fas fa-hospital-user"></i>Liste des Patients</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutPatientModal">
            <i class="fas fa-plus me-2"></i>Ajouter un patient
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Groupe sanguin</th>
                            <th>Médecin référent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($patients as $p): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($p['prenom'] . ' ' . $p['nom']); ?></strong></td>
                                <td><?php echo htmlspecialchars($p['email']); ?></td>
                                <td><?php echo htmlspecialchars($p['telephone'] ?: '—'); ?></td>
                                <td><?php echo $p['groupe_sanguin'] ? htmlspecialchars($p['groupe_sanguin']) : '—'; ?></td>
                                <td><?php echo $p['medecin_nom'] ? 'Dr ' . htmlspecialchars($p['medecin_prenom'] . ' ' . $p['medecin_nom']) : '—'; ?></td>
                                <td class="text-nowrap">
                                    <a class="btn btn-sm btn-teal me-1" href="fiche.php?id=<?php echo $p['id_patient']; ?>" title="Voir la fiche">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-primary me-1" onclick="editPatient(<?php echo $p['id_patient']; ?>)" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmerSuppression('supprimer.php', 'id_patient', <?php echo $p['id_patient']; ?>, 'Supprimer ce patient et toutes ses données associées ?')" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($patients)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">Aucun patient enregistré.</td></tr>
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
        })
        .catch(error => {
            alert('Erreur lors du chargement des données');
            console.error('Error:', error);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('formEditPatient').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);
        fetch('modifier.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Erreur lors de la modification');
                }
            })
            .catch(error => {
                alert('Erreur lors de la modification');
                console.error('Error:', error);
            });
    });
});
</script>
