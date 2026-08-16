<?php
/**
 * Database connection for Office Asset Tracker (PostgreSQL / Neon).
 *
 * The app was originally written against MySQL's mysqli API. To run on
 * PostgreSQL without rewriting every page, this file connects with PDO and
 * exposes a small mysqli-compatible shim ($conn) that supports the handful
 * of calls the app uses: query(), fetch_assoc(), num_rows and ->error.
 *
 * Configuration is environment-driven:
 *   1. DATABASE_URL (Neon/Render standard) — postgres://user:pass@host/db
 *   2. Discrete vars: PGHOST/PGPORT/PGUSER/PGPASSWORD/PGDATABASE
 *   3. Local defaults (localhost / postgres).
 */

/** HTML-escape a value for safe output (guards against stored XSS). */
function e($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}

/** Redirect to the login page unless a user is signed in. */
function require_login()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit();
    }
}

/** Redirect unless the signed-in user is an Admin. */
function require_admin()
{
    require_login();
    if (($_SESSION['role'] ?? '') !== 'Admin') {
        header("Location: /login.php");
        exit();
    }
}

/** Queue a one-time flash message shown on the next page render. */
function set_flash($type, $msg)
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

/** Bootstrap badge classes for an asset status value. */
function status_class($status)
{
    switch ($status) {
        case 'Available':    return 'bg-success';
        case 'In Use':       return 'bg-primary';
        case 'Under Repair': return 'bg-warning text-dark';
        case 'Disposed':     return 'bg-danger';
        default:             return 'bg-secondary';
    }
}

function oat_pg_dsn()
{
    $url = getenv('DATABASE_URL') ?: getenv('POSTGRES_URL');

    if ($url) {
        $p = parse_url($url);
        parse_str($p['query'] ?? '', $q);
        return [
            'host' => $p['host'] ?? 'localhost',
            'port' => $p['port'] ?? 5432,
            'db'   => isset($p['path']) ? ltrim($p['path'], '/') : 'neondb',
            'user' => isset($p['user']) ? urldecode($p['user']) : 'postgres',
            'pass' => isset($p['pass']) ? urldecode($p['pass']) : '',
            // Neon requires TLS; honour an explicit sslmode, else default to require.
            'sslmode' => $q['sslmode'] ?? 'require',
        ];
    }

    return [
        'host' => getenv('PGHOST')     ?: 'localhost',
        'port' => getenv('PGPORT')     ?: 5432,
        'db'   => getenv('PGDATABASE') ?: 'office_asset_tracker',
        'user' => getenv('PGUSER')     ?: 'postgres',
        'pass' => getenv('PGPASSWORD') ?: '',
        'sslmode' => getenv('PGSSLMODE') ?: 'prefer',
    ];
}

/**
 * Result wrapper mimicking the subset of mysqli_result the app relies on:
 *   - fetch_assoc(): next row as an associative array, or null when exhausted
 *   - num_rows:      number of rows (public property, as in mysqli)
 */
class OatResult
{
    private array $rows;
    private int $pos = 0;
    public int $num_rows;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_assoc()
    {
        return $this->rows[$this->pos++] ?? null;
    }
}

/**
 * Connection wrapper mimicking the subset of mysqli the app relies on:
 *   - query():  runs SQL; returns an OatResult for row-returning statements,
 *               true for statements that change data, false on error.
 *   - ->error:  last error message (as in mysqli).
 */
class OatConn
{
    private PDO $pdo;
    public string $error = '';

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run a query. Pass $params to use a prepared statement with bound
     * values (positional ? placeholders) — always prefer this for any value
     * derived from user input. Returns an OatResult for row-returning
     * statements, true for others, false on error.
     */
    public function query(string $sql, array $params = [])
    {
        try {
            if ($params) {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($params);
            } else {
                $stmt = $this->pdo->query($sql);
            }
            if ($stmt->columnCount() > 0) {
                return new OatResult($stmt->fetchAll(PDO::FETCH_ASSOC));
            }
            return true;
        } catch (PDOException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function set_charset($charset)
    {
        // No-op: PDO connects as UTF-8 by default. Kept for API compatibility.
        return true;
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}

$cfg = oat_pg_dsn();

$dsn = sprintf(
    'pgsql:host=%s;port=%s;dbname=%s;sslmode=%s',
    $cfg['host'], $cfg['port'], $cfg['db'], $cfg['sslmode']
);

try {
    $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$conn = new OatConn($pdo);

/**
 * One-time schema bootstrap. On a fresh database (no `users` table),
 * load the tables and sample data from schema.sql. Idempotent: the
 * script uses CREATE TABLE IF NOT EXISTS / INSERT ... ON CONFLICT DO NOTHING.
 * PDO::exec runs the multi-statement file in one round trip via libpq.
 */
$exists = $pdo->query("SELECT to_regclass('public.users') AS t")->fetch(PDO::FETCH_ASSOC);
if (empty($exists['t'])) {
    $schema = @file_get_contents(__DIR__ . '/schema.sql');
    if ($schema) {
        try {
            $pdo->exec($schema);
        } catch (PDOException $e) {
            // Surface bootstrap problems rather than failing silently.
            die("Schema initialisation failed: " . $e->getMessage());
        }
    }
}
?>
