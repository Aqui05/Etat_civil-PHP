<?php
// actes.php

function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch();
    }
    return null;
}

// Doit être connecter
if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Récupération rôle
$currentUser = getCurrentUser();
if (!$currentUser) {
    die("Erreur : Utilisateur non trouvé.");
}

$role = $currentUser['role'];
$userArrondissementId = $currentUser['arrondissement_id'];


//Récupération acte =: l'admin verra tous les actes contrairement aux autres.
if ($role === 'administrateur') {
    $sql = "SELECT r.*, a.name AS arrondissement_name FROM records r 
            LEFT JOIN arrondissements a ON r.arrondissement_id = a.id";
    $stmt = $pdo->prepare($sql);
} else {
    $sql = "SELECT r.*, a.name AS arrondissement_name FROM records r 
            LEFT JOIN arrondissements a ON r.arrondissement_id = a.id 
            WHERE r.arrondissement_id = :arrondissement_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':arrondissement_id', $userArrondissementId, PDO::PARAM_INT);
}

$stmt->execute();
$records = $stmt->fetchAll();


// Traitement de la suppression
if (isset($_POST['delete_id']) && ($role === 'administrateur' || $role === 'superviseur')) {
    $deleteId = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM records WHERE id = ?");
    if ($stmt->execute([$deleteId])) {
        echo "<div class='alert alert-success'>Acte supprimé avec succès.</div>";
    } else {
        echo "<div class='alert alert-danger'>Erreur lors de la suppression de l'acte.</div>";
    }
}

?>

<h2>Liste des Actes</h2>

<?php if ($role === 'administrateur' || $role === 'superviseur'): ?>
    <a href="ajouter_acte.php" class="btn btn-primary mb-3">Enregistrer un acte</a>
<?php endif; ?>

<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Type</th>
            <th>Arrondissement</th>
            <th>Date de création</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($records as $record): ?>
            <tr>
                <td><?= htmlspecialchars($record['id']) ?></td>
                <td><?= htmlspecialchars($record['type']) ?></td>
                <td><?= htmlspecialchars($record['arrondissement_name']) ?></td>
                <td><?= htmlspecialchars($record['created_at']) ?></td>
                <td>
                    <a href="voir_acte.php?id=<?= $record['id'] ?>" class="btn btn-sm btn-info">Voir</a>
                    <?php if ($role !== 'analytics'): ?>
                        <a href="modifier_acte.php?id=<?= $record['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="delete_id" value="<?= $record['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet acte ?');">Supprimer</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>