<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ForoGlobalRepository;

class ForoGlobalDto 
{
    #[Assert\NotBlank(message: 'El mensaje no puede estar vacio')]
    public string $mensaje;

    #[Assert\NotBlank(message: 'El id del usuario es obligatorio')]
    #[Assert\Type(type: 'integer')]
    public int $usuario;
}