<?php
// Apttitudee Root Entry Point
// Automatically forwards visitors from root domain (e.g. InfinityFree htdocs) to public/index.php

$queryString = $_SERVER['QUERY_STRING'] ?? '';
$target = 'public/index.php' . ($queryString ? '?' . $queryString : '');

header('Location: ' . $target);
exit;
