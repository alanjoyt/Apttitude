<?php
/**
 * Application Global Configuration & Helpers
 * Provides central base URL and cache-busting asset versioning.
 */

date_default_timezone_set('Asia/Kolkata');

if (file_exists(__DIR__ . '/../.env')) {
    $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) continue;
        if (strpos($line, '=') !== false) {
            [$k, $v] = explode('=', $line, 2);
            putenv(trim($k) . '=' . trim($v));
            $_ENV[trim($k)] = trim($v);
        }
    }
}

if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'production');
}

if (!defined('ADMIN_MAX_COIN_GRANT_PER_ACTION')) {
    define('ADMIN_MAX_COIN_GRANT_PER_ACTION', 500);
}

// Mock lengths unlock policy: One unlock gives all three lengths (Quick, Standard, Full)
if (!defined('MOCK_ONE_UNLOCK_ALL_LENGTHS')) {
    define('MOCK_ONE_UNLOCK_ALL_LENGTHS', true);
}

if (!defined('APP_BASE_URL')) {
    $envBase = getenv('APP_BASE_URL');
    if ($envBase) {
        define('APP_BASE_URL', rtrim($envBase, '/'));
    } else {
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
            || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        $protocol = $isHttps ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptPath = $_SERVER['SCRIPT_NAME'] ?? '';
        $dir = str_replace('\\', '/', dirname($scriptPath));
        $baseDir = rtrim($dir, '/');
        // If script is inside public, base URL points to public directory root
        define('APP_BASE_URL', $protocol . $host . $baseDir);
    }
}

/**
 * Returns an asset path with its real file modification timestamp for immutable cache-busting.
 * e.g. "css/theme.css?v=1728574920"
 */
function asset_url(string $relativePath): string {
    static $assetCache = [];
    $cleanPath = ltrim($relativePath, '/');
    if (isset($assetCache[$cleanPath])) {
        return $assetCache[$cleanPath];
    }

    $diskPath = __DIR__ . '/../public/' . $cleanPath;
    $v = file_exists($diskPath) ? filemtime($diskPath) : time();
    $result = APP_BASE_URL . '/' . $cleanPath . '?v=' . $v;
    $assetCache[$cleanPath] = $result;
    return $result;
}
