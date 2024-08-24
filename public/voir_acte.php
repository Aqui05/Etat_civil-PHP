<?php
// /public/voir_acte.php
require '../includes/config.php';
require '../includes/auth.php';

function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Récupérer l'ID de l'acte depuis l'URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Erreur : ID d'acte invalide.");
}

$acteId = (int)$_GET['id'];

// Vérifier que l'utilisateur a le droit de voir cet acte
$currentUser = getCurrentUser();
$role = $currentUser['role'];
$userArrondissementId = $currentUser['arrondissement_id'];

$sql = "SELECT r.*, a.name AS arrondissement_name 
        FROM records r 
        LEFT JOIN arrondissements a ON r.arrondissement_id = a.id 
        WHERE r.id = :id";

if ($role !== 'administrateur') {
    $sql .= " AND r.arrondissement_id = :arrondissement_id";
}

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id', $acteId, PDO::PARAM_INT);
if ($role !== 'administrateur') {
    $stmt->bindParam(':arrondissement_id', $userArrondissementId, PDO::PARAM_INT);
}
$stmt->execute();

$record = $stmt->fetch();

if (!$record) {
    die("Erreur : Acte non trouvé ou accès interdit.");
}

// JSOn décodage
$jsonData = json_decode($record['body'], true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("Erreur : Les données JSON sont invalides.");
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Acte</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <?php include '../templates/header.php'; ?>
    
    <main class="container mt-4">
        <h2>Détails de l'Acte</h2>
        <a href="actes.php" class="btn btn-secondary mb-3">Retour à la liste</a>

        <a href="telecharger_pdf.php?id=<?= htmlspecialchars($record['id']) ?>" class="btn btn-sm btn-secondary">Télécharger PDF</a>
        
        <div class="card">
            <div class="card-header">
                Acte ID : <?= htmlspecialchars($record['id']) ?>
            </div>
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($record['type']) ?></h5>
                <p class="card-text"><strong>Arrondissement :</strong> <?= htmlspecialchars($record['arrondissement_name']) ?></p>
                <p class="card-text"><strong>Date de création :</strong> <?= htmlspecialchars($record['created_at']) ?></p>
                
                <!-- JSON affichage -->
                <?php if ($jsonData): ?>
                    <h6>Détails de l'acte :</h6>
                    <?php foreach ($jsonData as $key => $value): ?>
                        <p class="card-text"><strong><?= htmlspecialchars($key) ?>:</strong> <?= htmlspecialchars($value) ?></p>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="card-text">Aucun détail disponible.</p>
                <?php endif; ?>
            </div>
            <div class="card-footer text-body-secondary">

            <a href="telecharger_pdf.php?id=<?= htmlspecialchars($record['id']) ?>" class="btn btn-sm btn-secondary">Télécharger PDF</a>
            </div>
        </div>

        <a href="telecharger_pdf.php?id=<?= htmlspecialchars($record['id']) ?>" class="btn btn-sm btn-secondary">Télécharger PDF</a>
    </main>

    <?php include '../templates/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
