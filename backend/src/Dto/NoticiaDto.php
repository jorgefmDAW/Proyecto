<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class NoticiaDto 
{
    #[Assert\NotBlank(message: 'El titulo es obligatorio')]
    public string $titulo;

    #[Assert\NotBlank(message: 'La categoria es obligatoria')]
    public string $categoria;

    #[Assert\NotBlank(message: 'El texto de la noticia no puede estar vacio')]
    public string $texto;
}