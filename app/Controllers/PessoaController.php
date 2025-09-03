<?php

namespace App\Controllers;

use App\Models\Entity\Pessoa;
use Doctrine\ORM\EntityManagerInterface;

class PessoaController extends BaseController
{

    public function __construct(private EntityManagerInterface $em) {}


    public function index(): void
    {
        $pessoas = $this->em->getRepository(Pessoa::class)->findBy([], ['id' => 'DESC']);
        $this->render('pessoa/index', compact('pessoas'));
    }


    public function createForm(): void
    {
        $this->render('pessoa/create');
    }


    public function create(): void
    {
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');


        if ($nome === '' || $cpf === '') {
            http_response_code(422);
            $error = 'Nome e CPF são obrigatórios.';
            $this->render('pessoa/create', compact('error'));
            return;
        }


        $pessoa = new Pessoa($nome, $cpf);
        $this->em->persist($pessoa);
        $this->em->flush();


        header('Location: /pessoas');
    }
}
