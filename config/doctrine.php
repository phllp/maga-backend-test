<?php

use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$devMode = filter_var($_ENV['APP_DEV'] ?? 'true', FILTER_VALIDATE_BOOL);
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/../app/Models/Entity'],
    isDevMode: $devMode
);

$dbParams = [
    'driver' => 'pdo_pgsql',
    'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
    'port' => (int)($_ENV['DB_PORT'] ?? 5432),
    'user' => $_ENV['DB_USER'] ?? 'admin',
    'password' => $_ENV['DB_PASS'] ?? 'admin',
    'dbname' => $_ENV['DB_NAME'] ?? 'maga_db',
    'charset' => 'utf8'
];

$entityManager = EntityManager::create($dbParams, $config);
