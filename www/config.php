<?php
/**
 * Portable MariaDB Database Connection Configuration
 * Communicates flawlessly across both Windows and Linux loops.
 */

$host    = '127.0.0.1';
$db      = 'test';
$user    = 'root';
$pass    = '';
$port    = '3306';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Instantiate our unified global database handler object variable
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Capture any network connection blocks or permission locks gracefully
    die("<p style='color: #ff5555; font-family: sans-serif;'>❌ Relational Database connection failure: " . $e->getMessage() . "</p>");
}
