<?php

namespace App\Controller;

use App\Entity\Noticia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

#[Route(path:'/api/noticias')]
final class NoticiaController extends AbstractController {

    // ======================= GET ALL NOTICIAS =======================
    #[Route(path:'', methods:['GET'])]
    #[OA\Get(
        path: '/api/noticias',
        summary: 'Obtiene todas las noticias',
        tags: ['Noticias'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene todas las noticias'
    )]
    public function getAllNoticias(EntityManagerInterface $em): JsonResponse {
        $noticias = $em->getRepository(Noticia::class)->findAll();

        return $this->json(array_map(
            fn(Noticia $noticias) => $this->toArray($noticias),
            $noticias
        ));
    }

    // ======================= HELPERS =======================
    private function toArray(Noticia $noticia): array {
        return [
            'id' => $noticia->getId(),
            'titulo' => $noticia->getTitulo(),
            'categoria' => $noticia->getCategoria(),
            'texto' => $noticia->getTexto(),
            'fecha' => $noticia->getFecha()->format('d-m-Y')
        ];
    }

}









