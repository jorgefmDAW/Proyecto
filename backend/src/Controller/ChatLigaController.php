<?php

namespace App\Controller;

use App\Entity\ChatLiga;
use App\Entity\Liga;
use App\Entity\Usuario;
use App\Entity\UsuarioFantasy;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path:'/api/chat-liga')]
final class ChatLigaController extends AbstractController {

    // ======================= GET ALL MENSAJES OF CHAT LIGA =======================
    #[Route(path:'/mensajes/{id}', methods:['GET'])]
    #[OA\Get(
        path: '/api/chat-liga/mensajes/{id}',
        summary: 'Obtiene todos los mensajes del chat de una liga específica',
        tags: ['Chat Liga']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array con todos los mensajes del chat de la liga'
    )]
    public function getAllMensajesChatLiga(int $id, EntityManagerInterface $em, #[CurrentUser] ?Usuario $usuario): JsonResponse {
        if (!$usuario) {
            return $this->json(['message' => 'No estás autenticado'], 401);
        }

        // VALIDACIÓN: Comprobar si el usuario pertenece a la liga antes de leer
        $perteneceALiga = $em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuario,
            'liga' => $id
        ]);

        if (!$perteneceALiga) {
            return $this->json(['error' => 'No tienes permiso para leer el chat de una liga a la que no perteneces'], 403);
        }

        $mensajes = $em->getRepository(ChatLiga::class)->findBy(
            ['liga' => $id], 
            ['fecha' => 'ASC']
        );

        if (!$mensajes) {
            return $this->json(['message' => 'No hay mensajes en esta liga'], 404); // Cambiado a 404 o 200 con array vacío según prefieras
        }

        return $this->json(array_map(
            fn(ChatLiga $mensaje) => $this->toArray($mensaje),
            $mensajes
        ));
    }

    // ======================= ENVIAR MENSAJE =======================
    #[Route(path:'/mensajes/enviar/{id}', methods:['POST'])]
    #[OA\Post(
        path: '/api/chat-liga/mensajes/enviar/{id}',
        summary: 'Inserta un mensaje en el chat de una liga específica',
        tags: ['Chat Liga'],
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'mensaje', type: 'string', example: '¡Hola a todos en la liga!'),
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: 'Inserta un mensaje en la base de datos'
    )]
    public function enviarMensajeChatLiga(int $id, Request $request, EntityManagerInterface $em, #[CurrentUser] ?Usuario $usuario): JsonResponse {
        $data = json_decode($request->getContent(), true);

        if(!$data || empty($data['mensaje'])) {
            return $this->json(['error' => 'No se enviaron datos o el mensaje está vacío'], 400);
        }

        if(!$usuario) {
            return $this->json(['message' => 'Tienes que estar loggeado para enviar un mensaje'], 401);
        }

        $liga = $em->getRepository(Liga::class)->find($id);
        
        if(!$liga) {
            return $this->json(['error' => 'La liga especificada no existe'], 404);
        }

        // VALIDACIÓN: Comprobar si el usuario está unido a la liga a través de UsuarioFantasy
        $perteneceALiga = $em->getRepository(UsuarioFantasy::class)->findOneBy([
            'usuario' => $usuario,
            'liga' => $liga // Doctrine mapea la entidad automáticamente
        ]);

        if (!$perteneceALiga) {
            return $this->json(['error' => 'No puedes enviar mensajes a una liga a la que no perteneces bro'], 403);
        }

        $chatLiga = new ChatLiga();
        $chatLiga->setMensaje($data['mensaje']);
        $chatLiga->setUsuario($usuario);
        $chatLiga->setLiga($liga);
        $chatLiga->setFecha(new \DateTime('now', new \DateTimeZone('Europe/Madrid')));

        $em->persist($chatLiga);
        $em->flush();

        $resultados = [
            'message' => 'Mensaje creado correctamente',
            'mensaje_creado' => $this->toArray($chatLiga)
        ];

        return $this->json($resultados, 201);
    }

    // ======================= HELPERS =======================
    private function toArray(ChatLiga $chatLiga): array {
        return [
            'id' => $chatLiga->getId(),
            'mensaje' => $chatLiga->getMensaje(),
            'fecha' => $chatLiga->getFecha() ? $chatLiga->getFecha()->format('d-m-Y | H:i') : null,
            // 'liga' => [
            //     'liga_id' => $chatLiga->getLiga()->getId(),
            //     'nombre' => $chatLiga->getLiga()->getNombre() 
            // ],
            'usuario' => [
                'usuario_id' => $chatLiga->getUsuario()->getId(),
                'username' => $chatLiga->getUsuario()->getUsername()
            ]
        ];
    }
}