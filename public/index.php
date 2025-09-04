<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/doctrine.php';
require_once __DIR__ . '/../app/Functions/formatters.php';


use App\Controllers\HomeController;
use App\Controllers\PessoaController;
use App\Controllers\ContatoController;

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Tela inicial

if ($uri === '/' && $method === 'GET') {
    (new HomeController($entityManager))->__invoke();
    exit;
}

// Endpoints relacionados as Pessoas

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

// Endpoints relacionados aos Contatos

if ($uri === '/contatos' && $method === 'GET') {
    (new ContatoController($entityManager))->indexJson(); // ?pessoaId=123
    exit;
}
if ($uri === '/contatos' && $method === 'POST') {
    (new ContatoController($entityManager))->create(); // pessoa_id, tipo, descricao
    exit;
}
if ($uri === '/contatos/delete' && $method === 'POST') {
    (new ContatoController($entityManager))->delete(); // id
    exit;
}

if ($uri === '/contatos/update' && $method === 'POST') {
    (new ContatoController($entityManager))->update(); // id, tipo, descricao
    exit;
}

http_response_code(404);
echo 'Página não encontrada';
