<?php

namespace App\Controller;

use App\Entity\Equipo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

#[Route(path:'/api/equipos')]
final class EquipoController extends AbstractController {

    // ======================= GET ALL EQUIPOS =======================
    #[Route(path:'', methods:['GET'])]
    #[OA\Get(
        path: '/api/equipos',
        summary: 'Obtiene todos los equipos de la liga',
        tags: ['Equipos']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array con todos los equipos de la liga'
    )]
    public function getAllEquipos(EntityManagerInterface $em): JsonResponse {
        $equipos = $em->getRepository(Equipo::class)->findAll();

        return $this->json(array_map(
            fn(Equipo $equipos) => $this->toArray($equipos),
            $equipos
        ));
    }

    // ======================= GET EQUIPO BY ID =======================
    #[Route(path:'/{equipo_id}', methods:['GET'])]
    #[OA\Get(
        path: '/api/equipos/{equipo_id}',
        summary: 'Obtiene un equipo con un id específico',
        tags: ['Equipos']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un equipo con un id específico'
    )]
    public function getPartidosOfJornada(EntityManagerInterface $em, int $equipo_id): JsonResponse {
        $partido = $em->getRepository(Equipo::class)->find($equipo_id);

        if(!$partido) {
            return $this->json(['message' => 'No se ha encontrado ningún equipo con ese id'], 404);
        }

       return $this->json($this->toArray($partido));
    }

    // ======================= HELPERS =======================
    private function toArray(Equipo $equipo): array {
        return [
            'id' => $equipo->getId(),
            'nombre' => $equipo->getNombre(),
            'escudo' => $equipo->getEscudo(),
        ];
    }

}