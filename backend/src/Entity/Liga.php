<?php

namespace App\Entity;

use App\Repository\LigaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigaRepository::class)]
class Liga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    #[ORM\OneToMany(mappedBy: 'liga', targetEntity: Usuario::class, fetch: 'EXTRA_LAZY')]
    private Collection $miembros;

    #[ORM\Column]
    private ?bool $privada = null;

    public function __construct()
    {
        $this->miembros = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getMiembros(): ?int
    {
        return $this->miembros->count();
    }

    public function isPrivada(): ?bool
    {
        return $this->privada;
    }

    public function setPrivada(bool $privada): static
    {
        $this->privada = $privada;

        return $this;
    }
}
