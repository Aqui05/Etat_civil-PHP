<!-- /templates/header.php -->
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">KAMGOKO Test</a>
        <?php if (isLoggedIn()): // Vérifie si l'utilisateur est connecté ?>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" id="statistiques-tab" data-toggle="tab" href="#statistiques" role="tab" aria-controls="statistiques">Statistiques</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="actes-tab" data-toggle="tab" href="#actes" role="tab" aria-controls="actes">Actes</a>
                    </li>
                    <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link" id="utilisateurs-tab" data-toggle="tab" href="#utilisateurs" role="tab" aria-controls="utilisateurs">Utilisateurs</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <form method="POST" class="form-inline">
                <button type="submit" name="logout" class="btn btn-outline-danger">Déconnexion</button>
            </form>
        <?php endif; ?>
    </nav>
</header>
