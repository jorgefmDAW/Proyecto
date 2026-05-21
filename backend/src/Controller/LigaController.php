<?php

namespace App\Controller;

use App\Entity\Liga;
use App\Repository\LigaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Dto\LigaDto;
use App\Entity\Solicitud;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Entity\UsuarioFantasy;
use DateTime;

#[Route(path:'/api/ligas')]
final class LigaController extends AbstractController {

    public function __construct(
        private LigaRepository $ligaRepository,
        private EntityManagerInterface $em,
    ) {}

    // ======================= GET ALL LIGAS =======================
    #[Route(path:'', methods:['GET'])]
    #[OA\Get(
        path: '/api/ligas',
        summary: 'Obtiene todas las ligas',
        tags: ['Ligas']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene todas las ligas'
    )]
    public function getAllLigas(): JsonResponse {
        $ligas = $this->ligaRepository->findAll();

        if(!$ligas) {
            return $this->json(['message' => 'No se han encontrado ligas'], 404);
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
    public function crearLiga(Request $request, ValidatorInterface $validator, #[CurrentUser] $usuario): JsonResponse 
    {
            $data = json_decode($request->getContent(), true);

            if(!$usuario) {
                return $this->json(['error' => 'Tienes que estar loggeado para poder crear una liga'], 403);
            }

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
            $liga_existente = $this->ligaRepository->findOneBy(['nombre' => $ligaDto->nombre]);

            if($liga_existente) {
                return $this->json(['error' => 'Ya hay una liga con ese nombre'], 409);
            }

            $liga = new Liga();
            $liga->setNombre($ligaDto->nombre);
            $liga->setPrivada($ligaDto->privada);

            $this->em->persist($liga);
            $this->em->flush();

            $usuarioFantasy = new UsuarioFantasy();
            $usuarioFantasy->setPuntosTotales(0);
            $usuarioFantasy->setUsuario($usuario);
            $usuarioFantasy->setLiga($liga);
            $usuarioFantasy->setCreador(true);

            $this->em->persist($usuarioFantasy);
            $this->em->flush();

            $resultados = [
                'message' => 'Liga creada correctamente',
                'liga_creada' => $this->toArray($liga)
            ];

            return $this->json($resultados, 201);
    }

    // ======================= UNIRSE A LIGA PUBLICA =======================
    #[Route(path:'/unirse/{id_liga}', methods:['POST'])]
    #[OA\Post(
        path: '/api/ligas/unirse/{id_liga}',
        summary: 'Unirse a una liga PUBLICA',
        tags: ['Ligas'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Unirse a una liga PUBLICA'
    )]
    public function unirseLigaPublica(int $id_liga, #[CurrentUser] $usuario): JsonResponse 
    {
        if(!$usuario) {
            return $this->json(['error' => 'Tienes que estar loggeado para poder unirte a una liga'], 403);
        }
    
        $liga = $this->ligaRepository->findOneBy(['id' => $id_liga, 'privada' => 0]);

        if(!$liga) {
            return $this->json(['error' => 'No se ha encontrado ninguna liga pública con ese id'], 404);
        }

        $usuarioFantasy = new UsuarioFantasy();
        $usuarioFantasy->setPuntosTotales(0);
        $usuarioFantasy->setUsuario($usuario);
        $usuarioFantasy->setLiga($liga);
        $usuarioFantasy->setCreador(false);

        $this->em->persist($usuarioFantasy);
        $this->em->flush();

        return $this->json(['message' => 'Se ha unido a la liga ' . $liga->getNombre() . ' correctamente']);
    }

    // ======================= SOLICITAR UNIRSE A LIGA PRIVADA =======================
    #[Route(path:'/unirse/solicitar/{id_liga}', methods:['POST'])]
    #[OA\Post(
        path: '/api/ligas/unirse/solicitar/{id_liga}',
        summary: 'Solicitar unirse a una liga PRIVADA',
        tags: ['Ligas'],
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'mensaje', type: 'string', example: 'Aceptad mi solicitud porfa'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Solicitar unirse a una liga PRIVADA'
    )]
    public function solicitarUnirseLigaPrivada(Request $request, int $id_liga, #[CurrentUser] $usuario): JsonResponse 
    {
        if(!$usuario) {
            return $this->json(['error' => 'Tienes que estar loggeado para poder solicitar unirte a una liga'], 403);
        }
    
        $liga = $this->ligaRepository->findOneBy(['id' => $id_liga, 'privada' => 1]);

        if(!$liga) {
            return $this->json(['error' => 'No se ha encontrado ninguna liga privada con ese id'], 404);
        }

        $data = json_decode($request->getContent(), true);

        $mensaje = $data['mensaje'] ?? '';

        $solicitudExistente = $this->em->getRepository(Solicitud::class)->findOneBy(['liga' => $liga, 'usuario' => $usuario]);

        if($solicitudExistente) {
            return $this->json(['error' => 'No puedes hacer otra solicitud a la misma liga'], 403);
        }

        $solicitud = new Solicitud();
        $solicitud->setUsuario($usuario);
        $solicitud->setLiga($liga);
        $solicitud->setFecha(new DateTime());
        $solicitud->setMensaje($mensaje);
        $solicitud->setAceptada(false);

        $this->em->persist($solicitud);
        $this->em->flush();

        return $this->json(['message' => 'Tu solicitud a la liga ' . $liga->getNombre() . ' se ha realizado correctamente']);
    }

    // ======================= HELPERS =======================
    private function toArray(Liga $liga): array {
        return [
            'id' => $liga->getId(),
            'nombre' => $liga->getNombre(),
            'miembros' => $liga->getMiembros(),
            'privada' => $liga->isPrivada()
        ];
    }
}