<?php

namespace App\Entity;

use App\Repository\EleccionFantasyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EleccionFantasyRepository::class)]
class EleccionFantasy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'eleccionFantasies')]
    private ?UsuarioFantasy $usuarioFantasy = null;

    #[ORM\ManyToOne(inversedBy: 'eleccionFantasies')]
    private ?Partido $partido = null;

    #[ORM\ManyToOne(inversedBy: 'eleccionFantasies')]
    private ?Jugador $jugador = null;

    #[ORM\ManyToOne(inversedBy: 'eleccionFantasies')]
    private ?Equipo $equipo = null;

    #[ORM\Column(nullable: true)]
    private ?int $puntosObtenidos = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $creadoEn = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $actualizadoEn = null;

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

    public function getEquipo(): ?Equipo
    {
        return $this->equipo;
    }

    public function setEquipo(?Equipo $equipo): static
    {
        $this->equipo = $equipo;

        return $this;
    }

    public function getPuntosObtenidos(): ?int
    {
        return $this->puntosObtenidos;
    }

    public function setPuntosObtenidos(?int $puntosObtenidos): static
    {
        $this->puntosObtenidos = $puntosObtenidos;

        return $this;
    }

    public function getCreadoEn(): ?\DateTime
    {
        return $this->creadoEn;
    }

    public function setCreadoEn(?\DateTime $creadoEn): static
    {
        $this->creadoEn = $creadoEn;

        return $this;
    }

    public function getActualizadoEn(): ?\DateTime
    {
        return $this->actualizadoEn;
    }

    public function setActualizadoEn(?\DateTime $actualizadoEn): static
    {
        $this->actualizadoEn = $actualizadoEn;

        return $this;
    }
}
