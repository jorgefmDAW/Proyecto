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
use App\Service\LigaAuthService;
use DateTime;

#[Route(path:'/api/ligas')]
final class LigaController extends AbstractController {

    public function __construct(
        private LigaRepository $ligaRepository,
        private EntityManagerInterface $em,
        private LigaAuthService $ligaAuthService
    ) {}

    // ======================= ENTRAR A MI LIGA =======================
    #[Route('/entrar/{id}', methods: ['PATCH'])] 
    #[OA\Patch(
        path: '/api/ligas/entrar/{id}',
        summary: 'Actualiza la liga actualmente seleccionada por el usuario',
        tags: ['Ligas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID de la nueva liga a la que se desea cambiar',
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Liga actualizada con éxito'),
            new OA\Response(response: 403, description: 'No perteneces a esta liga'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'Liga no encontrada'),
        ]
    )]
    public function entrar(int $id, #[CurrentUser] Usuario $usuario): JsonResponse {
        $usuarioFantasy = $this->ligaAuthService->verificarAcceso($id, $usuario);

        $liga = $usuarioFantasy->getLiga();

        if(!$liga) {
            return $this->json(['message' => 'Liga no encontrada'], 404);
        }

        $usuario->setLigaSeleccionada($liga);

        $this->em->persist($usuario);
        $this->em->flush();

        return $this->json(['mensaje' => 'Liga seleccionada actualizada correctamente']);
    }

    // ======================= GET LIGA SELECCIONADA  =======================
    #[Route(path:'/seleccionada', methods:['GET'])]
    #[OA\Get(
        path: '/api/ligas/seleccionada',
        summary: 'Obtiene la liga seleccionada (en la que el usuario entro por ultima vez)',
        tags: ['Ligas']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene la liga seleccionada (en la que el usuario entro por ultima vez)'
    )]
    public function getLigaSeleccionada(#[CurrentUser] ?Usuario $usuario): JsonResponse {
        if (!$usuario) {
            return $this->json(['message' => 'No autenticado'], 401);
        }

        $ligaSeleccionada = $usuario->getLigaSeleccionada();
        
        if(!$ligaSeleccionada) {
            return $this->json(['message' => 'El usuario no tiene ninguna liga seleccionada'], 404);
        }

        $usuarioFantasy = $this->em->getRepository(UsuarioFantasy::class)->findOneBy(['usuario' => $usuario, 'liga' => $ligaSeleccionada]);

        if(!$usuarioFantasy) {
            return $this->json(['message' => 'El usuario no esta unido a esta liga'], 404);
        }

        $resultado = [
            'liga_seleccionada' => [
                'id' => $ligaSeleccionada->getId(),
                'nombre' => $ligaSeleccionada->getNombre(),
                'creador' => $usuarioFantasy->getCreador()
            ]
        ];

        return $this->json($resultado);
    }

    // ======================= GET LIGAS DISPONIBLES =======================
    #[Route(path:'', methods:['GET'])]
    #[OA\Get(
        path: '/api/ligas',
        summary: 'Obtiene las ligas disponibles para unirse (no muestra en las que el usuario ya se ha unido)',
        tags: ['Ligas']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene las ligas disponibles para unirse (no muestra en las que el usuario ya se ha unido)'
    )]
    public function getLigasDisponibles(#[CurrentUser] ?Usuario $usuario): JsonResponse {
        $ligas = $this->ligaRepository->findAll();
        
        if(!$ligas) {
            return $this->json(['message' => 'No se han encontrado ligas'], 404);
        }

        $resultados = ['ligas' => []];

        // si el usuario ya es miembro de la liga no la muestra en ligas disponibles
        foreach($ligas as $l) {
            $esMiembro = $this->em->getRepository(UsuarioFantasy::class)->findOneBy([
                'usuario' => $usuario,
                'liga' => $l
            ]);

            if(!$esMiembro) {
                $resultados['ligas'][] = $this->toArray($l);
            }
        }

        return $this->json($resultados);
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
        $usuarioFantasy = $this->em->getRepository(UsuarioFantasy::class)->findBy(['usuario' => $usuario]);

        if(!$usuarioFantasy) {
            return $this->json(['message' => 'No se ha encontrado ningun usuario con ligas'], 404);
        }

        $resultados = [
            'ligas' => []
        ];

        foreach($usuarioFantasy as $uf) {
            $resultados['ligas'][] = $this->toArrayMisLigas($uf->getLiga(), $uf);
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
    #[Route(path:'/unirse/{id}', methods:['POST'])]
    #[OA\Post(
        path: '/api/ligas/unirse/{id}',
        summary: 'Unirse a una liga PUBLICA',
        tags: ['Ligas'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Unirse a una liga PUBLICA'
    )]
    public function unirseLigaPublica(int $id, #[CurrentUser] $usuario): JsonResponse 
    {
        if(!$usuario) {
            return $this->json(['error' => 'Tienes que estar loggeado para poder unirte a una liga'], 403);
        }
    
        $liga = $this->ligaRepository->findOneBy(['id' => $id, 'privada' => 0]);

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
    #[Route(path:'/unirse/solicitar/{id}', methods:['POST'])]
    #[OA\Post(
        path: '/api/ligas/unirse/solicitar/{id}',
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
    public function solicitarUnirseLigaPrivada(Request $request, int $id, #[CurrentUser] $usuario): JsonResponse 
    {
        if(!$usuario) {
            return $this->json(['error' => 'Tienes que estar loggeado para poder solicitar unirte a una liga'], 403);
        }
    
        $liga = $this->ligaRepository->findOneBy(['id' => $id, 'privada' => 1]);

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
        $solicitud->setMensaje($mensaje);
        $solicitud->setAceptada(false);

        $this->em->persist($solicitud);
        $this->em->flush();

        return $this->json(['message' => 'Tu solicitud a la liga ' . $liga->getNombre() . ' se ha realizado correctamente']);
    }

    // ======================= ABANDONAR LIGA =======================
    #[Route(path:'/abandonar/{id}', methods:['DELETE'])]
    #[OA\Delete(
        path: '/api/ligas/abandonar/{id}',
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

        // si el usuario que abandona la liga es el único miembro de esta, la liga se elimina
        if($liga->getMiembros()<=1) {
            $this->em->remove($liga);
        }
        else {
            $usuarioFantasy = $this->em->getRepository(UsuarioFantasy::class)->findOneBy(['usuario' => $usuario, 'liga' => $liga]);
            $this->em->remove($usuarioFantasy);
        }
        
        $this->em->flush();

        return $this->json(['message' => 'Te has salido de la liga ' . $liga->getNombre() . ' correctamente'], 200);
    }

    // ======================= GET LIGA BY ID =======================
    #[Route('/{id}', methods: ['GET'])]
    #[OA\Get(
        path: '/api/ligas/{id}',
        summary: 'Obtiene los datos de una liga',
        tags: ['Ligas'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'integer')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Datos de la liga'),
            new OA\Response(response: 403, description: 'No perteneces a esta liga'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function getLigaById(int $id, #[CurrentUser] Usuario $usuario): JsonResponse {
        $usuarioFantasy = $this->ligaAuthService->verificarAcceso($id, $usuario);
        $liga = $usuarioFantasy->getLiga();

        return $this->json([
            'id' => $liga->getId(),
            'nombre' => $liga->getNombre(),
            'privada' => $liga->isPrivada(),
            'miembros' => $liga->getMiembros(),
            'max_miembros' => $liga->getMaxMiembros(),
        ]);
    }

    // ======================= GET CLASIFICACION OF LIGA =======================
    #[Route(path:'/clasificacion/{id}', methods:['GET'])]
    #[OA\Get(
        path: '/api/ligas/clasificacion/{id}',
        summary: 'Obtiene la clasificacion de los usuarios de la liga',
        tags: ['Ligas']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene la clasificacion de los usuarios de la liga'
    )]
    public function getClasificacion(#[CurrentUser] ?Usuario $usuario, int $id): JsonResponse {
        $usuariosLiga = $this->em->getRepository(UsuarioFantasy::class)->findBy(['liga' => $id], ['puntosTotales'=> 'DESC']);
        
        if(!$usuariosLiga) {
            return $this->json(['message' => 'No se han encontrado usuarios en esa liga'], 404);
        }

        $resultados = ['clasificacion' => []];
        $posicion = 1;

        foreach($usuariosLiga as $ul) {
            $resultados['clasificacion'][] = [ 
                'id'=> $ul->getId(),
                'posicion' => $posicion,
                'usuario'=> $ul->getUsuario()->getUsername(),
                'puntos'=> $ul->getPuntosTotales(),
            ];

            $posicion++;
        }

        return $this->json($resultados);
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

    private function toArrayMisLigas(Liga $liga, UsuarioFantasy $usuarioFantasy): array {
        return [
            'id' => $liga->getId(),
            'nombre' => $liga->getNombre(),
            'miembros' => $liga->getMiembros(),
            'max_miembros' => $liga->getMaxMiembros(),
            'creador' => $usuarioFantasy->getCreador()
        ];
    }
}