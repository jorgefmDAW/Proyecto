<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class LigaDto 
{
    #[Assert\NotBlank(message: 'El nombre es obligatorio')]
    public string $nombre;

    #[Assert\NotNull(message: 'La privacidad no puede estar vacia')]
    public bool $privada;
}