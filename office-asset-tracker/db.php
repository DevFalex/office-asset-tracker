<?php
/**
 * Database connection for Office Asset Tracker.
 *
 * Configuration is environment-driven so the same code runs locally
 * (XAMPP/LAMP defaults) and on hosts like Railway. Resolution order:
 *   1. A full connection URL: MYSQL_URL / DATABASE_URL / MYSQL_PUBLIC_URL
 *   2. Discrete vars: MYSQLHOST/MYSQLPORT/... (Railway) or DB_HOST/DB_PORT/...
 *   3. Local defaults (localhost / root / no password).
 */

function oat_db_config()
{
    $url = getenv('MYSQL_URL') ?: getenv('DATABASE_URL') ?: getenv('MYSQL_PUBLIC_URL');
    if ($url) {
        $p = parse_url($url);
        return [
            'host' => $p['host'] ?? 'localhost',
            'port' => isset($p['port']) ? (int) $p['port'] : 3306,
            'user' => isset($p['user']) ? urldecode($p['user']) : 'root',
            'pass' => isset($p['pass']) ? urldecode($p['pass']) : '',
            'db'   => isset($p['path']) ? ltrim($p['path'], '/') : 'office_asset_tracker',
        ];
    }

    return [
        'host' => getenv('MYSQLHOST')     ?: (getenv('DB_HOST')     ?: 'localhost'),
        'port' => (int) (getenv('MYSQLPORT') ?: (getenv('DB_PORT') ?: 3306)),
        'user' => getenv('MYSQLUSER')     ?: (getenv('DB_USER')     ?: 'root'),
        'pass' => getenv('MYSQLPASSWORD') ?: (getenv('DB_PASSWORD') ?: ''),
        'db'   => getenv('MYSQLDATABASE') ?: (getenv('DB_NAME')     ?: 'office_asset_tracker'),
    ];
}

$cfg = oat_db_config();

$conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['db'], $cfg['port']);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

/**
 * One-time schema bootstrap. On a fresh database (no `users` table),
 * load the tables and sample data from schema.sql. Idempotent: the
 * script uses CREATE TABLE IF NOT EXISTS / INSERT IGNORE.
 */
$check = $conn->query("SHOW TABLES LIKE 'users'");
if ($check && $check->num_rows === 0) {
    $schema = @file_get_contents(__DIR__ . '/schema.sql');
    if ($schema && $conn->multi_query($schema)) {
        do {
            if ($res = $conn->store_result()) {
                $res->free();
            }
        } while ($conn->more_results() && $conn->next_result());
    }
}
?>
