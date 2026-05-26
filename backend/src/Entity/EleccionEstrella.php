<?php

namespace App\Entity;

use App\Repository\EleccionEstrellaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\UniqueConstraint(name: 'unique_partido_alineacion', columns: ['alineacion_id', 'partido_id'])]
#[ORM\Entity(repositoryClass: EleccionEstrellaRepository::class)]
class EleccionEstrella
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Alineacion::class, inversedBy: 'eleccionesEstrella')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Alineacion $alineacion = null;

    #[ORM\ManyToOne(inversedBy: 'eleccionEstrellas')]
    private ?Partido $partido = null;

    #[ORM\ManyToOne(inversedBy: 'eleccionEstrellas')]
    private ?Jugador $jugador = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAlineacion(): ?alineacion
    {
        return $this->alineacion;
    }

    public function setAlineacion(?alineacion $alineacion): static
    {
        $this->alineacion = $alineacion;

        return $this;
    }

    public function getPartido(): ?Partido
    {
        return $this->partido;
    }

    public function setPartido(?Partido $partido): static
    {
        $this->partido = $partido;

        return $this;
    }

    public function getJugador(): ?Jugador
    {
        return $this->jugador;
    }

    public function setJugador(?Jugador $jugador): static
    {
        $this->jugador = $jugador;

        return $this;
    }
}
