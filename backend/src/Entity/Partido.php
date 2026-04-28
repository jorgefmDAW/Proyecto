<?php

namespace App\Entity;

use App\Repository\PartidoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PartidoRepository::class)]
class Partido
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Equipo::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipo $local = null;

    #[ORM\ManyToOne(targetEntity: Equipo::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipo $visitante = null;

    #[ORM\Column(length: 255)]
    private ?string $dia = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTime $hora = null;

    #[ORM\Column]
    private ?int $local_goles = null;

    #[ORM\Column]
    private ?int $visitante_goles = null;

    #[ORM\ManyToOne(inversedBy: 'partidos')]
    private ?Jornada $jornada = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLocalId(): ?Equipo
    {
        return $this->local;
    }

    public function setLocalId(?Equipo $local): static
    {
        $this->local = $local;

        return $this;
    }

    public function getVisitanteId(): ?Equipo
    {
        return $this->visitante;
    }

    public function setVisitanteId(?Equipo $visitante): static
    {
        $this->visitante = $visitante;

        return $this;
    }

    public function getDia(): ?string
    {
        return $this->dia;
    }

    public function setDia(string $dia): static
    {
        $this->dia = $dia;

        return $this;
    }

    public function getHora(): ?\DateTime
    {
        return $this->hora;
    }

    public function setHora(\DateTime $hora): static
    {
        $this->hora = $hora;

        return $this;
    }

    public function getLocalGoles(): ?int
    {
        return $this->local_goles;
    }

    public function setLocalGoles(int $local_goles): static
    {
        $this->local_goles = $local_goles;

        return $this;
    }

    public function getVisitanteGoles(): ?int
    {
        return $this->visitante_goles;
    }

    public function setVisitanteGoles(int $visitante_goles): static
    {
        $this->visitante_goles = $visitante_goles;

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
}
