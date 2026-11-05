<?php
session_start();
require_once 'config/database.php';
require_once 'includes/auth.php';
$baseUrl = '/patient_tracker';

requireLogin($baseUrl);
$user = $_SESSION['user'];

include 'includes/header.php';

if ($user['role'] === 'medecin'):
    // ================== DASHBOARD MÉDECIN ==================
    $idMedecin = $user['id_medecin'];

    $stats = [
        'patients' => $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn(),
        'rdv_jour' => (function() use ($pdo) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM rendez_vous WHERE date_rdv = CURDATE() AND statut = 'prevu'");
            $stmt->execute();
            return $stmt->fetchColumn();
        })(),
        'traitements_actifs' => $pdo->query("SELECT COUNT(*) FROM traitements WHERE statut = 'actif'")->fetchColumn(),
        'symptomes_recents' => $pdo->query("SELECT COUNT(*) FROM symptomes WHERE date_symptome >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)")->fetchColumn(),
    ];

    $stmtRdv = $pdo->prepare("
        SELECT rv.*, p.nom AS p_nom, p.prenom AS p_prenom
        FROM rendez_vous rv
        JOIN patients p ON rv.id_patient = p.id_patient
        WHERE rv.statut = 'prevu'
        ORDER BY rv.date_rdv ASC, rv.heure_rdv ASC
        LIMIT 5
    ");
    $stmtRdv->execute();
    $prochainRdv = $stmtRdv->fetchAll();

    $stmtSympt = $pdo->query("
        SELECT s.*, p.nom AS p_nom, p.prenom AS p_prenom
        FROM symptomes s
        JOIN patients p ON s.id_patient = p.id_patient
        ORDER BY s.date_symptome DESC, s.id_symptome DESC
        LIMIT 5
    ");
    $derniersSymptomes = $stmtSympt->fetchAll();
    ?>

    <div class="hero-section">
        <div class="container">
            <h1 class="display-5 text-white"><i class="fas fa-user-doctor cross-icon"></i>Bienvenue, <?php echo htmlspecialchars($user['nom_affiche']); ?></h1>
            <p class="lead text-white opacity-75 mb-0">Tableau de bord médical</p>
        </div>
    </div>

    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-hospital-user fa-2x mb-3" style="color: var(--medical-blue);"></i>
                        <h3 class="card-title h6 text-muted">Patients suivis</h3>
                        <p class="card-text h2"><?php echo $stats['patients']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card teal-card">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-day fa-2x mb-3" style="color: var(--medical-teal);"></i>
                        <h3 class="card-title h6 text-muted">RDV aujourd'hui</h3>
                        <p class="card-text h2"><?php echo $stats['rdv_jour']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-pills fa-2x mb-3" style="color: var(--medical-blue);"></i>
                        <h3 class="card-title h6 text-muted">Traitements actifs</h3>
                        <p class="card-text h2"><?php echo $stats['traitements_actifs']; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card dashboard-card alert-card">
                    <div class="card-body text-center">
                        <i class="fas fa-notes-medical fa-2x mb-3" style="color: var(--medical-red);"></i>
                        <h3 class="card-title h6 text-muted">Symptômes (7j)</h3>
                        <p class="card-text h2"><?php echo $stats['symptomes_recents']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><i class="fas fa-calendar-check me-2"></i>Prochains rendez-vous</div>
                    <div class="card-body">
                        <?php if (empty($prochainRdv)): ?>
                            <p class="text-muted mb-0">Aucun rendez-vous à venir.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($prochainRdv as $rdv): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                        <div>
                                            <strong><?php echo htmlspecialchars($rdv['p_prenom'] . ' ' . $rdv['p_nom']); ?></strong>
                                            <div class="text-muted small"><?php echo htmlspecialchars($rdv['motif']); ?></div>
                                        </div>
                                        <span class="badge-statut badge-prevu">
                                            <?php echo date('d/m/Y', strtotime($rdv['date_rdv'])); ?> à <?php echo substr($rdv['heure_rdv'], 0, 5); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><i class="fas fa-notes-medical me-2"></i>Derniers symptômes signalés</div>
                    <div class="card-body">
                        <?php if (empty($derniersSymptomes)): ?>
                            <p class="text-muted mb-0">Aucun symptôme signalé récemment.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($derniersSymptomes as $s): ?>
                                    <li class="list-group-item px-0">
                                        <div class="d-flex justify-content-between">
                                            <strong><?php echo htmlspecialchars($s['p_prenom'] . ' ' . $s['p_nom']); ?></strong>
                                            <span class="intensite-<?php echo $s['intensite']; ?>"><?php echo ucfirst($s['intensite']); ?></span>
                                        </div>
                                        <div class="text-muted small"><?php echo htmlspecialchars($s['description']); ?> — <?php echo date('d/m/Y', strtotime($s['date_symptome'])); ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-hospital-user me-2"></i>Gérer les patients</h5>
                        <p class="card-text">Voir les fiches, ajouter un patient, consulter l'historique médical.</p>
                        <a href="modules/patients/liste.php" class="btn btn-primary">Accéder</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-calendar-plus me-2"></i>Planifier un rendez-vous</h5>
                        <p class="card-text">Créer un nouveau rendez-vous pour un patient.</p>
                        <a href="modules/rendezvous/liste.php" class="btn btn-primary">Commencer</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php else:
    // ================== DASHBOARD PATIENT ==================
    $idPatient = $user['id_patient'];

    $stmtP = $pdo->prepare("
        SELECT p.*, m.nom AS medecin_nom, m.prenom AS medecin_prenom
        FROM patients p
        LEFT JOIN medecins m ON p.id_medecin_referent = m.id_medecin
        WHERE p.id_patient = ?
    ");
    $stmtP->execute([$idPatient]);
    $patient = $stmtP->fetch();

    $stmtRdv = $pdo->prepare("
        SELECT rv.*, m.nom AS m_nom, m.prenom AS m_prenom
        FROM rendez_vous rv
        JOIN medecins m ON rv.id_medecin = m.id_medecin
        WHERE rv.id_patient = ? AND rv.statut = 'prevu'
        ORDER BY rv.date_rdv ASC LIMIT 3
    ");
    $stmtRdv->execute([$idPatient]);
    $mesRdv = $stmtRdv->fetchAll();

    $stmtTrait = $pdo->prepare("SELECT * FROM traitements WHERE id_patient = ? AND statut = 'actif' ORDER BY date_debut DESC");
    $stmtTrait->execute([$idPatient]);
    $mesTraitements = $stmtTrait->fetchAll();

    $stmtSympt = $pdo->prepare("SELECT * FROM symptomes WHERE id_patient = ? ORDER BY date_symptome DESC LIMIT 3");
    $stmtSympt->execute([$idPatient]);
    $mesSymptomes = $stmtSympt->fetchAll();
    ?>

    <div class="hero-section">
        <div class="container">
            <h1 class="display-5 text-white"><i class="fas fa-user cross-icon"></i>Bonjour, <?php echo htmlspecialchars($patient['prenom']); ?></h1>
            <p class="lead text-white opacity-75 mb-0">
                <?php if ($patient['medecin_nom']): ?>
                    Médecin référent : Dr <?php echo htmlspecialchars($patient['medecin_prenom'] . ' ' . $patient['medecin_nom']); ?>
                <?php else: ?>
                    Aucun médecin référent assigné
                <?php endif; ?>
            </p>
        </div>
    </div>

    <div class="container">
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="card dashboard-card teal-card">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-check fa-2x mb-3" style="color: var(--medical-teal);"></i>
                        <h3 class="card-title h6 text-muted">Prochains rendez-vous</h3>
                        <p class="card-text h2"><?php echo count($mesRdv); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card">
                    <div class="card-body text-center">
                        <i class="fas fa-pills fa-2x mb-3" style="color: var(--medical-blue);"></i>
                        <h3 class="card-title h6 text-muted">Traitements actifs</h3>
                        <p class="card-text h2"><?php echo count($mesTraitements); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card dashboard-card alert-card">
                    <div class="card-body text-center">
                        <i class="fas fa-notes-medical fa-2x mb-3" style="color: var(--medical-red);"></i>
                        <h3 class="card-title h6 text-muted">Symptômes signalés</h3>
                        <p class="card-text h2"><?php echo count($mesSymptomes); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><i class="fas fa-calendar-check me-2"></i>Mes prochains rendez-vous</div>
                    <div class="card-body">
                        <?php if (empty($mesRdv)): ?>
                            <p class="text-muted mb-0">Aucun rendez-vous prévu.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($mesRdv as $rdv): ?>
                                    <li class="list-group-item px-0">
                                        <strong>Dr <?php echo htmlspecialchars($rdv['m_prenom'] . ' ' . $rdv['m_nom']); ?></strong>
                                        <div class="text-muted small"><?php echo htmlspecialchars($rdv['motif']); ?> — <?php echo date('d/m/Y', strtotime($rdv['date_rdv'])); ?> à <?php echo substr($rdv['heure_rdv'], 0, 5); ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><i class="fas fa-pills me-2"></i>Mes traitements en cours</div>
                    <div class="card-body">
                        <?php if (empty($mesTraitements)): ?>
                            <p class="text-muted mb-0">Aucun traitement actif.</p>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($mesTraitements as $t): ?>
                                    <li class="list-group-item px-0">
                                        <strong><?php echo htmlspecialchars($t['nom_medicament']); ?></strong>
                                        <div class="text-muted small"><?php echo htmlspecialchars($t['dosage'] . ' — ' . $t['frequence']); ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-notes-medical me-2"></i>Signaler un symptôme</h5>
                        <p class="card-text">Ajouter une entrée à votre journal de symptômes.</p>
                        <a href="modules/symptomes/liste.php" class="btn btn-primary">Accéder</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-pills me-2"></i>Mes traitements</h5>
                        <p class="card-text">Voir mes médicaments et suivre mes prises.</p>
                        <a href="modules/traitements/liste.php" class="btn btn-primary">Accéder</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="fas fa-id-card me-2"></i>Ma fiche</h5>
                        <p class="card-text">Consulter mes informations médicales.</p>
                        <a href="modules/patients/fiche.php?id=<?php echo $idPatient; ?>" class="btn btn-primary">Consulter</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php include 'includes/footer.php'; ?>
