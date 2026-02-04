<?php
// Fichier des tâches
$fichier = 'data/taches.json';

// Lire les tâches
function lireTaches() {
    global $fichier;
    if (file_exists($fichier)) {
        $json = file_get_contents($fichier);
        return json_decode($json, true) ?: [];
    }
    return [];
}

// Sauvegarder les tâches
function sauvegarderTaches($taches) {
    global $fichier;
    file_put_contents($fichier, json_encode($taches, JSON_PRETTY_PRINT));
}

// AJOUTER une tâche
if (isset($_POST['ajouter'])) {
    $taches = lireTaches();
    $nouvelle = [
        'id' => time(),
        'titre' => $_POST['titre'],
        'description' => $_POST['description'],
        'priorite' => $_POST['priorite'],
        'statut' => 'a_faire',
        'date_creation' => date('Y-m-d H:i:s'),
        'date_limite' => $_POST['date_limite'],
        'responsable' => $_POST['responsable']
    ];
    $taches[] = $nouvelle;
    sauvegarderTaches($taches);
    header('Location: ?page=indexTaches&msg=ajout');
    exit;
}

// MODIFIER une tâche
if (isset($_POST['modifier'])) {
    $taches = lireTaches();
    foreach ($taches as $key => $tache) {
        if ($tache['id'] == $_POST['id']) {
            $taches[$key]['titre'] = $_POST['titre'];
            $taches[$key]['description'] = $_POST['description'];
            $taches[$key]['priorite'] = $_POST['priorite'];
            $taches[$key]['date_limite'] = $_POST['date_limite'];
            $taches[$key]['responsable'] = $_POST['responsable'];
        }
    }
    sauvegarderTaches($taches);
    header('Location: ?page=indexTaches&msg=modif');
    exit;
}

// CHANGER le statut
if (isset($_GET['changer']) && isset($_GET['id'])) {
    $taches = lireTaches();
    foreach ($taches as $key => $tache) {
        if ($tache['id'] == $_GET['id']) {
            if ($tache['statut'] == 'a_faire') {
                $taches[$key]['statut'] = 'en_cours';
            } elseif ($tache['statut'] == 'en_cours') {
                $taches[$key]['statut'] = 'terminee';
            }
        }
    }
    sauvegarderTaches($taches);
    header('Location: ?page=indexTaches');
    exit;
}

// SUPPRIMER une tâche
if (isset($_GET['supprimer']) && isset($_GET['id'])) {
    $taches = lireTaches();
    $taches = array_filter($taches, function($t) {
        return $t['id'] != $_GET['id'];
    });
    sauvegarderTaches(array_values($taches));
    header('Location: ?page=indexTaches&msg=supprime');
    exit;
}

// Récupérer les tâches
$taches = lireTaches();

// FILTRER les tâches
$recherche = $_GET['recherche'] ?? '';
$statut = $_GET['statut'] ?? '';
$priorite = $_GET['priorite'] ?? '';

if ($recherche) {
    $taches = array_filter($taches, function($t) use ($recherche) {
        return stripos($t['titre'], $recherche) !== false || stripos($t['description'], $recherche) !== false;
    });
}
if ($statut) {
    $taches = array_filter($taches, function($t) use ($statut) {
        return $t['statut'] == $statut;
    });
}
if ($priorite) {
    $taches = array_filter($taches, function($t) use ($priorite) {
        return $t['priorite'] == $priorite;
    });
}

// Tâche à modifier ?
$modif = null;
if (isset($_GET['modif_id'])) {
    foreach ($taches as $t) {
        if ($t['id'] == $_GET['modif_id']) {
            $modif = $t;
            break;
        }
    }
}

// Messages
$msg = '';
if (isset($_GET['msg'])) {
    if ($_GET['msg'] == 'ajout') $msg = '<div class="alert alert-success">Tâche ajoutée !</div>';
    if ($_GET['msg'] == 'modif') $msg = '<div class="alert alert-success">Tâche modifiée !</div>';
    if ($_GET['msg'] == 'supprime') $msg = '<div class="alert alert-success">Tâche supprimée !</div>';
}
?>

<h1 class="mt-4">Gestion des Tâches</h1>

<?php echo $msg; ?>

<div class="row mt-3">
    <!-- FORMULAIRE -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <?php echo $modif ? 'MODIFIER' : 'AJOUTER'; ?> UNE TÂCHE
            </div>
            <div class="card-body">
                <form method="post">
                    <?php if ($modif): ?>
                        <input type="hidden" name="id" value="<?php echo $modif['id']; ?>">
                    <?php endif; ?>
                    
                    <div class="mb-2">
                        <label>Titre *</label>
                        <input type="text" name="titre" class="form-control" required 
                               value="<?php echo $modif ? $modif['titre'] : ''; ?>">
                    </div>
                    
                    <div class="mb-2">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo $modif ? $modif['description'] : ''; ?></textarea>
                    </div>
                    
                    <div class="mb-2">
                        <label>Priorité *</label>
                        <select name="priorite" class="form-control" required>
                            <option value="basse" <?php echo ($modif && $modif['priorite']=='basse')?'selected':''; ?>>Basse</option>
                            <option value="moyenne" <?php echo (!$modif || $modif['priorite']=='moyenne')?'selected':''; ?>>Moyenne</option>
                            <option value="haute" <?php echo ($modif && $modif['priorite']=='haute')?'selected':''; ?>>Haute</option>
                        </select>
                    </div>
                    
                    <div class="mb-2">
                        <label>Date limite *</label>
                        <input type="date" name="date_limite" class="form-control" required 
                               value="<?php echo $modif ? date('Y-m-d', strtotime($modif['date_limite'])) : ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label>Responsable *</label>
                        <input type="text" name="responsable" class="form-control" required 
                               placeholder="Nom Prénom"
                               value="<?php echo $modif ? $modif['responsable'] : ''; ?>">
                    </div>

                    <button type="submit" name="<?php echo $modif ? 'modifier' : 'ajouter'; ?>" class="btn btn-primary w-100">
                        <?php echo $modif ? 'Modifier' : 'Ajouter'; ?>
                    </button>
                    
                    <?php if ($modif): ?>
                        <a href="?page=indexTaches" class="btn btn-secondary w-100 mt-2">Annuler</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <!-- LISTE DES TÂCHES -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                LISTE DES TÂCHES (<?php echo count($taches); ?>)
            </div>
            <div class="card-body">
                <!-- FILTRES -->
                <form method="get" class="row mb-3">
                    <input type="hidden" name="page" value="indexTaches">
                    <div class="col-md-4">
                        <input type="text" name="recherche" class="form-control" placeholder="Rechercher..." 
                               value="<?php echo $recherche; ?>">
                    </div>
                    <div class="col-md-3">
                        <select name="statut" class="form-control">
                            <option value="">Tous statuts</option>
                            <option value="a_faire" <?php echo $statut=='a_faire'?'selected':''; ?>>À faire</option>
                            <option value="en_cours" <?php echo $statut=='en_cours'?'selected':''; ?>>En cours</option>
                            <option value="terminee" <?php echo $statut=='terminee'?'selected':''; ?>>Terminée</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="priorite" class="form-control">
                            <option value="">Toutes priorités</option>
                            <option value="basse" <?php echo $priorite=='basse'?'selected':''; ?>>Basse</option>
                            <option value="moyenne" <?php echo $priorite=='moyenne'?'selected':''; ?>>Moyenne</option>
                            <option value="haute" <?php echo $priorite=='haute'?'selected':''; ?>>Haute</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100">Filtrer</button>
                    </div>
                </form>

                <!-- TABLE -->
                <?php if (empty($taches)): ?>
                    <div class="alert alert-info">Aucune tâche</div>
                <?php else: ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Responsable</th>
                                <th>Priorité</th>
                                <th>Statut</th>
                                <th>Date limite</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($taches as $t): 
                                // Vérifier si en retard
                                $enRetard = ($t['statut'] != 'terminee' && strtotime($t['date_limite']) < time());
                            ?>
                                <tr class="<?php echo $enRetard ? 'table-danger' : ''; ?>">
                                    <td>
                                        <strong><?php echo $t['titre']; ?></strong>
                                        <?php if ($enRetard): ?>
                                            <span class="badge bg-danger">EN RETARD</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $t['responsable']; ?></td>
                                    <td>
                                        <?php
                                        $color = $t['priorite']=='haute' ? 'danger' : ($t['priorite']=='moyenne' ? 'warning' : 'info');
                                        echo '<span class="badge bg-'.$color.'">'.ucfirst($t['priorite']).'</span>';
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        $statuts = ['a_faire'=>'À faire', 'en_cours'=>'En cours', 'terminee'=>'Terminée'];
                                        $colors = ['a_faire'=>'secondary', 'en_cours'=>'warning', 'terminee'=>'success'];
                                        echo '<span class="badge bg-'.$colors[$t['statut']].'">'.$statuts[$t['statut']].'</span>';
                                        ?>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($t['date_limite'])); ?></td>
                                    <td>
                                        <!-- Changer statut -->
                                        <?php if ($t['statut'] != 'terminee'): ?>
                                            <a href="?page=indexTaches&changer=1&id=<?php echo $t['id']; ?>" 
                                               class="btn btn-sm btn-info" title="Changer statut">
                                                ➡️
                                            </a>
                                        <?php endif; ?>
                                        
                                        <!-- Modifier -->
                                        <?php if ($t['statut'] != 'terminee'): ?>
                                            <a href="?page=indexTaches&modif_id=<?php echo $t['id']; ?>" 
                                               class="btn btn-sm btn-warning" title="Modifier">
                                                ✏️
                                            </a>
                                        <?php endif; ?>
                                        
                                        <!-- Supprimer -->
                                        <a href="?page=indexTaches&supprimer=1&id=<?php echo $t['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('Supprimer cette tâche ?')"
                                           title="Supprimer">
                                            🗑️
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
