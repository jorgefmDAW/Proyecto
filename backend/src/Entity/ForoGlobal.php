<?php

namespace App\Entity;

use App\Repository\ForoGlobalRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ForoGlobalRepository::class)]
class ForoGlobal
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $mensaje = null;

    #[ORM\ManyToOne(inversedBy: 'foroGlobals')]
    private ?Usuario $usuario = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): static
    {
        $this->usuario = $usuario;

        return $this;
    }
}
