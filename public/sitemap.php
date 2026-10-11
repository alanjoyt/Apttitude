<?php
/**
 * Dynamic Sitemap Generator
 * Uses APP_BASE_URL so production automatically uses the real domain.
 */
require_once __DIR__ . '/../config/app.php';

header('Content-Type: application/xml; charset=utf-8');

$routes = [
    ['path' => '/index.php?action=landing',     'changefreq' => 'daily',   'priority' => '1.0'],
    ['path' => '/index.php?action=practice',    'changefreq' => 'daily',   'priority' => '0.9'],
    ['path' => '/index.php?action=technical',   'changefreq' => 'daily',   'priority' => '0.9'],
    ['path' => '/index.php?action=store',       'changefreq' => 'weekly',  'priority' => '0.8'],
    ['path' => '/index.php?action=leaderboard', 'changefreq' => 'daily',   'priority' => '0.8'],
    ['path' => '/index.php?action=skill_tree',  'changefreq' => 'weekly',  'priority' => '0.8'],
    ['path' => '/index.php?action=campaign',    'changefreq' => 'weekly',  'priority' => '0.8'],
    ['path' => '/index.php?action=duel',        'changefreq' => 'daily',   'priority' => '0.8'],
    ['path' => '/index.php?action=time_attack', 'changefreq' => 'daily',   'priority' => '0.8'],
    ['path' => '/index.php?action=social',      'changefreq' => 'daily',   'priority' => '0.7'],
    ['path' => '/index.php?action=privacy',     'changefreq' => 'monthly', 'priority' => '0.5'],
    ['path' => '/index.php?action=terms',       'changefreq' => 'monthly', 'priority' => '0.5'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($routes as $route): ?>
  <url>
    <loc><?= htmlspecialchars(APP_BASE_URL . $route['path']) ?></loc>
    <changefreq><?= $route['changefreq'] ?></changefreq>
    <priority><?= $route['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
