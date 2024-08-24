
<?php
if (isset($_POST['register'])) {
    $newUsername = $_POST['new_username'];
    $newPassword = $_POST['new_password'];
    $newRole = $_POST['new_role'];
    $newArrondissement = $_POST['new_arrondissement'];
    if (registerUser($newUsername, $newPassword, $newRole, $newArrondissement)) {
        echo '<div class="alert alert-success">Nouvel utilisateur ajouté avec succès.</div>';
    } else {
        echo '<div class="alert alert-danger">Erreur lors de lajout de lutilisateur.</div>';
    }

    // Traitement du formulaire de modification d'utilisateur
if (isset($_POST['edit_user'])) {
    $userId = $_POST['edit_user_id'];
    $username = $_POST['edit_username'];
    $role = $_POST['edit_role'];
    $arrondissementId = $_POST['edit_arrondissement'];
    $newPassword = $_POST['edit_password'];

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();

    if ($user) {
        // Préparer la requête de mise à jour
        if (!empty($newPassword)) {
            // Si un nouveau mot de passe est fourni, le hacher
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $updateStmt = $pdo->prepare("UPDATE users SET username = ?, password = ?, role = ?, arrondissement_id = ? WHERE id = ?");
            $updateSuccess = $updateStmt->execute([$username, $hashedPassword, $role, $arrondissementId, $userId]);
        } else {
            // Sinon, ne pas changer le mot de passe
            $updateStmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, arrondissement_id = ? WHERE id = ?");
            $updateSuccess = $updateStmt->execute([$username, $role, $arrondissementId, $userId]);
        }

        if ($updateSuccess) {
            echo '<div class="alert alert-success">Utilisateur mis à jour avec succès.</div>';
        } else {
            echo '<div class="alert alert-danger">Erreur lors de la mise à jour de l\'utilisateur.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">Utilisateur introuvable.</div>';
    }
}
}


// les utilisateurs

    $sql = "SELECT * FROM users WHERE role != 'administrateur'" ;
    $stmt = $pdo->prepare($sql);

$stmt->execute();
$users = $stmt->fetchAll();
?>


<h3 class="mt-3">Gestion des utilisateurs</h3>
<button class="btn btn-primary mb-3" data-toggle="modal" data-target="#registerModal">Ajouter un utilisateur</button>
<!-- Liste des utilisateurs et options de gestion -->


<!-- Table des utilisateurs -->
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom d'utilisateur</th>
            <th>Rôle</th>
            <th>Arrondissement</th>
            <th>Date de création</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['role']) ?></td>
                <td><?= htmlspecialchars(getArrondissementName($user['arrondissement_id'])) ?></td>
                <td><?= htmlspecialchars($user['created_at']) ?></td>
                <td>
                    <!-- Bouton Modifier -->
                    <button 
                        class="btn btn-sm btn-warning editUserBtn" 
                        data-id="<?= $user['id'] ?>"
                        data-username="<?= htmlspecialchars($user['username']) ?>"
                        data-role="<?= $user['role'] ?>"
                        data-arrondissement="<?= $user['arrondissement_id'] ?>"
                        data-toggle="modal" 
                        data-target="#editUserModal"
                    >
                        Modifier
                    </button>

                    <!-- Formulaire de suppression -->
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>



<!-- Modal d'ajout d'utilisateur -->
<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registerModalLabel">Ajouter un utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="new_username">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="new_username" name="new_username" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_role">Rôle</label>
                        <select class="form-control" id="new_role" name="new_role" required>
                            <option value="superviseur">Superviseur</option>
                            <option value="analytics">Analyste</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="new_arrondissement">Arrondissement</label>
                        <select class="form-control" id="new_arrondissement" name="new_arrondissement" required>
                            <?php foreach ($arrondissements as $arrondissement): ?>
                                <option value="<?php echo $arrondissement['id']; ?>"><?php echo htmlspecialchars($arrondissement['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary">Ajouter l'utilisateur</button>
                </form>
            </div>
        </div>
    </div>
</div>




<!-- Modal de modification d'utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">Modifier l'utilisateur</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Champ caché pour l'ID de l'utilisateur -->
                    <input type="hidden" name="edit_user_id" id="edit_user_id">

                    <div class="form-group">
                        <label for="edit_username">Nom d'utilisateur</label>
                        <input type="text" class="form-control" id="edit_username" name="edit_username" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_password">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="edit_password" name="edit_password" placeholder="Laissez vide pour ne pas changer">
                    </div>
                    <div class="form-group">
                        <label for="edit_role">Rôle</label>
                        <select class="form-control" id="edit_role" name="edit_role" required>
                            <option value="superviseur">Superviseur</option>
                            <option value="analyste">Analyste</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit_arrondissement">Arrondissement</label>
                        <select class="form-control" id="edit_arrondissement" name="edit_arrondissement" required>
                            <?php foreach ($arrondissements as $arrondissement): ?>
                                <option value="<?= $arrondissement['id']; ?>"><?= htmlspecialchars($arrondissement['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" name="edit_user" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- Script pour remplir le modal de modification d'utilisateur -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editButtons = document.querySelectorAll('.editUserBtn');
        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                const username = this.getAttribute('data-username');
                const role = this.getAttribute('data-role');
                const arrondissementId = this.getAttribute('data-arrondissement');

                // Remplir les champs du modal par défaut
                document.getElementById('edit_user_id').value = userId;
                document.getElementById('edit_username').value = username;
                document.getElementById('edit_role').value = role;
                document.getElementById('edit_arrondissement').value = arrondissementId;
            });
        });
    });
</script>
