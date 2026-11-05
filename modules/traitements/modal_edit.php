<div class="modal fade" id="editTraitementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-prescription me-2"></i>Modifier le traitement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditTraitement" action="modifier.php" method="POST" onsubmit="return false;">
                <input type="hidden" id="edit_id_traitement" name="id_traitement">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom du médicament</label>
                        <input type="text" class="form-control" id="edit_nom_medicament" name="nom_medicament" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Dosage</label>
                            <input type="text" class="form-control" id="edit_dosage" name="dosage">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Fréquence</label>
                            <input type="text" class="form-control" id="edit_frequence" name="frequence">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" class="form-control" id="edit_date_debut" name="date_debut" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Date de fin</label>
                            <input type="date" class="form-control" id="edit_date_fin" name="date_fin">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Médecin prescripteur</label>
                        <select class="form-select" id="edit_id_medecin_prescripteur" name="id_medecin_prescripteur">
                            <option value="">Non renseigné</option>
                            <?php foreach ($medecins as $m): ?>
                                <option value="<?php echo $m['id_medecin']; ?>">Dr <?php echo htmlspecialchars($m['prenom'] . ' ' . $m['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select class="form-select" id="edit_statut_traitement" name="statut" required>
                            <option value="actif">Actif</option>
                            <option value="termine">Terminé</option>
                            <option value="arrete">Arrêté</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea class="form-control" id="edit_instructions" name="instructions" rows="2"></textarea>
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
