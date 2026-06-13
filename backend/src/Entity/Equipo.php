<?php

namespace App\Entity;

use App\Repository\EquipoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Ignore;

#[ORM\Entity(repositoryClass: EquipoRepository::class)]
class Equipo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nombre = null;

    /**
     * @var Collection<int, Jugador>
     */
    #[Ignore]
    #[ORM\OneToMany(targetEntity: Jugador::class, mappedBy: 'equipo')]
    private Collection $jugadors;

    #[ORM\Column(length: 255)]
    private ?string $escudo = null;

    public function __construct()
    {
        $this->jugadors = new ArrayCollection();
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

    /**
     * @return Collection<int, Jugador>
     */
    public function getJugadors(): Collection
    {
        return $this->jugadors;
    }

    public function addJugador(Jugador $jugador): static
    {
        if (!$this->jugadors->contains($jugador)) {
            $this->jugadors->add($jugador);
            $jugador->setEquipo($this);
        }

        return $this;
    }

    public function removeJugador(Jugador $jugador): static
    {
        if ($this->jugadors->removeElement($jugador)) {
            // set the owning side to null (unless already changed)
            if ($jugador->getEquipo() === $this) {
                $jugador->setEquipo(null);
            }
        }

        return $this;
    }

    public function getEscudo(): ?string
    {
        return $this->escudo;
    }

    public function setEscudo(string $escudo): static
    {
        $this->escudo = $escudo;

        return $this;
    }
}
