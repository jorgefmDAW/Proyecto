<?php

namespace App\Entity;

use App\Repository\AlineacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlineacionRepository::class)]
class Alineacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'alineacions')]
    private ?UsuarioFantasy $usuarioFantasy = null;

    #[ORM\ManyToOne(inversedBy: 'alineacions')]
    private ?Jornada $jornada = null;

    #[ORM\Column]
    private ?int $puntosJornada = null;

    /**
     * @var Collection<int, EleccionEstrella>
     */
    #[ORM\OneToMany(targetEntity: EleccionEstrella::class, mappedBy: 'alineacion')]
    private Collection $eleccionEstrellas;

    public function __construct()
    {
        $this->eleccionEstrellas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuarioFantasy(): ?UsuarioFantasy
    {
        return $this->usuarioFantasy;
    }

    public function setUsuarioFantasy(?UsuarioFantasy $usuarioFantasy): static
    {
        $this->usuarioFantasy = $usuarioFantasy;

        return $this;
    }

    public function getJornada(): ?Jornada
    {
        return $this->jornada;
    }

    public function setJornada(?Jornada $jornada): static
    {
        $this->jornada = $jornada;

        return $this;
    }

    public function getPuntosJornada(): ?int
    {
        return $this->puntosJornada;
    }

    public function setPuntosJornada(int $puntosJornada): static
    {
        $this->puntosJornada = $puntosJornada;

        return $this;
    }

    /**
     * @return Collection<int, EleccionEstrella>
     */
    public function getEleccionEstrellas(): Collection
    {
        return $this->eleccionEstrellas;
    }

    public function addEleccionEstrella(EleccionEstrella $eleccionEstrella): static
    {
        if (!$this->eleccionEstrellas->contains($eleccionEstrella)) {
            $this->eleccionEstrellas->add($eleccionEstrella);
            $eleccionEstrella->setAlineacion($this);
        }

        return $this;
    }

    public function removeEleccionEstrella(EleccionEstrella $eleccionEstrella): static
    {
        if ($this->eleccionEstrellas->removeElement($eleccionEstrella)) {
            // set the owning side to null (unless already changed)
            if ($eleccionEstrella->getAlineacion() === $this) {
                $eleccionEstrella->setAlineacion(null);
            }
        }

        return $this;
    }
}
