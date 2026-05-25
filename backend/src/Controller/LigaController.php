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
use App\Entity\Usuario;
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

    // ======================= GET LIGA BY ID =======================
    #[Route(path:'/liga/{id}', methods:['GET'])]
    #[OA\Get(
        path: '/api/ligas/liga/{id}',
        summary: 'Obtiene todas las ligas',
        tags: ['Ligas']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene todas las ligas'
    )]
    public function getLigaById(int $id): JsonResponse {
        $liga = $this->ligaRepository->find($id);

        if(!$liga) {
            return $this->json(['message' => 'No se han encontrado ligas'], 404);
        }

        $ligaResultado = $this->toArray($liga);

        return $this->json($ligaResultado);
    }

    // ======================= GET MIS LIGAS =======================
    #[Route(path:'/mis-ligas', methods:['GET'])]
    #[OA\Get(
        path: '/api/ligas/mis-ligas',
        summary: 'Obtiene las ligas a las que esta unido el usuario',
        tags: ['Ligas']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene las ligas a las que esta unido el usuario'
    )]
    public function getMisLigas(#[CurrentUser] ?Usuario $usuario): JsonResponse {
        if(!$usuario) {
            return $this->json(['error' => 'Tienes que estar loggeado para poder crear una liga'], 403);
        }

        $usuarioFantasy = $this->em->getRepository(UsuarioFantasy::class)->findBy(['usuario' => $usuario]);

        if(!$usuarioFantasy) {
            return $this->json(['message' => 'No se ha encontrado ningun usuario con ligas'], 404);
        }

        $resultados = [
            'ligas' => []
        ];

        foreach($usuarioFantasy as $uf) {
            $resultados['ligas'][] = $this->toArray($uf->getLiga());
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
                new OA\Property(property: 'privada', type: 'boolean', example: '1'),
                new OA\Property(property: 'max_miembros', type: 'integer', example: '12')
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

        // verificar si hay una liga con el mismo nombre
        $liga_existente = $this->ligaRepository->findOneBy(['nombre' => $data['nombre']]);

        if($liga_existente) {
            return $this->json(['error' => 'No puede haber dos ligas con el mismo nombre'], 409);
        }

        $liga = new Liga();
        $liga->setNombre($data['nombre']);
        $liga->setPrivada($data['privada']);
        $liga->setMaxMiembros($data['max_miembros']);

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

        if($liga->getMiembros() == $liga->getMaxMiembros()) {
            return $this->json(['error' => 'No ha sido posible unirse a la liga. La liga está llena'], 403);
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

    // ======================= ELIMINAR NOTICIA =======================
    #[Route(path:'/salirse/{id}', methods:['DELETE'])]
    #[OA\Delete(
        path: '/api/ligas/salirse/{id}',
        summary: 'Elimina al usuario de la liga (elimina el registro de la tabla usuariosfantasy)',
        tags: ['Ligas'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Elimina al usuario de la liga (elimina el registro de la tabla usuariosfantasy)'
    )]
    public function salirseLiga(#[CurrentUser] ?Usuario $usuario, int $id): JsonResponse {
        $liga = $this->em->getRepository(Liga::class)->find($id);

        if(!$liga) {
            return $this->json(['error' => 'Liga no encontrada'], 404);
        }

        if(!$usuario) {
            return $this->json(['error' => 'Tienes que estar loggeado para poder salirte de una liga'], 404);
        }

        $usuarioFantasy = $this->em->getRepository(UsuarioFantasy::class)->findOneBy(['usuario' => $usuario, 'liga' => $liga]);

        $this->em->remove($usuarioFantasy);
        $this->em->flush();

        return $this->json(['message' => 'Te has salido de la liga ' . $liga->getNombre() . ' correctamente'], 200);
    }

    // ======================= HELPERS =======================
    private function toArray(Liga $liga): array {
        return [
            'id' => $liga->getId(),
            'nombre' => $liga->getNombre(),
            'miembros' => $liga->getMiembros(),
            'max_miembros' => $liga->getMaxMiembros(),
            'privada' => $liga->isPrivada()
        ];
    }
}