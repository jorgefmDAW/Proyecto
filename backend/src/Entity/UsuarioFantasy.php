<?php

namespace App\Entity;

use App\Repository\UsuarioFantasyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuarioFantasyRepository::class)]
class UsuarioFantasy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'usuarioFantasies')]
    private ?Usuario $usuario = null;

    #[ORM\ManyToOne(inversedBy: 'usuarioFantasies')]
    private ?Liga $liga = null;

    #[ORM\Column]
    private ?int $puntosTotales = null;

    #[ORM\Column]
    private ?bool $creador = null;

    /**
     * @var Collection<int, EleccionFantasy>
     */
    #[ORM\OneToMany(targetEntity: EleccionFantasy::class, mappedBy: 'usuarioFantasy')]
    private Collection $eleccionFantasies;

    public function __construct()
    {
        $this->eleccionFantasies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getLiga(): ?Liga
    {
        return $this->liga;
    }

    public function setLiga(?Liga $liga): static
    {
        $this->liga = $liga;

        return $this;
    }

    public function getPuntosTotales(): ?int
    {
        return $this->puntosTotales;
    }

    public function setPuntosTotales(int $puntosTotales): static
    {
        $this->puntosTotales = $puntosTotales;

        return $this;
    }

    public function getCreador(): ?bool
    {
        return $this->creador;
    }

    public function setCreador(?bool $creador): static
    {
        $this->creador = $creador;

        return $this;
    }

    /**
     * @return Collection<int, EleccionFantasy>
     */
    public function getEleccionFantasies(): Collection
    {
        return $this->eleccionFantasies;
    }

    public function addEleccionFantasy(EleccionFantasy $eleccionFantasy): static
    {
        if (!$this->eleccionFantasies->contains($eleccionFantasy)) {
            $this->eleccionFantasies->add($eleccionFantasy);
            $eleccionFantasy->setUsuarioFantasy($this);
        }

        return $this;
    }

    public function removeEleccionFantasy(EleccionFantasy $eleccionFantasy): static
    {
        if ($this->eleccionFantasies->removeElement($eleccionFantasy)) {
            // set the owning side to null (unless already changed)
            if ($eleccionFantasy->getUsuarioFantasy() === $this) {
                $eleccionFantasy->setUsuarioFantasy(null);
            }
        }

        return $this;
    }

}
