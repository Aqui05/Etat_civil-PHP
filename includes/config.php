<?php
// Connexion à la base de données
$host = 'localhost';
$db   = 'kamgoko_test';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

// Création des tables
$sql = "
CREATE TABLE IF NOT EXISTS arrondissements (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('administrateur', 'superviseur', 'analytics') NOT NULL,
    arrondissement_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (arrondissement_id) REFERENCES arrondissements(id)
);

CREATE TABLE IF NOT EXISTS records (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    record_info_id INTEGER,
    type ENUM('naissance', 'mariage', 'décès') NOT NULL,
    arrondissement_id INTEGER,
    body JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (arrondissement_id) REFERENCES arrondissements(id),
    UNIQUE KEY unique_record (type, arrondissement_id, body) -- Ensure uniqueness
);
";

$pdo->exec($sql);

// Insertion de données par défaut
$defaultArrondissements = [
    ['name' => 'akpakpa'],
    ['name' => 'Agla'],
    ['name' => 'Cadjèhoun'],
    ['name' => 'Cocotomey'],
    ['name' => 'Fidjrossè'],
    ['name' => 'Gbegamey'],
    ['name' => 'Gbénonkpo'],
    ['name' => 'Hindé'],
    ['name' => 'Ladji'],
    ['name' => 'Saint-Michel'],
    ['name' => 'Vèdoko'],
    ['name' => 'Sikècodji'],
    ['name' => 'Abattoir'],
];

$stmt = $pdo->prepare("INSERT IGNORE INTO arrondissements (name) VALUES (:name)");
foreach ($defaultArrondissements as $arrondissement) {
    $stmt->execute($arrondissement);
}

// Insertion d'utilisateurs
$defaultUsers = [
    [
        'username' => 'admin',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
        'role' => 'administrateur',
        'arrondissement_id' => 1
    ],
];

$stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password, role, arrondissement_id) VALUES (:username, :password, :role, :arrondissement_id)");
foreach ($defaultUsers as $user) {
    $stmt->execute($user);
}

// Insertion d'actes avec JSON

$records = [
  [
    'type' => 'naissance',
    'arrondissement_id' => 1,
    'body' => json_encode([
      'nom' => 'Dupont',
      'prenom' => 'Jean',
      'date_naissance' => '2023-05-15',
      'lieu_naissance' => 'Hôpital Central',
      'parents' => [
        'pere' => 'Dupont Pierre',
        'mere' => 'Martin Sophie'
      ]
    ])
    ],
  ];
  
  $stmt = $pdo->prepare("
    INSERT IGNORE INTO records (type, arrondissement_id, body)
    VALUES (:type, :arrondissement_id, :body)
  ");
  
  foreach ($records as $record) {
    $stmt->execute($record);
  }
?>



