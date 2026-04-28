<?php

namespace App\Controller;

use App\Dto\ForoGlobalDto;
use App\Entity\ForoGlobal;
use App\Entity\Usuario;
use App\Entity\Noticia;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function PHPUnit\Framework\isArray;

#[Route(path:'/api/foro-global')]
final class ForoGlobalController extends AbstractController {

    // ======================= GET ALL MENSAJES =======================
    #[Route(path:'/mensajes', methods:['GET'])]
    #[OA\Get(
        path: '/api/foro-global/mensajes',
        summary: 'Obtiene todos los mensajes del foro global',
        tags: ['Foro Global']
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array con todos los mensajes del foro global'
    )]
    public function getAllMensajes(EntityManagerInterface $em): JsonResponse {
        $mensajes = $em->getRepository(ForoGlobal::class)->findAll();

        return $this->json(array_map(
            fn(ForoGlobal $mensajes) => $this->toArray($mensajes),
            $mensajes
        ));
    }

    // ======================= ENVIAR MENSAJE =======================
    #[Route(path:'/mensajes/enviar', methods:['POST'])]
    #[OA\Post(
        path: '/api/foro-global/mensajes/enviar',
        summary: 'Inserta un mensaje en la base de datos',
        tags: ['Foro Global'],
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'mensaje', type: 'text', example: 'Hola soy un nuevo usuario'),
                new OA\Property(property: 'usuario_id', type: 'int', example: '1')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Inserta un mensaje en la base de datos'
    )]
    public function enviarMensaje(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em
        ): JsonResponse {

            $data = json_decode($request->getContent(), true);

            // mapear datos al dto
            $foroGlobalDto = new ForoGlobalDto();
            $foroGlobalDto->mensaje = $data['mensaje'];
            $foroGlobalDto->usuario = $data['usuario_id'];

            // validar dto
            $errores = $validator->validate($foroGlobalDto);
            if( count($errores) > 0 ) {
                return $this->json(['errores' => (string) $errores], 400);
            }

            // obtiene el usuario mediante el id que se le ha pasado por el dto
            $usuario = $em->getRepository(Usuario::class)->find($foroGlobalDto->usuario);

            $foroGlobal = new ForoGlobal();
            $foroGlobal->setMensaje($foroGlobalDto->mensaje);
            $foroGlobal->setUsuario($usuario);

            $em->persist($foroGlobal);
            $em->flush();

            $resultados = [
                'message' => 'Mensaje creado correctamente',
                'mensaje_creado' => $this->toArray($foroGlobal)
            ];

            return $this->json($resultados, 201);
    }

    // ======================= HELPERS =======================
    private function toArray(ForoGlobal $foroGlobal): array {
        return [
            'id' => $foroGlobal->getId(),
            'mensaje' => $foroGlobal->getMensaje(),
            'usuario_id' => $foroGlobal->getUsuario()->getId()
        ];
    }

}