<div class="modal fade" id="ajoutRdvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-plus me-2"></i>Planifier un rendez-vous</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="ajouter.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Patient</label>
                        <select class="form-select" name="id_patient" required>
                            <option value="">Sélectionner un patient</option>
                            <?php foreach ($patients as $p): ?>
                                <option value="<?php echo $p['id_patient']; ?>" <?php echo (isset($filtrePatientId) && $filtrePatientId == $p['id_patient']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['prenom'] . ' ' . $p['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Médecin</label>
                        <select class="form-select" name="id_medecin" required>
                            <option value="">Sélectionner un médecin</option>
                            <?php foreach ($medecins as $m): ?>
                                <option value="<?php echo $m['id_medecin']; ?>">Dr <?php echo htmlspecialchars($m['prenom'] . ' ' . $m['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" name="date_rdv" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Heure</label>
                            <input type="time" class="form-control" name="heure_rdv" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motif</label>
                        <input type="text" class="form-control" name="motif" required placeholder="Ex: Consultation de suivi">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Planifier</button>
                </div>
            </form>
        </div>
    </div>
</div>
