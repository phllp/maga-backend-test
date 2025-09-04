<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/doctrine.php';

use App\Controllers\HomeController;
use App\Controllers\PessoaController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($uri === '/' && $method === 'GET') {
    (new HomeController($entityManager))->__invoke();
    exit;
}

if ($uri === '/pessoas' && $method === 'GET') {
    (new PessoaController($entityManager))->index();
    exit;
}

if ($uri === '/pessoas/create' && $method === 'GET') {
    (new PessoaController($entityManager))->createForm();
    exit;
}

if ($uri === '/pessoas/create' && $method === 'POST') {
    (new PessoaController($entityManager))->create();
    exit;
}

if ($uri === '/pessoas/edit' && $method === 'GET') {
    (new PessoaController($entityManager))->editForm();
    exit;
}
if ($uri === '/pessoas/update' && $method === 'POST') {
    (new PessoaController($entityManager))->update();
    exit;
}

http_response_code(404);
echo 'Página não encontrada';
