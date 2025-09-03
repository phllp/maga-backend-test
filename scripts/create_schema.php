<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/doctrine.php';

use Doctrine\ORM\Tools\SchemaTool;
use App\Models\Entity\Pessoa;
use App\Models\Entity\Contato;

$tool = new SchemaTool($entityManager);
$classes = [
    $entityManager->getClassMetadata(Pessoa::class),
    $entityManager->getClassMetadata(Contato::class)
];

$tool->createSchema($classes);

echo "Schema criado com sucesso.\n";
