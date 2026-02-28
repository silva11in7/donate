<?php
// admin/db_init.php - Run once to initialize the database
$db_file = __DIR__ . '/database.sqlite';
$db = new PDO('sqlite:' . $db_file);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Create Users table
$db->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password_hash TEXT NOT NULL
)");

// Create Leads table
$db->exec("CREATE TABLE IF NOT EXISTS leads (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT,
    email TEXT,
    phone TEXT,
    pix_code TEXT,
    status TEXT DEFAULT 'pending', -- pending, approved
    amount REAL,
    step TEXT DEFAULT 'start', -- start, info, payment, complete
    gateway TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Create Gateways table
$db->exec("CREATE TABLE IF NOT EXISTS gateways (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    active INTEGER DEFAULT 0,
    config_json TEXT
)");

// Insert default admin if not exists (password: admin123)
$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
    $db->prepare("INSERT INTO users (username, password_hash) VALUES ('admin', ?)")->execute([$password_hash]);
}

// Initial gateways
$stmt = $db->prepare("SELECT COUNT(*) FROM gateways");
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    $db->prepare("INSERT INTO gateways (name, active) VALUES ('Perfect Pay', 1)")->execute();
    $db->prepare("INSERT INTO gateways (name, active) VALUES ('Kirvano', 0)")->execute();
    $db->prepare("INSERT INTO gateways (name, active) VALUES ('Paggue', 0)")->execute();
}

echo "Database initialized successfully.\n";
unlink(__FILE__); // Self-destruct for security
?>
