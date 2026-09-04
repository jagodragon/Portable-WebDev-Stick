<?php
/**
 * Portable WebDev Sandbox Dashboard
 * Automatically provisions missing data architectures seamlessly.
 */

// 1. Establish a raw connection to the MariaDB instance root (without picking a DB yet)
$host    = '127.0.0.1';
$user    = 'root';
$pass    = '';
$port    = '3306';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // 2. Automated Provisioning Layer: Force build our missing database container container safely
    $pdo->exec("CREATE DATABASE IF NOT EXISTS TestTable CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

    // 3. Now jump cleanly straight into our provisioned schema space
    $pdo->exec("USE TestTable;");
    $db_status = "CONNECTED & AUTO-PROVISIONED";
} catch (\PDOException $e) {
    $db_status = "ERROR: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Portable WebDev Sandbox Station</title>
<style>
body { font-family: sans-serif; background: #121214; color: #e1e1e6; padding: 40px; text-align: center; }
.box { background: #19191c; border: 1px solid #00ff66; padding: 25px; border-radius: 8px; display: inline-block; margin-top: 20px; text-align: left; min-width: 400px; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
.status { color: #00ff66; font-weight: bold; }
.error { color: #ff5555; font-weight: bold; }
hr { border: 0; border-top: 1px solid #29292e; margin: 15px 0; }
h3 { color: #a8a8b3; margin-bottom: 5px; font-size: 16px; }
.table-list { background: #202024; padding: 10px 15px; border-radius: 4px; border-left: 3px solid #00ff66; font-family: monospace; font-size: 14px; margin-top: 5px; }
</style>
</head>
<body>

<h1>🚀 Portable WebDev Sandbox Station</h1>

<div class="box">
<p>💻 Apache Server: <span class="status">ONLINE (Static Multi-OS Build)</span></p>
<p>🗄️ MariaDB Engine: <span class="<?= str_contains($db_status, 'ERROR') ? 'error' : 'status' ?>"><?= $db_status ?></span></p>

<hr>

<h3>🗂️ Active Schema Space: <span style="color:#fff;">TestTable</span></h3>
<h3>📁 Current Custom Tables Vector:</h3>
<div class="table-list">
<?php
if (!str_contains($db_status, 'ERROR')) {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($tables)) {
        echo "<span style='color: #a8a8b3; font-style: italic;'>[ Pristine Sandbox: 0 Active Tables Found ]</span>";
    } else {
        foreach ($tables as $table) {
            echo "🔹 " . htmlspecialchars($table) . "<br>";
        }
    }
} else {
    echo "<span class='error'>Tables unavailable due to connection lock.</span>";
}
?>
</div>

<hr>
<p style="color: #a8a8b3; margin: 0; font-size: 13px; line-height: 1.4;">👍 Your open-source portable ecosystem handles everything automatically. The stack is empty, clean, and optimized to share with other developers.</p>
</div>

</body>
</html>
