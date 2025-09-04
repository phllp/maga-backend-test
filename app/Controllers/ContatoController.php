<?php

namespace App\Controllers;

use App\Models\Entity\Pessoa;
use App\Models\Entity\Contato;
use App\Models\Enum\ContatoTipo;
use Doctrine\ORM\EntityManagerInterface;

class ContatoController extends BaseController
{
    public function __construct(private EntityManagerInterface $em) {}

    // GET /contatos?pessoaId=123
    public function indexJson(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoaId = (int) ($_GET['pessoaId'] ?? 0);
        if ($pessoaId <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'Parâmetro pessoaId inválido']);
            return;
        }

        $pessoa = $this->em->find(Pessoa::class, $pessoaId);
        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['error' => 'Pessoa não encontrada']);
            return;
        }

        $contatos = [];
        foreach ($pessoa->getContatos() as $c) {
            $contatos[] = [
                'id'        => $c->getId(),
                'tipo'      => ['value' => $c->getTipo()->value, 'label' => $c->getTipo()->label()],
                'descricao' => $c->getDescricao(),
            ];
        }

        // Retorno pra função fetch
        echo json_encode([
            'pessoa' => [
                'id'   => $pessoa->getId(),
                'nome' => $pessoa->getNome(),
                'cpf'  => $pessoa->getCpf(),
            ],
            'contatos' => $contatos,
        ]);
    }

    // POST /contatos  (pessoa_id, tipo, descricao)
    public function create(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $pessoaId  = (int) ($_POST['pessoa_id'] ?? 0);
        $descricao = trim($_POST['descricao'] ?? '');

        $tipoRaw   = $_POST['tipo'] ?? null;
        $tipo = ContatoTipo::tryFrom((int)$tipoRaw);

        if ($pessoaId <= 0 || $descricao === '' || $tipo === null) {
            http_response_code(422);
            echo json_encode(['error' => 'Campos obrigatórios: pessoa_id, tipo, descricao']);
            return;
        }

        $pessoa = $this->em->find(Pessoa::class, $pessoaId);
        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['error' => 'Pessoa não encontrada']);
            return;
        }

        $contato = new Contato($tipo, $descricao, $pessoa);

        $pessoa->addContato($contato);

        $this->em->persist($contato);
        $this->em->flush();

        echo json_encode([
            'ok' => true,
            'contato' => [
                'id' => $contato->getId(),
                'tipo' => ['value' => $contato->getTipo()->value, 'label' => $contato->getTipo()->label()],
                'descricao' => $contato->getDescricao(),
            ]
        ]);
    }

    // POST /contatos/delete (id)
    public function delete(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo json_encode(['error' => 'ID inválido']);
            return;
        }

        $contato = $this->em->find(Contato::class, $id);
        if (!$contato) {
            http_response_code(404);
            echo json_encode(['error' => 'Contato não encontrado']);
            return;
        }

        $this->em->remove($contato);
        $this->em->flush();

        echo json_encode(['ok' => true]);
    }
}
