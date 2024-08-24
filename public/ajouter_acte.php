<?php
// ajouter_acte.php

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

if (!isLoggedIn() || (getCurrentUser()['role'] !== 'administrateur' && getCurrentUser()['role'] !== 'superviseur')) {
    header('Location: index.php');
    exit;
}

$currentUser = getCurrentUser();
$arrondissements = getArrondissements(); // Cette fonction est implémenté dans auth.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'];
    $arrondissement_id = $_POST['arrondissement_id'];

    // Créer un tableau des détails en fonction du type d'acte :: c'est pour le json de détails
    $details = [];
    switch ($type) {
        case 'naissance':
            $details = [
                'nom_enfant' => $_POST['nom_enfant'],
                'date_naissance' => $_POST['date_naissance'],
                'lieu_naissance' => $_POST['lieu_naissance'],
                'nom_pere' => $_POST['nom_pere'],
                'nom_mere' => $_POST['nom_mere'],
                'profession_mere' => $_POST['profession_mere'],
                'profession_pere' => $_POST['profession_pere'],
            ];
            break;
        case 'mariage':
            $details = [
                'nom_epoux' => $_POST['nom_epoux'],
                'nom_epouse' => $_POST['nom_epouse'],
                'profession_epouse' => $_POST['profession_epouse'],
                'profession_epoux' => $_POST['profession_epoux'],
                'date_mariage' => $_POST['date_mariage'],
                'lieu_mariage' => $_POST['lieu_mariage'],
            ];
            break;
        case 'décès':
            $details = [
                'nom_defunt' => $_POST['nom_defunt'],
                'profession_defunt' => $_POST['profession_defunt'],
                'date_deces' => $_POST['date_deces'],
                'lieu_deces' => $_POST['lieu_deces'],
                'cause_deces' => $_POST['cause_deces'],
                'nationalite' => $_POST['nationalite'],
            ];
            break;
    }

    // Convertir les détails en JSON
    $body = json_encode($details);

    $stmt = $pdo->prepare("INSERT INTO records (type, arrondissement_id, body) VALUES (?, ?, ?)");
    if ($stmt->execute([$type, $arrondissement_id, $body])) {
        header('Location: dashboard.php?tab=actes');
        exit;
    } else {
        $error = "Erreur lors de l'enregistrement de l'acte.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un acte</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script>
        function toggleFields(type) {
            document.getElementById('fields-naissance').style.display = (type === 'naissance') ? 'block' : 'none';
            document.getElementById('fields-mariage').style.display = (type === 'mariage') ? 'block' : 'none';
            document.getElementById('fields-deces').style.display = (type === 'décès') ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Ajouter un acte</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="type">Type d'acte</label>
                <select name="type" id="type" class="form-control" required onchange="toggleFields(this.value)">
                    <option value="naissance">Naissance</option>
                    <option value="mariage">Mariage</option>
                    <option value="décès">Décès</option>
                </select>
            </div>
            <div class="form-group">
                <label for="arrondissement_id">Arrondissement</label>
                <select name="arrondissement_id" id="arrondissement_id" class="form-control" required>
                    <?php foreach ($arrondissements as $arrondissement): ?>
                        <option value="<?= $arrondissement['id'] ?>"><?= htmlspecialchars($arrondissement['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Champs spécifiques pour chaque type d'acte -->
            <div id="fields-naissance" style="display: none;">
                <h3>Informations de Naissance</h3>
                <div class="form-group">
                    <label for="nom_enfant">Nom de l'enfant</label>
                    <input type="text" name="nom_enfant" id="nom_enfant" class="form-control">
                </div>
                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" name="date_naissance" id="date_naissance" class="form-control">
                </div>
                <div class="form-group">
                    <label for="lieu_naissance">Lieu de naissance</label>
                    <input type="text" name="lieu_naissance" id="lieu_naissance" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nom_pere">Nom du père</label>
                    <input type="text" name="nom_pere" id="nom_pere" class="form-control">
                </div>
                <div class="form-group">
                    <label for="profession_pere">Profession du Père</label>
                    <input type="text" name="profession_pere" id="profession_pere" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nom_mere">Nom de la mère</label>
                    <input type="text" name="nom_mere" id="nom_mere" class="form-control">
                </div>
                <div class="form-group">
                    <label for="profession_mere">Profession de la mère</label>
                    <input type="text" name="profession_mere" id="profession_mere" class="form-control">
                </div>
            </div>

            <div id="fields-mariage" style="display: none;">
                <h3>Informations de Mariage</h3>
                <div class="form-group">
                    <label for="nom_epoux">Nom de l'époux</label>
                    <input type="text" name="nom_epoux" id="nom_epoux" class="form-control">
                </div>
                <div class="form-group">
                    <label for="profession_epoux">Profession de l'époux</label>
                    <input type="text" name="profession_epoux" id="profession_epoux" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nom_epouse">Nom de l'épouse</label>
                    <input type="text" name="nom_epouse" id="nom_epouse" class="form-control">
                </div>
                <div class="form-group">
                    <label for="profession_epouse">Profession de l'épouse</label>
                    <input type="text" name="profession_epouse" id="profession_epouse" class="form-control">
                </div>
                <div class="form-group">
                    <label for="date_mariage">Date de mariage</label>
                    <input type="date" name="date_mariage" id="date_mariage" class="form-control">
                </div>
                <div class="form-group">
                    <label for="lieu_mariage">Lieu de mariage</label>
                    <input type="text" name="lieu_mariage" id="lieu_mariage" class="form-control">
                </div>
            </div>

            <div id="fields-deces" style="display: none;">
                <h3>Informations de Décès</h3>
                <div class="form-group">
                    <label for="nom_defunt">Nom du défunt</label>
                    <input type="text" name="nom_defunt" id="nom_defunt" class="form-control">
                </div>
                <div class="form-group">
                    <label for="profession_defunt">Profession du défunt</label>
                    <input type="text" name="profession_defunt" id="profession_defunt" class="form-control">
                </div>
                <div class="form-group">
                    <label for="date_deces">Date de décès</label>
                    <input type="date" name="date_deces" id="date_deces" class="form-control">
                </div>
                <div class="form-group">
                    <label for="lieu_deces">Lieu de décès</label>
                    <input type="text" name="lieu_deces" id="lieu_deces" class="form-control">
                </div>
                <div class="form-group">
                    <label for="cause_deces">Cause du décès</label>
                    <input type="text" name="cause_deces" id="cause_deces" class="form-control">
                </div>
                <div class="form-group">
                    <label for="nationalite">Nationalité</label>
                    <input type="text" name="nationalite" id="nationalite" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Enregistrer l'acte</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            toggleFields(document.getElementById('type').value);
        });
    </script>
</body>
</html>
