    <footer class="footer mt-auto py-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-heart-pulse me-2"></i>MedTrack</h5>
                    <p class="text-muted">Plateforme de suivi médical patients &amp; praticiens</p>
                </div>
                <div class="col-md-4 text-center">
                    <h5>Navigation Rapide</h5>
                    <ul class="list-unstyled">
                        <?php if (isMedecin()): ?>
                            <li><a href="<?php echo $baseUrl; ?>/modules/patients/liste.php">Patients</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/modules/rendezvous/liste.php">Rendez-vous</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/modules/medecins/liste.php">Médecins</a></li>
                        <?php elseif (isPatient()): ?>
                            <li><a href="<?php echo $baseUrl; ?>/modules/symptomes/liste.php">Mes symptômes</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/modules/traitements/liste.php">Mes traitements</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/modules/rendezvous/liste.php">Mes rendez-vous</a></li>
                        <?php else: ?>
                            <li><a href="<?php echo $baseUrl; ?>/login.php">Connexion</a></li>
                            <li><a href="<?php echo $baseUrl; ?>/register.php">Créer un compte</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-md-4 text-end">
                    <h5>Contact</h5>
                    <p class="text-muted">
                        <i class="fas fa-envelope me-2"></i>contact@medtrack.fr<br>
                        <i class="fas fa-phone me-2"></i>01 42 33 44 00
                    </p>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <small class="text-muted">&copy; <?php echo date('Y'); ?> MedTrack - Tous droits réservés</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $baseUrl; ?>/assets/js/scripts.js"></script>
</body>
</html>
