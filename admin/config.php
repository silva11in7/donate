<?php
// admin/config.php
require_once __DIR__ . '/../security/firewall.php';
require_once 'layout.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic .env Loader
function load_env($path) {
    if (!file_exists($path)) return;
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}
load_env(__DIR__ . '/../.env');

// Database Connection
try {
    $host = $_ENV['DB_HOST'] ?? '';
    $port = $_ENV['DB_PORT'] ?? '5432';
    $dbname = $_ENV['DB_NAME'] ?? 'postgres';
    $user = $_ENV['DB_USER'] ?? 'postgres';
    $pass = $_ENV['DB_PASS'] ?? '';

    $has_pgsql = in_array('pgsql', PDO::getAvailableDrivers());
    $has_mysql = in_array('mysql', PDO::getAvailableDrivers());

    if ($host) {
        try {
            if ($port == '5432' || $port == '6543' || strpos($host, 'supabase.co') !== false) {
                // Force PG for Supabase
                if (!$has_pgsql) throw new PDOException("Driver 'pdo_pgsql' não encontrado.");
                $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_PERSISTENT => true
                ]);
            } else {
                // Try MySQL (phpMyAdmin)
                if (!$has_mysql) throw new PDOException("Driver 'pdo_mysql' não encontrado.");
                $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
                $pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_TIMEOUT => 5,
                    PDO::ATTR_PERSISTENT => true
                ]);
            }
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Fallback to SQLite on connection error
            $pdo = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
                $_SESSION['db_warning'] = "Erro de conexão com o Banco de Dados ($host): " . $e->getMessage() . ". Usando SQLite temporariamente.";
            }
        }
    } else {
        // Fallback to SQLite if no host provided
        $pdo = new PDO('sqlite:' . __DIR__ . '/database.sqlite');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    die("Erro crítico ao inicializar o banco de dados: " . $e->getMessage());
}

// Ensure the database is initialized (Postgres compatible)
function ensure_db($pdo) {
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    $is_mysql = ($driver === 'mysql');
    $is_pgsql = ($driver === 'pgsql');
    
    $auto_inc = $is_mysql ? "AUTO_INCREMENT" : ($is_pgsql ? "SERIAL" : "INTEGER");
    $pk = $is_mysql ? "PRIMARY KEY" : ($is_pgsql ? "PRIMARY KEY" : "PRIMARY KEY AUTOINCREMENT");
    $timestamp = ($is_mysql || $is_pgsql) ? "TIMESTAMP DEFAULT CURRENT_TIMESTAMP" : "DATETIME DEFAULT CURRENT_TIMESTAMP";
    $text_type = $is_mysql ? "VARCHAR(255)" : "TEXT"; // MySQL prefers length for indexes but TEXT works for simple fields

    // MySQL specific table options
    $table_options = $is_mysql ? " ENGINE=InnoDB DEFAULT CHARSET=utf8mb4" : "";

    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id $auto_inc $pk,
        username VARCHAR(100) NOT NULL" . ($is_pgsql ? " UNIQUE" : "") . ",
        password_hash TEXT NOT NULL,
        full_name VARCHAR(100) DEFAULT 'Administrador',
        profile_image TEXT
        " . (!$is_pgsql ? ", UNIQUE(username)" : "") . "
    ) $table_options;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS leads (
        id $auto_inc $pk,
        name VARCHAR(255),
        email VARCHAR(255),
        phone VARCHAR(50),
        pix_code TEXT,
        status VARCHAR(50) DEFAULT 'pending',
        amount REAL,
        step VARCHAR(50) DEFAULT 'start',
        gateway VARCHAR(50),
        created_at $timestamp,
        updated_at $timestamp" . ($is_mysql ? " ON UPDATE CURRENT_TIMESTAMP" : "") . "
    ) $table_options;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS gateways (
        id $auto_inc $pk,
        name VARCHAR(50) NOT NULL" . ($is_pgsql ? " UNIQUE" : "") . ",
        active INTEGER DEFAULT 0,
        config_json TEXT
        " . (!$is_pgsql ? ", UNIQUE(name)" : "") . "
    ) $table_options;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS recovery_rules (
        id $auto_inc $pk,
        delay_minutes INTEGER NOT NULL,
        message TEXT,
        active INTEGER DEFAULT 1
    ) $table_options;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS settings (
        \"key\" VARCHAR(255) PRIMARY KEY,
        \"value\" TEXT
    ) $table_options;");

    // Insert default admin if not exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (username, password_hash, full_name) VALUES ('admin', ?, 'Administrador')")->execute([$password_hash]);
    }

    // Initial gateways
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM gateways");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Perfect Pay', 1)")->execute();
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Kirvano', 0)")->execute();
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Paggue', 0)")->execute();
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Genesys', 0)")->execute();
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Amplo', 0)")->execute();
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Oasyfy', 0)")->execute();
        $pdo->prepare("INSERT INTO gateways (name, active) VALUES ('Babylon', 0)")->execute();
    }

    // Seed default settings if empty
    $defaults = [
        'vakinha_goal' => '50000',
        'vakinha_raised' => '12500',
        'vakinha_title' => 'Ajude a salvar o pequeno Arthur',
        'vakinha_description' => 'Apoie essa causa e faça a diferença na vida de quem precisa.',
        'vid_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ',
        'utmfy_api_token' => '',
        'utmfy_platform' => 'KamyBot',
        'banner_url' => 'https://images.unsplash.com/photo-1547683326-33a7ad29c8a2?auto=format&fit=crop&q=80&w=1200',
        'banner_author' => 'Cruz Vermelha',
        'banner_title' => 'SOS ENCHENTES',
        'banner_location_1' => 'JUIZ DE FORA - MG',
        'banner_location_2' => 'ÚBA - MG'
    ];
    
    foreach ($defaults as $key => $val) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings WHERE \"key\" = ?");
        $stmt->execute([$key]);
        if ($stmt->fetchColumn() == 0) {
            $pdo->prepare("INSERT INTO settings (\"key\", \"value\") VALUES (?, ?)")->execute([$key, $val]);
        }
    }
}

// Optimization: Only run ensure_db once per session to avoid slowness
if (!isset($_SESSION['db_initialized']) || isset($_GET['reinit_db'])) {
    ensure_db($pdo);
    $_SESSION['db_initialized'] = true;
}

function check_auth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function get_current_admin() {
    global $pdo;
    if (!isset($_SESSION['user_id'])) return null;
    
    // Optimization: Cache admin data in session
    if (isset($_SESSION['admin_user_cache']) && !isset($_GET['refresh_cache'])) {
        return $_SESSION['admin_user_cache'];
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['admin_user_cache'] = $user;
    }
    
    return $user;
}

function refresh_admin_cache() {
    unset($_SESSION['admin_user_cache']);
}
?>
