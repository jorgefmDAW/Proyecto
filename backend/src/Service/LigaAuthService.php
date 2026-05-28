<?php

namespace App\Service;

use App\Entity\Usuario;
use App\Entity\UsuarioFantasy;
use App\Repository\UsuarioFantasyRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class LigaAuthService
{
    public function __construct(
        private UsuarioFantasyRepository $usuarioFantasyRepository
    ) {}

    // verifica si el usuario pertenece a esa liga, si pertenece devuelve el usuario fantasy, si NO pertenece devuelve un 403
    public function verificarAcceso(int $ligaId, Usuario $usuario): UsuarioFantasy
    {
        $usuarioFantasy = $this->usuarioFantasyRepository->findOneBy([
            'liga'    => $ligaId,
            'usuario' => $usuario->getId(),
        ]);

        if (!$usuarioFantasy) {
            throw new AccessDeniedHttpException('No perteneces a esta liga.');
        }

        return $usuarioFantasy;
    }
}