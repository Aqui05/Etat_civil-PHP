<?php
// /includes/auth.php
session_start();
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}
function login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}
function logout() {
    session_unset();
    session_destroy();
}
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur';
}
function registerUser($username, $password, $role, $arrondissement_id) {
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, arrondissement_id) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$username, $hashedPassword, $role, $arrondissement_id]);
}

// Fonction pour récupérer le nom de l'arrondissement par son ID
function getArrondissementName($arrondissement_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT name FROM arrondissements WHERE id = ?");
    $stmt->execute([$arrondissement_id]);
    $arrondissement = $stmt->fetch();
    return $arrondissement ? $arrondissement['name'] : 'N/A';
}


function getArrondissements() {
    global $pdo;
    $stmt = $pdo->query("SELECT id, name FROM arrondissements");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>