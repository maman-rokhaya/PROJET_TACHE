<?php
// Lire les tâches
$fichier = 'data/taches.json';
$taches = [];
if (file_exists($fichier)) {
    $json = file_get_contents($fichier);
    $taches = json_decode($json, true) ?: [];
}

// Calculer les stats
$total = count($taches);
$terminees = 0;
$enRetard = 0;
$aFaire = 0;
$enCours = 0;

foreach ($taches as $t) {
    if ($t['statut'] == 'terminee') $terminees++;
    if ($t['statut'] == 'a_faire') $aFaire++;
    if ($t['statut'] == 'en_cours') $enCours++;
    
    // Vérifier retard
    if ($t['statut'] != 'terminee' && strtotime($t['date_limite']) < time()) {
        $enRetard++;
    }
}

$pourcentage = $total > 0 ? round(($terminees / $total) * 100) : 0;
?>

<h1 class="mt-4">Tableau de bord</h1>

<div class="row mt-4">
    <!-- Total -->
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Total des tâches</h5>
                <h1><?php echo $total; ?></h1>
            </div>
            <div class="card-footer">
                <a href="?page=indexTaches" class="text-white">Voir les tâches →</a>
            </div>
        </div>
    </div>

    <!-- Terminées -->
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Tâches terminées</h5>
                <h1><?php echo $terminees; ?></h1>
            </div>
            <div class="card-footer">
                <small><?php echo $pourcentage; ?>% complétées</small>
            </div>
        </div>
    </div>

    <!-- Pourcentage -->
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>Taux de complétion</h5>
                <h1><?php echo $pourcentage; ?>%</h1>
            </div>
            <div class="card-footer">
                <small><?php echo $terminees; ?> / <?php echo $total; ?></small>
            </div>
        </div>
    </div>

    <!-- En retard -->
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5>Tâches en retard</h5>
                <h1><?php echo $enRetard; ?></h1>
            </div>
            <div class="card-footer">
                <a href="?page=indexTaches" class="text-white">Action requise →</a>
            </div>
        </div>
    </div>
</div>

<!-- Deuxième ligne -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-warning text-white">
                Tâches à faire
            </div>
            <div class="card-body text-center">
                <h1 class="text-warning"><?php echo $aFaire; ?></h1>
                <p>Tâches en attente</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Tâches en cours
            </div>
            <div class="card-body text-center">
                <h1 class="text-primary"><?php echo $enCours; ?></h1>
                <p>Tâches en traitement</p>
            </div>
        </div>
    </div>
</div>

<!-- Barre de progression -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Répartition des tâches</div>
            <div class="card-body">
                <?php if ($total > 0): ?>
                    <div class="progress" style="height: 40px;">
                        <?php
                        $pctAFaire = ($aFaire / $total) * 100;
                        $pctEnCours = ($enCours / $total) * 100;
                        $pctTerminee = ($terminees / $total) * 100;
                        ?>
                        <div class="progress-bar bg-secondary" style="width: <?php echo $pctAFaire; ?>%">
                            <?php if($pctAFaire > 10) echo "À faire"; ?>
                        </div>
                        <div class="progress-bar bg-warning" style="width: <?php echo $pctEnCours; ?>%">
                            <?php if($pctEnCours > 10) echo "En cours"; ?>
                        </div>
                        <div class="progress-bar bg-success" style="width: <?php echo $pctTerminee; ?>%">
                            <?php if($pctTerminee > 10) echo "Terminées"; ?>
                        </div>
                    </div>
                    <div class="mt-3 text-center">
                        <span class="badge bg-secondary">À faire: <?php echo $aFaire; ?></span>
                        <span class="badge bg-warning">En cours: <?php echo $enCours; ?></span>
                        <span class="badge bg-success">Terminées: <?php echo $terminees; ?></span>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Aucune tâche pour le moment</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Alerte retard -->
<?php if ($enRetard > 0): ?>
<div class="row mt-4">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <h4>⚠️ Attention !</h4>
            <p>Vous avez <strong><?php echo $enRetard; ?> tâche(s)</strong> en retard !</p>
            <a href="?page=indexTaches" class="btn btn-danger">Voir les tâches</a>
        </div>
    </div>
</div>
<?php endif; ?>
