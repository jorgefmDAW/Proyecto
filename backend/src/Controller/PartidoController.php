<?php

namespace App\Controller;

use App\Entity\Partido;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

#[Route(path:'/api/partidos')]
final class PartidoController extends AbstractController {

    // ======================= GET ALL PARTIDOS =======================
    #[Route(path:'', methods:['GET'])]
    #[OA\Get(
        path: '/api/partidos',
        summary: 'Obtiene todos los partidos de cada jornada',
        tags: ['Partidos'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array con todos los partidos de cada jornada'
    )]
    public function getAllPartidos(EntityManagerInterface $em): JsonResponse {
        $partidos = $em->getRepository(Partido::class)->findAll();

        return $this->json(array_map(
            fn(Partido $partidos) => $this->toArray($partidos),
            $partidos
        ));
    }

    // ======================= GET PARTIDOS OF JORNADA =======================
    #[Route(path:'/{jornada}', methods:['GET'])]
    #[OA\Get(
        path: '/api/partidos/{jornada}',
        summary: 'Obtiene todos los partidos de una jornada específica',
        tags: ['Partidos'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array con todos los partidos de una jornada específica'
    )]
    public function getPartidosOfJornada(EntityManagerInterface $em, int $jornada): JsonResponse {
        $partidos = $em->getRepository(Partido::class)->findBy(['jornada' => $jornada]);

        if(!$partidos) {
            return $this->json(['message' => 'No se ha encontrado partidos para esa jornada'], 404);
        }

        $resultado = [
            'jornada_buscada' => $partidos[0]->getJornada()->getId(),
            'partidos' => []
        ];

        foreach ($partidos as $p) {
            $resultado['partidos'][] = $this->toArray($p);
        }

        return new JsonResponse($resultado);
    }

    // ======================= HELPERS =======================
    private function toArray(Partido $partido): array {
        return [
            'id' => $partido->getId(),
            'local' => $partido->getLocalId()->getNombre(),
            'escudo_local' => $partido->getLocalId()->getEscudo(),
            'visitante' => $partido->getVisitanteId()->getNombre(),
            'escudo_visitante' => $partido->getVisitanteId()->getEscudo(),
            'jornada' => $partido->getJornada()->getId(),
            'fecha' => $partido->getDia(),
            'hora' => $partido->getHora()->format('H:i'),
            'local_goles' => $partido->getLocalGoles(),
            'visitante_goles' => $partido->getVisitanteGoles()
        ];
    }

}