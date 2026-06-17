<?php

namespace App\Controller;

use App\Entity\EleccionFantasy;
use App\Entity\Jugador;
use App\Entity\Equipo;
use App\Entity\Partido;
use App\Entity\Puntuacion;
use App\Entity\UsuarioFantasy;
use App\Entity\Jornada;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use OpenApi\Attributes as OA;

#[Route(path: '/api/elecciones')]
final class EleccionFantasyController extends AbstractController
{
    private function recalcularPuntosEleccion(EleccionFantasy $eleccion, Jornada $jornada, EntityManagerInterface $em): int
    {
        $partido = $eleccion->getPartido();
        $puntos  = 0;

        $golesLocal     = $partido->getLocalGoles();
        $golesVisitante = $partido->getVisitanteGoles();

        if ($golesLocal === null || $golesVisitante === null) {
            return 0;
        }

        if ($golesLocal > $golesVisitante) {
            $equipoGanador = $partido->getLocalId();
        } elseif ($golesVisitante > $golesLocal) {
            $equipoGanador = $partido->getVisitanteId();
        } else {
            $equipoGanador = null;
        }

        $equipoElegido = $eleccion->getEquipo();
        
        if ($equipoGanador === null) {
            if ($equipoElegido === null || strtolower($equipoElegido->getNombre()) === 'empate') {
                $puntos += 5;
            }
        } else {
            if ($equipoElegido !== null && $equipoElegido->getId() === $equipoGanador->getId()) {
                $puntos += 5;
            }
        }

        $jugadorElegido = $eleccion->getJugador();
        if ($jugadorElegido !== null) {
            $puntuacion = $em->getRepository(Puntuacion::class)->findOneBy([
                'jugador' => $jugadorElegido,
                'jornada' => $jornada,
            ]);

            if ($puntuacion !== null) {
                $puntos += $puntuacion->getPuntos();
            }
        }

        return $puntos;
    }

    private function actualizarPuntosTotales(UsuarioFantasy $usuarioFantasy, EntityManagerInterface $em): void
    {
        $todasElecciones = $em->getRepository(EleccionFantasy::class)->findBy([
            'usuarioFantasy' => $usuarioFantasy,
        ]);

        $sumaTotal = 0;
        foreach ($todasElecciones as $e) {
            $sumaTotal += $e->getPuntosObtenidos() ?? 0;
        }

        $usuarioFantasy->setPuntosTotales($sumaTotal);
        
        $em->persist($usuarioFantasy);
    }

    #[Route(path: '/jornada/{jornada_id}/liga/{liga_id}', methods: ['GET'])]
    #[OA\Get(
        path: '/api/elecciones/jornada/{jornada_id}/liga/{liga_id}',
        summary: 'Obtiene las elecciones del usuario autenticado para una jornada y liga especifica',
        tags: ['Elecciones Fantasy']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve las elecciones del usuario para cada partido de la jornada'
    )]
    public function getEleccionesByJornada(
        EntityManagerInterface $em,
        int $jornada_id,
        int $liga_id
    ): JsonResponse {
        $usuario = $this->getUser();

        $usuarioFantasy = $em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuario,
            'liga'    => $liga_id
        ]);

        if (!$usuarioFantasy) {
            return $this->json(['message' => 'No perteneces a esta liga'], 403);
        }

        $jornada = $em->getRepository(Jornada::class)->find($jornada_id);

        if (!$jornada) {
            return $this->json(['message' => 'Jornada no encontrada'], 404);
        }

        $resultado = [];
        foreach ($jornada->getPartidos() as $partido) {
            $eleccion = $em->getRepository(EleccionFantasy::class)->findOneBy([
                'usuarioFantasy' => $usuarioFantasy,
                'partido'        => $partido
            ]);

            $resultado[] = [
                'partido_id'       => $partido->getId(),
                'local'            => $partido->getLocalId()->getNombre(),
                'visitante'        => $partido->getVisitanteId()->getNombre(),
                'jugador_elegido'  => $eleccion?->getJugador()?->getNombre(),
                'jugador_foto'     => $eleccion?->getJugador()?->getFoto(),   
                'equipo_foto'      => $eleccion?->getJugador()?->getEquipo()?->getEscudo(),
                'jugador_posicion' => $eleccion?->getJugador()?->getPosicion(),   
                'equipo_elegido'   => $eleccion?->getEquipo()?->getNombre() ?? ($eleccion ? 'empate' : null),
                'puntos_obtenidos' => $eleccion?->getPuntosObtenidos(),
            ];
        }

        return $this->json([
            'jornada_id' => $jornada_id,
            'liga_id'    => $liga_id,
            'elecciones' => $resultado
        ]);
    }

    #[Route(path: '/partido/{partido_id}/liga/{liga_id}/jugador', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/elecciones/partido/{partido_id}/liga/{liga_id}/jugador',
        summary: 'Elige o cambia el jugador estrella para un partido en una liga especifica',
        tags: ['Elecciones Fantasy']
    )]
    #[OA\Response(
        response: 200,
        description: 'Jugador elegido correctamente'
    )]
    public function patchJugador(
        EntityManagerInterface $em,
        Request $request,
        int $partido_id,
        int $liga_id
    ): JsonResponse {
        $usuario = $this->getUser();
        $data    = json_decode($request->getContent(), true);

        if (!isset($data['jugador_id'])) {
            return $this->json(['message' => 'Falta el campo jugador_id'], 400);
        }

        $usuarioFantasy = $em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuario,
            'liga'    => $liga_id
        ]);

        if (!$usuarioFantasy) {
            return $this->json(['message' => 'No perteneces a esta liga'], 403);
        }

        $partido = $em->getRepository(Partido::class)->find($partido_id);

        if (!$partido) {
            return $this->json(['message' => 'Partido no encontrado'], 404);
        }

        $jugador = $em->getRepository(Jugador::class)->find($data['jugador_id']);

        if (!$jugador) {
            return $this->json(['message' => 'Jugador no encontrado'], 404);
        }

        $equipoLocal     = $partido->getLocalId();
        $equipoVisitante = $partido->getVisitanteId();

        if (
            $jugador->getEquipo()->getId() !== $equipoLocal->getId() &&
            $jugador->getEquipo()->getId() !== $equipoVisitante->getId()
        ) {
            return $this->json(['message' => 'El jugador no pertenece a ninguno de los equipos de este partido'], 400);
        }

        $eleccion = $em->getRepository(EleccionFantasy::class)->findOneBy([
            'usuarioFantasy' => $usuarioFantasy,
            'partido'        => $partido
        ]);

        if (!$eleccion) {
            $eleccion = new EleccionFantasy();
            $eleccion->setUsuarioFantasy($usuarioFantasy);
            $eleccion->setPartido($partido);
            $eleccion->setCreadoEn(new \DateTime());
            $em->persist($eleccion);
        }

        $eleccion->setJugador($jugador);
        $eleccion->setActualizadoEn(new \DateTime());

        $jornada = $partido->getJornada();
        if ($jornada !== null && $partido->getLocalGoles() !== null && $partido->getVisitanteGoles() !== null) {
            $puntosNuevos = $this->recalcularPuntosEleccion($eleccion, $jornada, $em);
            $eleccion->setPuntosObtenidos($puntosNuevos);
            
            $em->flush(); 

            $this->actualizarPuntosTotales($usuarioFantasy, $em);
        }

        $em->flush();

        return $this->json([
            'message'         => 'Jugador elegido correctamente',
            'partido_id'      => $partido->getId(),
            'jugador_elegido' => $jugador->getNombre(),
        ]);
    }

    #[Route(path: '/partido/{partido_id}/liga/{liga_id}/equipo', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/elecciones/partido/{partido_id}/liga/{liga_id}/equipo',
        summary: 'Elige o cambia el equipo ganador para un partido en una liga especifica',
        tags: ['Elecciones Fantasy']
    )]
    #[OA\Response(
        response: 200,
        description: 'Equipo elegido correctamente'
    )]
    public function patchEquipo(
        EntityManagerInterface $em,
        Request $request,
        int $partido_id,
        int $liga_id
    ): JsonResponse {
        $usuario = $this->getUser();
        $data    = json_decode($request->getContent(), true);

        $usuarioFantasy = $em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuario,
            'liga'    => $liga_id
        ]);

        if (!$usuarioFantasy) {
            return $this->json(['message' => 'No perteneces a esta liga'], 403);
        }

        $partido = $em->getRepository(Partido::class)->find($partido_id);

        if (!$partido) {
            return $this->json(['message' => 'Partido no encontrado'], 404);
        }

        $eleccion = $em->getRepository(EleccionFantasy::class)->findOneBy([
            'usuarioFantasy' => $usuarioFantasy,
            'partido'        => $partido
        ]);

        if (!$eleccion) {
            $eleccion = new EleccionFantasy();
            $eleccion->setUsuarioFantasy($usuarioFantasy);
            $eleccion->setPartido($partido);
            $eleccion->setCreadoEn(new \DateTime());
            $em->persist($eleccion);
        }

        $eleccion->setActualizadoEn(new \DateTime());

        if (isset($data['empate']) && $data['empate'] === true) {
            $eleccion->setEquipo(null);
        } else {
            if (!isset($data['equipo_id'])) {
                return $this->json(['message' => 'Falta el campo equipo_id'], 400);
            }

            $equipo = $em->getRepository(Equipo::class)->find($data['equipo_id']);

            if (!$equipo) {
                return $this->json(['message' => 'Equipo no encontrado'], 404);
            }

            $equipoLocal     = $partido->getLocalId();
            $equipoVisitante = $partido->getVisitanteId();

            if (
                $equipo->getId() !== $equipoLocal->getId() &&
                $equipo->getId() !== $equipoVisitante->getId()
            ) {
                return $this->json(['message' => 'El equipo no juega en este partido'], 400);
            }

            $eleccion->setEquipo($equipo);
        }

        $jornada = $partido->getJornada();
        if ($jornada !== null && $partido->getLocalGoles() !== null && $partido->getVisitanteGoles() !== null) {
            $puntosNuevos = $this->recalcularPuntosEleccion($eleccion, $jornada, $em);
            $eleccion->setPuntosObtenidos($puntosNuevos);
            
            $em->flush(); 

            $this->actualizarPuntosTotales($usuarioFantasy, $em);
        }

        $em->flush();

        return $this->json([
            'message'        => 'Equipo elegido correctamente',
            'partido_id'     => $partido->getId(),
            'equipo_elegido' => $eleccion->getEquipo()?->getNombre() ?? 'empate',
        ]);
    }
}