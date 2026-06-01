<?php

namespace App\Controller;

use App\Entity\Jugador;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use OpenApi\Attributes as OA;

#[Route(path:'/api/jugadores')]
final class JugadorController extends AbstractController {

    // ======================= GET JUGADOR BY ID =======================
    #[Route(path:'/id/{jugador_id}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/id/{jugador_id}',
        summary: 'Obtiene un jugador con un id específico',
        tags: ['Jugadores']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un jugador con un id específico'
    )]
    public function getJugadorById(EntityManagerInterface $em, int $jugador_id): JsonResponse {
        $jugador = $em->getRepository(Jugador::class)->find($jugador_id);

        if(!$jugador) {
            return $this->json(['message' => 'No se han encontrado jugadores con ese id'], 404);
        }

        return $this->json($this->toArray($jugador));
    }

    // ======================= GET JUGADORES OF EQUIPO =======================
    #[Route(path:'/equipo/{equipo_id}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/equipo/{equipo_id}',
        summary: 'Obtiene la lista de todos los jugadores de un equipo específico',
        tags: ['Jugadores']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array de jugadores de un equipo específico'
    )]
    public function getJugadoresOfEquipo(EntityManagerInterface $em, int $equipo_id): JsonResponse {
        $jugadores = $em->getRepository(Jugador::class)->findBy(['equipo' => $equipo_id]);

        if(!$jugadores) {
            return $this->json(['message' => 'No se han encontrado jugadores en ese equipo',
                                'equipos_disponibles' => '1-20'], 404);
        }

        $resultado = [
            'equipo_buscado' => $jugadores[0]->getEquipo()->getNombre(),
            'jugadores' => []
        ];

        foreach ($jugadores as $j) {
            $resultado['jugadores'][] = $this->toArray($j);
        }

        return new JsonResponse($resultado);
    }

    // ======================= GET JUGADORES OF 2 EQUIPOS =======================
    #[Route(path:'/equipos/{equipo_id1}/{equipo_id2}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/equipos/{equipo_id1}/{equipo_id2}',
        summary: 'Obtiene la lista de todos los jugadores de dos equipos',
        tags: ['Jugadores']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array de jugadores de dos equipos'
    )]
    public function getJugadoresOf2Equipos(EntityManagerInterface $em, int $equipo_id1, int $equipo_id2): JsonResponse {
        $jugadores_equipo1 = $em->getRepository(Jugador::class)->findBy(['equipo' => $equipo_id1]);
        $jugadores_equipo2 = $em->getRepository(Jugador::class)->findBy(['equipo' => $equipo_id2]);

        if(!$jugadores_equipo1) {
            return $this->json(['message' => 'No se han encontrado jugadores en el primer equipo',
                                'equipos_disponibles' => '1-20'], 404);
        }

        if(!$jugadores_equipo2) {
            return $this->json(['message' => 'No se han encontrado jugadores en el segundo equipo',
                                'equipos_disponibles' => '1-20'], 404);
        }

        $resultado = [
            'equipo_buscado_1' => $jugadores_equipo1[0]->getEquipo()->getNombre(),
            'jugadores_equipo_1' => [],
            'equipo_buscado_2' => $jugadores_equipo2[0]->getEquipo()->getNombre(),
            'jugadores_equipo_2' => [],
            
        ];

        foreach ($jugadores_equipo1 as $j1) {
            $resultado['jugadores_equipo_1'][] = $this->toArray($j1);
        }

        foreach ($jugadores_equipo2 as $j2) {
            $resultado['jugadores_equipo_2'][] = $this->toArray($j2);
        }

        return new JsonResponse($resultado);
    }

    // ======================= GET JUGADORES OF EQUIPO BY POSICION =======================
    #[Route(path:'/equipo/{equipo_id}/posicion/{posicion}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/equipo/{equipo_id}/posicion/{posicion}',
        summary: 'Obtiene los jugadores de un equipo y posicion específica',
        tags: ['Jugadores']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array de jugadores de un equipo y posicion específica | Equipos 1-20 / Posiciones POR DEF MED DEL'
    )]
    public function getJugadoresOfEquipoByPosicion(EntityManagerInterface $em, int $equipo_id , string $posicion): JsonResponse {
        $jugadores = $em->getRepository(Jugador::class)->findBy(['posicion' => $posicion, 'equipo' => $equipo_id]);

        if(!$jugadores) {
            return $this->json(['message' => 'No se han encontrado jugadores de ese equipo en esa posición',
                                'posiciones_disponibles' => 'POR, DEF, MED, DEL',
                                'equipos_disponibles' => '1-20'], 404);
        }

        $resultado = [
            'equipo_buscado' => $jugadores[0]->getEquipo()->getNombre(),
            'posicion_buscada' => $jugadores[0]->getPosicion(),
            'jugadores' => []
        ];

        foreach ($jugadores as $j) {
            $resultado['jugadores'][] = $this->toArray($j);
        }

        return new JsonResponse($resultado);
    }

    // ======================= GET JUGADORES OF EQUIPO BY NACIONALIDAD =======================
    #[Route(path:'/equipo/{equipo_id}/nacionalidad/{nacionalidad}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/equipo/{equipo_id}/nacionalidad/{nacionalidad}',
        summary: 'Obtiene los jugadores de un equipo y nacionalidad específica',
        tags: ['Jugadores']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene la lista de jugadores de un equipo y nacionalidad específica'
    )]
    public function getJugadorOfEquipoByNacionalidad(EntityManagerInterface $em, int $equipo_id, string $nacionalidad): JsonResponse {
        $jugadores = $em->getRepository(Jugador::class)->findBy(['equipo' => $equipo_id, 'nacionalidad' => $nacionalidad]);

        if(!$jugadores) {
            return $this->json(['message' => 'No se han encontrado jugadores de ese equipo con esa nacionalidad'], 404);
        }

        $resultados = [
            'equipo_buscado' => $jugadores[0]->getEquipo()->getNombre(),
            'nacionalidad_buscada' => $nacionalidad,
            'jugadores' => []
        ];

        foreach ($jugadores as $j) {
            $resultados['jugadores'][] = $this->toArray($j);
        }

        return $this->json($resultados);
    }

    // ======================= GET JUGADORES OF EQUIPO BY NACIONALIDAD AND POSICION =======================
    #[Route(path:'/equipo/{equipo_id}/nacionalidad/{nacionalidad}/posicion/{posicion}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/equipo/{equipo_id}/nacionalidad/{nacionalidad}/posicion/{posicion}',
        summary: 'Obtiene un jugador de un equipo, nacionalidad y posicion específica',
        tags: ['Jugadores']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene un jugador de un equipo, nacionalidad y posicion específica'
    )]
    public function getJugadorOfEquipoByNacionalidadAndPosicion(EntityManagerInterface $em, int $equipo_id, string $nacionalidad, string $posicion): JsonResponse {
        $jugadores = $em->getRepository(Jugador::class)->findBy(['equipo' => $equipo_id, 'nacionalidad' => $nacionalidad, 'posicion' => $posicion]);

        if(!$jugadores) {
            return $this->json(['message' => 'No se han encontrado jugadores de ese equipo con esa nacionalidad en esa posicion'], 404);
        }

        $resultados = [
            'equipo_buscado' => $jugadores[0]->getEquipo()->getNombre(),
            'nacionalidad_buscada' => $nacionalidad,
            'posicion_buscada' => $jugadores[0]->getPosicion(),
            'jugadores' => []
        ];

        foreach ($jugadores as $j) {
            $resultados['jugadores'][] = $this->toArray($j);
        }

        return $this->json($resultados);
    }

    // ======================= GET TOP 10 JUGADORES =======================
    #[Route(path:'/top10', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/top10',
        summary: 'Obtiene los 10 jugadores con mas puntos totales',
        tags: ['Jugadores - Top 10']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array de los 10 jugadores con mas puntos totales'
    )]
    public function getTop10Jugadores(EntityManagerInterface $em): JsonResponse {
        $top10Jugadores = $em->getRepository(Jugador::class)->findTop10Jugadores();

        return new JsonResponse($top10Jugadores);
    }

    // ======================= GET TOP 10 JUGADORES OF EQUIPO =======================
    #[Route(path:'/top10/equipo/{equipo_id}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/top10/equipo/{equipo_id}',
        summary: 'Obtiene los 10 jugadores con mas puntos totales de un equipo espefífico',
        tags: ['Jugadores - Top 10']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array de los 10 jugadores con mas puntos totales de un equipo específico'
    )]
    public function getTop10JugadoresOfEquipo(EntityManagerInterface $em, int $equipo_id): JsonResponse {
        $top10Jugadores = $em->getRepository(Jugador::class)->findtop10Jugadores($equipo_id, null);

        if(!$top10Jugadores) {
            return $this->json(['message' => 'No se han encontrado jugadores en esa posicion',
                                'equipos_disponibles' => '1-20'], 404);
        }

        return new JsonResponse($top10Jugadores);
    }

    // ======================= GET TOP 10 JUGADORES BY POSICION =======================
    #[Route(path:'/top10/posicion/{posicion}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/top10/posicion/{posicion}',
        summary: 'Obtiene los 10 jugadores con mas puntos totales de una posicion espefífica',
        tags: ['Jugadores - Top 10']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array de los 10 jugadores con mas puntos totales de una posicion específica'
    )]
    public function getTop10JugadoresByPosicion(EntityManagerInterface $em, string $posicion): JsonResponse {
        $top10Jugadores = $em->getRepository(Jugador::class)->findtop10Jugadores(null, $posicion);

        if(!$top10Jugadores) {
            return $this->json(['message' => 'No se han encontrado jugadores en esa posicion',
                                'posiciones_disponibles' => 'POR, DEF, MED, DEL'], 404);
        }

        return new JsonResponse($top10Jugadores);
    }

    // ======================= GET TOP 10 JUGADORES OF EQUIPO BY POSICION =======================
    #[Route(path:'/top10/equipo/{equipo_id}/posicion/{posicion}', methods:['GET'])]
    #[OA\Get(
        path: '/api/jugadores/top10/equipo/{equipo_id}/posicion/{posicion}',
        summary: 'Obtiene los 10 jugadores con mas puntos totales de un equipo y posicion espefífica',
        tags: ['Jugadores - Top 10']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array de los 10 jugadores con mas puntos totales de un equipo y posicion específica'
    )]
    public function getTop10JugadoresOfEquipoByPosicion(EntityManagerInterface $em, int $equipo_id, string $posicion, ): JsonResponse {
        $top10Jugadores = $em->getRepository(Jugador::class)->findtop10Jugadores($equipo_id, $posicion);

        if(!$top10Jugadores) {
            return $this->json(['message' => 'No se han encontrado jugadores de ese equipo en esa posicion',
                                'posiciones_disponibles' => 'POR, DEF, MED, DEL',
                                'equipos_disponibles' => '1-20'], 404);
        }

        return new JsonResponse($top10Jugadores);
    }

    // ======================= HELPERS =======================
    private function toArray(Jugador $jugador): array {
        return [
            'id' => $jugador->getId(),
            'nombre' => $jugador->getNombre(),
            'posicion' => $jugador->getPosicion(),
            'edad' => $jugador->getEdad(),
            'nacionalidad' => $jugador->getNacionalidad(),
            'equipo' => $jugador->getEquipo()->getNombre(),
            'foto' => $jugador->getFoto(),
            'puntos_totales' => $jugador->sumaPuntos(),
            'puntos_por_jornada' => $this->collectionToArray($jugador->getPuntuacions()),
        ];
    }

    private function collectionToArray(Collection $puntos_por_jornada) {
        $listaPuntos = [];
        foreach ($puntos_por_jornada as $p) {
            $listaPuntos[] = [
                'jornada' => $p->getJornada()->getId(), 
                'puntos' => $p->getPuntos()
            ];
        }

        return $listaPuntos;
    }

}