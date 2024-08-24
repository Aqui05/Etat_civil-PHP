<?php
// /public/dashboard.php
require '../includes/config.php';
require '../includes/auth.php';

if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if (isset($_POST['logout'])) {
    logout();
    header('Location: index.php');
    exit;
}

$arrondissements = getArrondissements();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .logout-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <?php include '../templates/header.php'; ?>

    <main class="container mt-4">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="statistiques-tab" data-toggle="tab" href="#statistiques" role="tab" aria-controls="statistiques" aria-selected="true">Statistiques</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="actes-tab" data-toggle="tab" href="#actes" role="tab" aria-controls="actes" aria-selected="false">Actes</a>
            </li>
            <?php if (isAdmin()): ?>
            <li class="nav-item">
                <a class="nav-link" id="utilisateurs-tab" data-toggle="tab" href="#utilisateurs" role="tab" aria-controls="utilisateurs" aria-selected="false">Utilisateurs</a>
            </li>
            <?php endif; ?>
        </ul>
        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="statistiques" role="tabpanel" aria-labelledby="statistiques-tab">
                <?php include 'statistiques.php'; ?>
            </div>
            <div class="tab-pane fade" id="actes" role="tabpanel" aria-labelledby="actes-tab">
                <?php include 'actes.php'; ?>
            </div>
            <?php if (isAdmin()): ?>
            <div class="tab-pane fade" id="utilisateurs" role="tabpanel" aria-labelledby="utilisateurs-tab">
                <?php include 'utilisateurs.php'; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../templates/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>