<?php

namespace App\Models\Entity;

use App\Models\Enum\ContatoTipo;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: 'contatos',
    indexes: [new ORM\Index(name: 'idx_contato_pessoa', columns: ['pessoa_id'])]
)]
class Contato
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', enumType: ContatoTipo::class)]
    private ContatoTipo $tipo;

    #[ORM\Column(type: 'string', length: 255)]
    private string $descricao;

    #[ORM\ManyToOne(targetEntity: Pessoa::class, inversedBy: 'contatos')]
    #[ORM\JoinColumn(name: 'pessoa_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Pessoa $pessoa = null;

    public function __construct(ContatoTipo $tipo, string $descricao, Pessoa $pessoa)
    {
        $this->tipo = $tipo;
        $this->descricao = $descricao;
        $this->pessoa = $pessoa;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTipo(): ContatoTipo
    {
        return $this->tipo;
    }
    public function setTipo(ContatoTipo $tipo): void
    {
        $this->tipo = $tipo;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }
    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getPessoa(): ?Pessoa
    {
        return $this->pessoa;
    }
    public function setPessoa(?Pessoa $pessoa): void
    {
        $this->pessoa = $pessoa;
    }
}
