<div class="modal fade" id="ajoutTraitementModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-prescription me-2"></i>Prescrire un traitement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="ajouter.php" method="POST">
                <input type="hidden" name="id_patient" value="<?php echo $idPatient; ?>">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nom du médicament</label>
                        <input type="text" class="form-control" name="nom_medicament" required placeholder="Ex: Doliprane">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Dosage</label>
                            <input type="text" class="form-control" name="dosage" placeholder="Ex: 500 mg">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Fréquence</label>
                            <input type="text" class="form-control" name="frequence" placeholder="Ex: 2 fois par jour">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Date de début</label>
                            <input type="date" class="form-control" name="date_debut" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Date de fin (optionnel)</label>
                            <input type="date" class="form-control" name="date_fin">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Médecin prescripteur</label>
                        <select class="form-select" name="id_medecin_prescripteur">
                            <option value="">Non renseigné</option>
                            <?php foreach ($medecins as $m): ?>
                                <option value="<?php echo $m['id_medecin']; ?>" <?php echo ($m['id_medecin'] == $user['id_medecin']) ? 'selected' : ''; ?>>
                                    Dr <?php echo htmlspecialchars($m['prenom'] . ' ' . $m['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Instructions</label>
                        <textarea class="form-control" name="instructions" rows="2" placeholder="Ex: À prendre au cours des repas"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Prescrire</button>
                </div>
            </form>
        </div>
    </div>
</div>
