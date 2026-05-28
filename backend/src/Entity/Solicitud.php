<?php

namespace App\Entity;

use App\Repository\SolicitudRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SolicitudRepository::class)]
class Solicitud
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Liga::class, inversedBy: 'solicitudes')]
    #[ORM\JoinColumn(nullable: false)] // nullable: false porque una solicitud siempre necesita una liga
    private ?Liga $liga = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class, inversedBy: 'solicitudes')]
    #[ORM\JoinColumn(nullable: false)] // nullable: false porque una solicitud siempre necesita un usuario
    private ?Usuario $usuario = null;

    #[ORM\Column(length: 255)]
    private ?string $mensaje = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $fecha = null;

    #[ORM\Column]
    private ?bool $aceptada = null;

    public function __construct()
    {
        $this->fecha = new \DateTimeImmutable('now', new \DateTimeZone('Europe/Madrid'));
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

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): static
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getFecha(): ?\DateTimeImmutable
    {
        return $this->fecha;
    }

    public function isAceptada(): ?bool
    {
        return $this->aceptada;
    }

    public function setAceptada(bool $aceptada): static
    {
        $this->aceptada = $aceptada;

        return $this;
    }
}
