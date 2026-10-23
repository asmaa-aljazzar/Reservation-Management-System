<?php
$projectRoot = realpath(dirname(__DIR__));
$documentRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$baseUrl = '';
if ($projectRoot !== false && $documentRoot !== false) {
    $projectPath = str_replace('\\', '/', $projectRoot);
    $documentPath = rtrim(str_replace('\\', '/', $documentRoot), '/');
    if (stripos($projectPath, $documentPath . '/') === 0) {
        $baseUrl = substr($projectPath, strlen($documentPath));
    }
}
if (!defined('APP_BASE_URL')) {
    define('APP_BASE_URL', rtrim($baseUrl, '/'));
}
function app_url(string $path = ''): string
{
    return APP_BASE_URL . '/' . ltrim($path, '/');
}
