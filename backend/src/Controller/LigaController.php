<?php

namespace App\Controller;

use App\Entity\Liga;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Dto\LigaDto;
use OpenApi\Attributes as OA;

#[Route(path:'/api/ligas')]
final class LigaController extends AbstractController {

    // ======================= GET ALL LIGAS PUBLICAS =======================
    #[Route(path:'', methods:['GET'])]
    #[OA\Get(
        path: '/api/ligas',
        summary: 'Obtiene todas las ligas publicas',
        tags: ['Ligas']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene todas las ligas publicas'
    )]
    public function getAllLigasPublicas(EntityManagerInterface $em): JsonResponse {
        $ligas = $em->getRepository(Liga::class)->findBy(['privada' => 0]); # al ponerle 0 devuelve todas las ligas publicas

        if(!$ligas) {
            return $this->json(['message' => 'No se han encontrado ligas publicas'], 404);
        }

        $resultados = [
            'ligas' => []
        ];

        foreach($ligas as $l) {
            $resultados['ligas'][] = $this->toArray($l);
        }

        return $this->json($resultados);
    }

    // ======================= CREAR LIGA =======================
    #[Route(path:'/crear', methods:['POST'])]
    #[OA\Post(
        path: '/api/ligas/crear',
        summary: 'Crea una liga',
        tags: ['Ligas'],
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'nombre', type: 'string', example: 'Liga Expertos'),
                new OA\Property(property: 'privada', type: 'boolean', example: '1')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Crea una liga -> 1 privada / 0 publica'
    )]
    public function crearLiga(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em
        ): JsonResponse {

            $data = json_decode($request->getContent(), true);

            // mapear datos al dto
            $ligaDto = new LigaDto();
            $ligaDto->nombre = $data['nombre'];
            $ligaDto->privada = $data['privada'];

            // validar dto
            $errores = $validator->validate($ligaDto);
            if( count($errores) > 0 ) {
                return $this->json(['errores' => (string) $errores], 400);
            } 

            // verificar si hay una liga con el mismo nombre
            $liga_existente = $em
                ->getRepository(Liga::class)
                ->findOneBy(['nombre' => $ligaDto->nombre]);

            if($liga_existente) {
                return $this->json(['error' => 'Ya hay una liga con ese nombre'], 409);
            }

            $liga = new Liga();
            $liga->setNombre($ligaDto->nombre);
            $liga->setPrivada($ligaDto->privada);

            $em->persist($liga);
            $em->flush();

            $resultados = [
                'message' => 'Liga creada correctamente',
                'liga_creada' => $this->toArray($liga)
            ];

            return $this->json($resultados, 201);
    }

    // ======================= HELPERS =======================
    private function toArray(Liga $liga): array {
        return [
            'id' => $liga->getId(),
            'nombre' => $liga->getNombre(),
            'miembros' => $liga->getMiembros()
        ];
    }
}