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

    #[ORM\Column]
    private ?int $numero = null;

    #[ORM\Column]
    private ?\DateTime $fecha_inicio = null;

    #[ORM\Column]
    private ?\DateTime $fecha_final = null;

    #[ORM\OneToMany(targetEntity: Partido::class, mappedBy: 'jornada')]
    private Collection $partidos;

    #[ORM\OneToMany(targetEntity: Puntuacion::class, mappedBy: 'jornada')]
    private Collection $puntuacions;

    public function __construct()
    {
        $this->partidos = new ArrayCollection();
        $this->puntuacions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;
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
            if ($partido->getJornada() === $this) {
                $partido->setJornada(null);
            }
        }
        return $this;
    }

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
            if ($puntuacion->getJornada() === $this) {
                $puntuacion->setJornada(null);
            }
        }
        return $this;
    }
}