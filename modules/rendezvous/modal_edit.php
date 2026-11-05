<div class="modal fade" id="editRdvModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-calendar-pen me-2"></i>Modifier le rendez-vous</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEditRdv" action="modifier.php" method="POST" onsubmit="return false;">
                <input type="hidden" id="edit_id_rdv" name="id_rdv">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Patient</label>
                        <select class="form-select" id="edit_id_patient_rdv" name="id_patient" required>
                            <?php foreach ($patients as $p): ?>
                                <option value="<?php echo $p['id_patient']; ?>"><?php echo htmlspecialchars($p['prenom'] . ' ' . $p['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Médecin</label>
                        <select class="form-select" id="edit_id_medecin_rdv" name="id_medecin" required>
                            <?php foreach ($medecins as $m): ?>
                                <option value="<?php echo $m['id_medecin']; ?>">Dr <?php echo htmlspecialchars($m['prenom'] . ' ' . $m['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" class="form-control" id="edit_date_rdv" name="date_rdv" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">Heure</label>
                            <input type="time" class="form-control" id="edit_heure_rdv" name="heure_rdv" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motif</label>
                        <input type="text" class="form-control" id="edit_motif" name="motif" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <select class="form-select" id="edit_statut" name="statut" required>
                            <option value="prevu">Prévu</option>
                            <option value="termine">Terminé</option>
                            <option value="annule">Annulé</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Compte-rendu de consultation</label>
                        <textarea class="form-control" id="edit_compte_rendu" name="compte_rendu" rows="3" placeholder="Observations, conclusions de la consultation..."></textarea>
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
