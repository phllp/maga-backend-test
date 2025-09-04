<?php

namespace App\Controllers;

use App\Models\Entity\Pessoa;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

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

    public function editForm(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid ID';
            return;
        }

        $pessoa = $this->em->find(Pessoa::class, $id);
        if (!$pessoa) {
            http_response_code(404);
            echo 'Pessoa not found';
            return;
        }

        $this->render('pessoa/edit', compact('pessoa'));
    }

    public function update(): void
    {
        $id   = (int) ($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $cpf  = trim($_POST['cpf'] ?? '');

        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid ID';
            return;
        }

        $pessoa = $this->em->find(Pessoa::class, $id);
        if (!$pessoa) {
            http_response_code(404);
            echo 'Pessoa não encontrada';
            return;
        }

        // Valida se um dos campos foi informado ao menos
        if ($nome === '' && $cpf === '') {
            http_response_code(422);
            $error = 'Nome ou CPF precisam ser informados.';
            $this->render('pessoa/edit', compact('pessoa', 'error'));
            return;
        }

        // Validação contra CPF duplicado
        $existing = $this->em->getRepository(Pessoa::class)->findOneBy(['cpf' => $cpf]);
        if ($existing && $existing->getId() !== $pessoa->getId()) {
            http_response_code(422);
            $error = 'Este CPF já está cadastrado.';
            $this->render('pessoa/edit', compact('pessoa', 'error'));
            return;
        }

        $pessoa->setNome($nome);
        $pessoa->setCpf($cpf);

        try {
            $this->em->flush();
        } catch (UniqueConstraintViolationException) {
            // Caso ao descartar as alterações o cpf volte a ser o original e este já existe em algum
            // registro novo, a validação deve ser feita novamente
            http_response_code(422);
            $error = 'Este CPF já está cadastrado.';
            $this->render('pessoa/edit', compact('pessoa', 'error'));
            return;
        }

        header('Location: /pessoas');
    }
}
