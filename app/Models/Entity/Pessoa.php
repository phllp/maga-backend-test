<?php

namespace App\Models\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
#[ORM\Table(name: 'pessoas')]
class Pessoa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $nome;

    #[ORM\Column(type: 'string', length: 11, unique: true)]
    private string $cpf;

    /** @var Collection<int, Contato> */
    #[ORM\OneToMany(
        mappedBy: 'pessoa',
        targetEntity: Contato::class,
        cascade: ['persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $contatos;

    public function __construct(string $nome, string $cpf)
    {
        $this->nome = $nome;
        $this->cpf = $cpf;
        $this->contatos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }
    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }
    public function setCpf(string $cpf): void
    {
        $this->cpf = $cpf;
    }

    /** @return Collection<int, Contato> */
    public function getContatos(): Collection
    {
        return $this->contatos;
    }

    public function addContato(Contato $contato): void
    {
        if (!$this->contatos->contains($contato)) {
            $this->contatos->add($contato);
            $contato->setPessoa($this);
        }
    }

    public function removeContato(Contato $contato): void
    {
        if ($this->contatos->removeElement($contato)) {
            if ($contato->getPessoa() === $this) {
                $contato->setPessoa(null);
            }
        }
    }
}
