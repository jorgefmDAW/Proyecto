<?php

namespace App\Controller;

use App\Entity\EleccionFantasy;
use App\Entity\Jugador;
use App\Entity\Equipo;
use App\Entity\Partido;
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

    // ======================= GET ELECCIONES BY JORNADA =======================
    #[Route(path: '/jornada/{jornada_id}/liga/{liga_id}', methods: ['GET'])]
    #[OA\Get(
        path: '/api/elecciones/jornada/{jornada_id}/liga/{liga_id}',
        summary: 'Obtiene las elecciones del usuario autenticado para una jornada y liga específica',
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
        // Sacamos el usuario del token JWT
        $usuario = $this->getUser();

        // Buscamos su UsuarioFantasy para esa liga
        $usuarioFantasy = $em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuario,
            'liga'    => $liga_id
        ]);

        if (!$usuarioFantasy) {
            return $this->json(['message' => 'No perteneces a esta liga'], 403);
        }

        // Buscamos la jornada
        $jornada = $em->getRepository(Jornada::class)->find($jornada_id);

        if (!$jornada) {
            return $this->json(['message' => 'Jornada no encontrada'], 404);
        }

        // Recorremos los partidos de la jornada y buscamos la elección para cada uno
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
                'equipo_elegido'   => $eleccion?->getEquipo()?->getNombre(),
                'puntos_obtenidos' => $eleccion?->getPuntosObtenidos(),
            ];
        }

        return $this->json([
            'jornada_id'  => $jornada_id,
            'liga_id'     => $liga_id,
            'elecciones'  => $resultado
        ]);
    }


    // ======================= PATCH ELEGIR JUGADOR =======================
    #[Route(path: '/partido/{partido_id}/liga/{liga_id}/jugador', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/elecciones/partido/{partido_id}/liga/{liga_id}/jugador',
        summary: 'Elige o cambia el jugador estrella para un partido en una liga específica',
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
        $data = json_decode($request->getContent(), true);

        if (!isset($data['jugador_id'])) {
            return $this->json(['message' => 'Falta el campo jugador_id'], 400);
        }

        // Verificamos que el usuario pertenece a la liga
        $usuarioFantasy = $em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuario,
            'liga'    => $liga_id
        ]);

        if (!$usuarioFantasy) {
            return $this->json(['message' => 'No perteneces a esta liga'], 403);
        }

        // Verificamos que el partido existe
        $partido = $em->getRepository(Partido::class)->find($partido_id);

        if (!$partido) {
            return $this->json(['message' => 'Partido no encontrado'], 404);
        }

        // Verificamos que el jugador existe
        $jugador = $em->getRepository(Jugador::class)->find($data['jugador_id']);

        if (!$jugador) {
            return $this->json(['message' => 'Jugador no encontrado'], 404);
        }

        // Verificamos que el jugador pertenece a uno de los dos equipos del partido
        $equipoLocal     = $partido->getLocalId();
        $equipoVisitante = $partido->getVisitanteId();

        if (
            $jugador->getEquipo()->getId() !== $equipoLocal->getId() &&
            $jugador->getEquipo()->getId() !== $equipoVisitante->getId()
        ) {
            return $this->json(['message' => 'El jugador no pertenece a ninguno de los equipos de este partido'], 400);
        }

        // Buscamos la elección existente o creamos una nueva
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

        $em->flush();

        return $this->json([
            'message'         => 'Jugador elegido correctamente',
            'partido_id'      => $partido->getId(),
            'jugador_elegido' => $jugador->getNombre(),
        ]);
    }


    // ======================= PATCH ELEGIR EQUIPO =======================
    #[Route(path: '/partido/{partido_id}/liga/{liga_id}/equipo', methods: ['PATCH'])]
    #[OA\Patch(
        path: '/api/elecciones/partido/{partido_id}/liga/{liga_id}/equipo',
        summary: 'Elige o cambia el equipo ganador para un partido en una liga específica',
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
        $data = json_decode($request->getContent(), true);

        if (!isset($data['equipo_id'])) {
            return $this->json(['message' => 'Falta el campo equipo_id'], 400);
        }

        // Verificamos que el usuario pertenece a la liga
        $usuarioFantasy = $em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuario,
            'liga'    => $liga_id
        ]);

        if (!$usuarioFantasy) {
            return $this->json(['message' => 'No perteneces a esta liga'], 403);
        }

        // Verificamos que el partido existe
        $partido = $em->getRepository(Partido::class)->find($partido_id);

        if (!$partido) {
            return $this->json(['message' => 'Partido no encontrado'], 404);
        }

        // Verificamos que el equipo existe
        $equipo = $em->getRepository(Equipo::class)->find($data['equipo_id']);

        if (!$equipo) {
            return $this->json(['message' => 'Equipo no encontrado'], 404);
        }

        // Verificamos que el equipo elegido es uno de los dos del partido
        $equipoLocal     = $partido->getLocalId();
        $equipoVisitante = $partido->getVisitanteId();

        if (
            $equipo->getId() !== $equipoLocal->getId() &&
            $equipo->getId() !== $equipoVisitante->getId()
        ) {
            return $this->json(['message' => 'El equipo no juega en este partido'], 400);
        }

        // Buscamos la elección existente o creamos una nueva
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

        $eleccion->setEquipo($equipo);
        $eleccion->setActualizadoEn(new \DateTime());

        $em->flush();

        return $this->json([
            'message'        => 'Equipo elegido correctamente',
            'partido_id'     => $partido->getId(),
            'equipo_elegido' => $equipo->getNombre(),
        ]);
    }
}