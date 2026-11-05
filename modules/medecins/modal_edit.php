<div class="modal fade" id="editMedecinModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-edit me-2"></i>Modifier le médecin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditMedecin" action="modifier.php" method="POST" onsubmit="return false;">
                <input type="hidden" id="edit_id_medecin" name="id_medecin">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" class="form-control" id="edit_prenom" name="prenom" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" id="edit_nom" name="nom" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="edit_telephone" name="telephone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Spécialité</label>
                        <select class="form-select" id="edit_id_specialite" name="id_specialite">
                            <option value="">Sélectionner une spécialité</option>
                            <?php foreach ($specialites as $s): ?>
                                <option value="<?php echo $s['id_specialite']; ?>"><?php echo htmlspecialchars($s['nom_specialite']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>
