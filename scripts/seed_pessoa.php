<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/doctrine.php';


use App\Models\Entity\Pessoa;


$user = new Pessoa('Eleanor Rigby', '11122233344');
$entityManager->persist($user);
$entityManager->flush();


echo "Seeded: {$user->getId()}\n";
