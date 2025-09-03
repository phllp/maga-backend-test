<?php

namespace App\Controllers;

use Doctrine\ORM\EntityManagerInterface;

class HomeController extends BaseController
{

    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(): void
    {
        $this->render('home/index');
    }
}
