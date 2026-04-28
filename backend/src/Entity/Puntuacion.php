<?php

namespace App\Entity;

use App\Repository\PuntuacionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PuntuacionRepository::class)]
class Puntuacion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $puntos = null;

    #[ORM\ManyToOne(inversedBy: 'puntuacions')]
    private ?Jugador $jugador = null;

    #[ORM\ManyToOne(inversedBy: 'puntuacions')]
    private ?Jornada $jornada = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPuntos(): ?int
    {
        return $this->puntos;
    }

    public function setPuntos(int $puntos): static
    {
        $this->puntos = $puntos;

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
