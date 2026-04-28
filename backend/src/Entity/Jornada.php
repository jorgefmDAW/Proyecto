<?php

namespace App\Entity;

use App\Repository\JornadaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JornadaRepository::class)]
class Jornada
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, Partido>
     */
    #[ORM\OneToMany(targetEntity: Partido::class, mappedBy: 'jornada_id')]
    private Collection $partidos;

    /**
     * @var Collection<int, Puntuacion>
     */
    #[ORM\OneToMany(targetEntity: Puntuacion::class, mappedBy: 'jornada_id')]
    private Collection $puntuacions;

    #[ORM\Column]
    private ?\DateTime $fecha_inicio = null;

    #[ORM\Column]
    private ?\DateTime $fecha_final = null;

    public function __construct()
    {
        $this->partidos = new ArrayCollection();
        $this->puntuacions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return Collection<int, Partido>
     */
    public function getPartidos(): Collection
    {
        return $this->partidos;
    }

    public function addPartido(Partido $partido): static
    {
        if (!$this->partidos->contains($partido)) {
            $this->partidos->add($partido);
            $partido->setJornada($this);
        }

        return $this;
    }

    public function removePartido(Partido $partido): static
    {
        if ($this->partidos->removeElement($partido)) {
            // set the owning side to null (unless already changed)
            if ($partido->getJornada() === $this) {
                $partido->setJornada(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Puntuacion>
     */
    public function getPuntuacions(): Collection
    {
        return $this->puntuacions;
    }

    public function addPuntuacion(Puntuacion $puntuacion): static
    {
        if (!$this->puntuacions->contains($puntuacion)) {
            $this->puntuacions->add($puntuacion);
            $puntuacion->setJornada($this);
        }

        return $this;
    }

    public function removePuntuacion(Puntuacion $puntuacion): static
    {
        if ($this->puntuacions->removeElement($puntuacion)) {
            // set the owning side to null (unless already changed)
            if ($puntuacion->getJornada() === $this) {
                $puntuacion->setJornada(null);
            }
        }

        return $this;
    }

    public function getFechaInicio(): ?\DateTime
    {
        return $this->fecha_inicio;
    }

    public function setFechaInicio(\DateTime $fecha_inicio): static
    {
        $this->fecha_inicio = $fecha_inicio;

        return $this;
    }

    public function getFechaFinal(): ?\DateTime
    {
        return $this->fecha_final;
    }

    public function setFechaFinal(\DateTime $fecha_final): static
    {
        $this->fecha_final = $fecha_final;

        return $this;
    }
}
