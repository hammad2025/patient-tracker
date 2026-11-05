<?php
// modules/specialites/liste.php - Médecin uniquement
session_start();
require_once '../../config/database.php';
require_once '../../includes/auth.php';
$baseUrl = '/patient_tracker';
requireMedecin($baseUrl);

include '../../includes/header.php';

$specialites = $pdo->query("
    SELECT s.*, (SELECT COUNT(*) FROM medecins m WHERE m.id_specialite = s.id_specialite) AS nb_medecins
    FROM specialites s ORDER BY s.nom_specialite
")->fetchAll();
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <h1 class="page-title"><i class="fas fa-tags"></i>Spécialités</h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ajoutSpecialiteModal">
            <i class="fas fa-plus me-2"></i>Ajouter une spécialité
        </button>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Médecins rattachés</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($specialites as $s): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($s['nom_specialite']); ?></td>
                                <td><span class="badge-statut badge-prevu"><?php echo $s['nb_medecins']; ?></span></td>
                                <td class="text-nowrap">
                                    <button class="btn btn-sm btn-primary me-1" onclick="editSpecialite(<?php echo $s['id_specialite']; ?>, '<?php echo htmlspecialchars($s['nom_specialite'], ENT_QUOTES); ?>')">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="confirmerSuppression('supprimer.php', 'id_specialite', <?php echo $s['id_specialite']; ?>, 'Supprimer cette spécialité ?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($specialites)): ?>
                            <tr><td colspan="3" class="text-center text-muted py-4">Aucune spécialité enregistrée.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajout -->
<div class="modal fade" id="ajoutSpecialiteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-tag me-2"></i>Ajouter une spécialité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="ajouter.php" method="POST">
                <div class="modal-body">
                    <label class="form-label">Nom de la spécialité</label>
                    <input type="text" class="form-control" name="nom_specialite" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Édition -->
<div class="modal fade" id="editSpecialiteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Modifier la spécialité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditSpecialite" action="modifier.php" method="POST" onsubmit="return false;">
                <input type="hidden" id="edit_id_specialite" name="id_specialite">
                <div class="modal-body">
                    <label class="form-label">Nom de la spécialité</label>
                    <input type="text" class="form-control" id="edit_nom_specialite" name="nom_specialite" required>
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

<script>
function editSpecialite(id, nom) {
    document.getElementById('edit_id_specialite').value = id;
    document.getElementById('edit_nom_specialite').value = nom;
    new bootstrap.Modal(document.getElementById('editSpecialiteModal')).show();
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('formEditSpecialite').addEventListener('submit', function (e) {
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
