<?php
// config/database.php
// Paramètres de connexion à la base de données (MAMP)

define('DB_HOST', 'localhost');
define('DB_NAME', 'patient_tracker');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_PORT', '8889'); // Port MySQL par défaut de MAMP (souvent 3306 hors MAMP)

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]
    );
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
