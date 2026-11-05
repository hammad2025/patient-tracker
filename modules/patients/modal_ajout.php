<!-- modules/patients/modal_ajout.php -->
<div class="modal fade" id="ajoutPatientModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Ajouter un patient</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formAjoutPatient" action="ajouter.php" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Prénom</label>
                            <input type="text" class="form-control" name="prenom" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nom</label>
                            <input type="text" class="form-control" name="nom" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Téléphone</label>
                            <input type="tel" class="form-control" name="telephone">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date de naissance</label>
                            <input type="date" class="form-control" name="date_naissance">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Groupe sanguin</label>
                            <select class="form-select" name="groupe_sanguin">
                                <option value="">Non renseigné</option>
                                <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $gs): ?>
                                    <option value="<?php echo $gs; ?>"><?php echo $gs; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Adresse</label>
                        <textarea class="form-control" name="adresse" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Médecin référent</label>
                        <select class="form-select" name="id_medecin_referent">
                            <option value="">Aucun</option>
                            <?php foreach ($medecins as $m): ?>
                                <option value="<?php echo $m['id_medecin']; ?>">Dr <?php echo htmlspecialchars($m['prenom'] . ' ' . $m['nom']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Allergies connues</label>
                        <textarea class="form-control" name="allergies" rows="2" placeholder="Ex: Pénicilline, Iode..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Antécédents médicaux</label>
                        <textarea class="form-control" name="antecedents" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes du médecin</label>
                        <textarea class="form-control" name="notes_medecin" rows="2" placeholder="Remarques internes visibles par l'équipe médicale"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
