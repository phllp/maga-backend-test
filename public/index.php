<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/doctrine.php';

use App\Controllers\HomeController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($uri === '/' && $method === 'GET') {
    (new HomeController($entityManager))->__invoke();
    exit;
}

http_response_code(404);
echo 'Página não encontrada';
