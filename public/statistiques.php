<?php

// Assurez-vous que la connexion PDO est correctement établie
require '../includes/config.php';

// Fonction pour récupérer le nombre total d'enregistrements
function getTotalRecords() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM records");
    return $stmt->fetchColumn();
}

// Fonction pour récupérer le nombre total d'enregistrements par type
function getTotalRecordsByType() {
    global $pdo;
    $stmt = $pdo->query("SELECT type, COUNT(*) as count FROM records GROUP BY type");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer le nombre total d'enregistrements par arrondissement
function getTotalRecordsByArrondissement() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT a.name, COUNT(r.id) as count 
        FROM arrondissements a 
        LEFT JOIN records r ON a.id = r.arrondissement_id 
        GROUP BY a.name
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer le nombre total d'utilisateurs
function getTotalUsers() {
    global $pdo;
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    return $stmt->fetchColumn();
}

// Fonction pour récupérer le nombre total d'utilisateurs par arrondissement
function getTotalUsersByArrondissement() {
    global $pdo;
    $stmt = $pdo->query("
        SELECT a.name, COUNT(u.id) as count 
        FROM arrondissements a 
        LEFT JOIN users u ON a.id = u.arrondissement_id 
        GROUP BY a.name
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fonction pour récupérer le nombre total d'enregistrements dans une période donnée
function getTotalRecordsByPeriod($startDate, $endDate) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM records WHERE created_at BETWEEN ? AND ?");
    $stmt->execute([$startDate, $endDate]);
    return $stmt->fetchColumn();
}

// Récupérer les statistiques
$totalRecords = getTotalRecords();
$totalRecordsByType = getTotalRecordsByType();
$totalRecordsByArrondissement = getTotalRecordsByArrondissement();
$totalUsers = getTotalUsers();
$totalUsersByArrondissement = getTotalUsersByArrondissement();

// Pour les statistiques par période, nous utiliserons le mois en cours
$startDate = date('Y-m-01'); // Premier jour du mois en cours
$endDate = date('Y-m-t'); // Dernier jour du mois en cours
$totalRecordsByPeriod = getTotalRecordsByPeriod($startDate, $endDate);

?>

<div class="container mt-4">
    <h2>Statistiques</h2>
    
    <div class="row">
        <!-- Total des enregistrements -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total des enregistrements</h5>
                    <p class="card-text"><?php echo htmlspecialchars($totalRecords); ?></p>
                </div>
            </div>
        </div>

        <!-- Total par type -->
        <?php foreach ($totalRecordsByType as $recordType): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total <?php echo htmlspecialchars(ucfirst($recordType['type'])); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($recordType['count']); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Total des utilisateurs -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total des utilisateurs</h5>
                    <p class="card-text"><?php echo htmlspecialchars($totalUsers); ?></p>
                </div>
            </div>
        </div>

        <!-- Total par période -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total pour <?php echo htmlspecialchars(date('F Y')); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($totalRecordsByPeriod); ?></p>
                </div>
            </div>
        </div>
    </div>

    <h3 class="mt-4">Statistiques par arrondissement</h3>
    <div class="row">
        <!-- Total enregistrements par arrondissement -->
        <?php foreach ($totalRecordsByArrondissement as $arrondissement): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($arrondissement['name']); ?></h5>
                    <p class="card-text">Enregistrements : <?php echo htmlspecialchars($arrondissement['count']); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <h3 class="mt-4">Utilisateurs par arrondissement</h3>
    <div class="row">
        <!-- Total utilisateurs par arrondissement -->
        <?php foreach ($totalUsersByArrondissement as $arrondissement): ?>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($arrondissement['name']); ?></h5>
                    <p class="card-text">Utilisateurs : <?php echo htmlspecialchars($arrondissement['count']); ?></p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
