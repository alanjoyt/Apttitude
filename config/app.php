<?php
/**
 * Application Global Configuration & Helpers
 * Provides central base URL and cache-busting asset versioning.
 */

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
    $result = $cleanPath . '?v=' . $v;
    $assetCache[$cleanPath] = $result;
    return $result;
}
