<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UsuarioDto 
{
    #[Assert\NotBlank(message: 'El email es obligatiorio')]
    #[Assert\Email(message: 'El email no es válido')]
    public string $email;

    #[Assert\NotBlank(message: 'El nombre de usuario es obligatiorio')]
    public string $username;

    #[Assert\NotBlank(message: 'La contraseña es obligatoria')]
    #[Assert\Length(
        min: 6,
        minMessage: 'La contraseña debe tener al menos {{ limit }} caracteres'
    )]
    public string $password;
}