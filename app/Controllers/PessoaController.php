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
        $q = trim($_GET['q'] ?? '');

        if ($q !== '') {
            $qb = $this->em->createQueryBuilder();
            $pessoas = $qb->select('p')
                ->from(Pessoa::class, 'p')
                ->where($qb->expr()->like('LOWER(p.nome)', ':q'))
                ->setParameter('q', '%' . mb_strtolower($q) . '%')
                ->orderBy('p.nome', 'ASC')
                ->getQuery()
                ->getResult();
        } else {
            $pessoas = $this->em->getRepository(Pessoa::class)
                ->findBy([], ['id' => 'DESC']);
        }

        $this->render('pessoa/index', compact('pessoas', 'q'));
    }


    public function createForm(): void
    {
        $this->render('pessoa/create');
    }

    private function isCpfValido(string $cpf): bool
    {
        return strlen($cpf) === 11 && ctype_digit($cpf);
    }

    private function isCpfExistente(string $cpf, string $idPessoa = ''): bool
    {
        $existing = $this->em->getRepository(Pessoa::class)->findOneBy(['cpf' => $cpf]);
        // No caso da criação de uma nova pessoa, o ID será vazio
        if (strlen($idPessoa) === 0) {
            // echo '' . $cpf . '' . $idPessoa . '';
            return $existing !== null;
        }
        // Se encontrar uma pessoa com o mesmo CPF, verifica se é a mesma pessoa (update)
        return $existing && $existing->getId() !== intval($idPessoa);
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

        // Validação contra CPF duplicado
        if ($this->isCpfExistente($cpf)) {
            http_response_code(422);
            $error = 'Este CPF já está cadastrado.';
            $this->render('pessoa/create', compact('pessoa', 'error'));
            return;
        }

        // Validação do formato do CPF
        if (!$this->isCpfValido($cpf)) {
            http_response_code(422);
            $error = 'CPF inválido. Deve conter exatamente 11 dígitos numéricos.';
            $this->render('pessoa/create', compact('pessoa', 'error'));
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

        // Valida se os campos foram informados
        if ($nome === '' && $cpf === '') {
            http_response_code(422);
            $error = 'Nome e CPF precisam ser informados.';
            $this->render('pessoa/edit', compact('pessoa', 'error'));
            return;
        }

        if (!$this->isCpfValido($cpf)) {
            http_response_code(422);
            $error = 'CPF inválido. Deve conter exatamente 11 dígitos numéricos.';
            $this->render('pessoa/edit', compact('pessoa', 'error'));
            return;
        }

        // Validação contra CPF duplicado
        if ($this->isCpfExistente($cpf, $pessoa->getId())) {
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

    // POST /pessoas/delete  (id)
    public function delete(): void
    {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo 'ID inválido.';
            return;
        }

        $pessoa = $this->em->find(Pessoa::class, $id);
        if (!$pessoa) {
            http_response_code(404);
            echo 'Pessoa não encontrada.';
            return;
        }

        $this->em->remove($pessoa);
        $this->em->flush();

        // Sucesso -> volta para a lista
        header('Location: /pessoas');
    }
}
