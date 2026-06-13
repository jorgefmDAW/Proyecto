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

#[Route(path:'/api/solicitudes')]
final class SolicitudController extends AbstractController {

    public function __construct(
        private LigaRepository $ligaRepository,
        private EntityManagerInterface $em,
        private LigaAuthService $ligaAuthService
    ) {}

    // ======================= GET SOLICITUDES OF LIGA =======================
    #[Route(path:'/{liga_id}', methods:['GET'])]
    #[OA\Get(
        path: '/api/solicitudes/{liga_id}',
        summary: 'Obtiene las solicitudes de union de la liga',
        tags: ['Solicitudes']
    )]
    #[OA\Response(
        response: 200,
        description: 'Obtiene las solicitudes de union de la liga'
    )]
    public function getSolicitudes(#[CurrentUser] ?Usuario $usuarioLogueado, int $liga_id): JsonResponse 
    {
        // 1. Validar que el usuario está logueado
        if (!$usuarioLogueado) {
            return $this->json(['error' => 'Tienes que estar logueado'], 401);
        }

        // 2. Comprobar que el usuario LOGUEADO es el creador de esta liga (findOneBy en vez de findBy)
        $esCreador = $this->em->getRepository(UsuarioFantasy::class)->findOneBy([
            'liga' => $liga_id, 
            'usuario' => $usuarioLogueado, // Comprobamos que sea él específicamente
            'creador' => true
        ]);

        if(!$esCreador) {
            return $this->json(['error' => 'Solo el creador puede ver las solicitudes'], 403);
        }

        // 3. Buscar las solicitudes
        $solicitudes = $this->em->getRepository(Solicitud::class)->findBy(['liga' => $liga_id]);

        $resultados = ['solicitudes' => []];

        // 4. Formatear la respuesta (corregido el typo de "solcitudes")
        foreach($solicitudes as $s) {
            $resultados['solicitudes'][] = $this->toArray($s);
        }

        return $this->json($resultados);
    }

    // ======================= ACEPTAR SOLICITUD =======================
    #[Route(path:'/aceptar/{id}', methods:['POST'])]
    #[OA\Post(
        path: '/api/solicitudes/aceptar/{id}',
        summary: 'Acepta una solicitud y une al usuario a la liga',
        tags: ['Solicitudes'],
    )]
    #[OA\Response(
        response: 200,
        description: 'Solicitud aceptada y usuario unido a la liga'
    )]
    public function aceptarSolicitud(int $id, #[CurrentUser] ?Usuario $admin): JsonResponse 
    {
        if(!$admin) {
            return $this->json(['error' => 'Tienes que estar loggeado para gestionar solicitudes'], 401);
        }

        $solicitud = $this->em->getRepository(Solicitud::class)->find($id);

        if (!$solicitud) {
            return $this->json(['error' => 'No se ha encontrado la solicitud'], 404);
        }

        $liga = $solicitud->getLiga();
        $usuarioSolicitante = $solicitud->getUsuario();

        $esCreador = $this->em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $admin,
            'liga' => $liga,
            'creador' => true
        ]);

        if (!$esCreador) {
            return $this->json(['error' => 'Solo el creador de la liga puede aceptar solicitudes'], 403);
        }

        if ($solicitud->isAceptada()) {
            return $this->json(['error' => 'Esta solicitud ya fue aceptada'], 400);
        }

        if($liga->getMiembros() >= $liga->getMaxMiembros()) {
            return $this->json(['error' => 'No ha sido posible unir al usuario. La liga ya está llena'], 403);
        }

        $yaPertenece = $this->em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuarioSolicitante,
            'liga' => $liga
        ]);

        if ($yaPertenece) {
            return $this->json(['error' => 'Este usuario ya pertenece a la liga'], 400);
        }

        $solicitud->setAceptada(true);

        $usuarioFantasy = new UsuarioFantasy();
        $usuarioFantasy->setPuntosTotales(0);
        $usuarioFantasy->setUsuario($usuarioSolicitante); 
        $usuarioFantasy->setLiga($liga);
        $usuarioFantasy->setCreador(false);

        $this->em->persist($solicitud);
        $this->em->persist($usuarioFantasy);
        $this->em->flush();

        return $this->json([
            'message' => 'Solicitud aceptada. ' . $usuarioSolicitante->getUsername() . ' se ha unido a la liga ' . $liga->getNombre() . ' correctamente'
        ]);
    }

    // ======================= HELPERS =======================
    private function toArray(Solicitud $solicitud): array {
        return [
            'id' => $solicitud->getId(),
            
            // Extraemos solo lo necesario del usuario para no romper el serializador
            'usuario' => [
                'usuario_id' => $solicitud->getUsuario()->getId(), 
                'username'   => $solicitud->getUsuario()->getUsername()
            ],
            
            // Hacemos lo mismo con la liga (ajusta los getters si se llaman diferente en tu entidad)
            'liga' => [
                'id'     => $solicitud->getLiga()->getId(),
                'nombre' => $solicitud->getLiga()->getNombre()
            ],
            
            'mensaje' => $solicitud->getMensaje(),
            
            // Es buena práctica formatear la fecha a string para evitar fallos al serializar el objeto DateTime
            'fecha' => $solicitud->getFecha() ? $solicitud->getFecha()->format('d-m-Y | H:i') : null,
            
            'aceptada' => $solicitud->isAceptada()
        ];
    }

}