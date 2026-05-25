<?php

namespace App\Controller;

use App\Dto\NoticiaDto;
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

#[Route(path:'/api/admin')]
final class AdminController extends AbstractController {

    // ======================= GET ALL USUARIOS =======================
    #[Route(path:'/usuarios', methods:['GET'])]
    #[OA\Get(
        path: '/api/admin/usuarios',
        summary: 'Obtiene todos los usuarios de la aplicacion',
        tags: ['Admin - Usuarios'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve un array con todos los usuarios de la aplicacion'
    )]
    public function getAllUsuarios(EntityManagerInterface $em): JsonResponse {
        $usuarios = $em->getRepository(Usuario::class)->findAll();

        return $this->json(array_map(
            fn(Usuario $usuarios) => $this->toArrayUsuario($usuarios),
            $usuarios
        ));
    }

    // ======================= ELIMINAR USUARIO =======================
    #[Route(path:'/usuarios/eliminar/{usuario_id}', methods:['DELETE'])]
    #[OA\Delete(
        path: '/api/admin/usuarios/eliminar/{usuario_id}',
        summary: 'Elimina de la base de datos a un usuario con un id específico',
        tags: ['Admin - Usuarios'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Elimina de la base de datos a un usuario con un id específico'
    )]
    public function deleteUsuario(EntityManagerInterface $em, int $usuario_id): JsonResponse {
        $usuario = $em->getRepository(Usuario::class)->find($usuario_id);

        if(!$usuario) {
            return $this->json(['message' => 'Usuario no encontrado'], 404);
        }

        $em->remove($usuario);
        $em->flush();

        return $this->json(['message' => 'Usuario eliminado correctamente'], 200);
    }

    // ======================= ACTUALIZAR USUARIO ROLES =======================
    #[Route(path:'/usuarios/actualizar/roles/{usuario_id}', methods:['PATCH'])]
    #[OA\Patch(
        path: '/api/admin/usuarios/actualizar/roles/{usuario_id}',
        summary: 'Actualiza el rol de un usuario',
        tags: ['Admin - Usuarios'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'roles', type: 'string', example: ['ROLE_USER']),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Actualiza el rol de un usuario '
    )]
    public function actualizarUsuarioRoles(
        EntityManagerInterface $em,
        Request $request, 
        int $usuario_id
    ): JsonResponse {

            $usuario = $em->getRepository(Usuario::class)->find($usuario_id);

            if(!$usuario) {
                return $this->json(['message' => 'Usuario no encontrado'], 404);
            }

            $data = json_decode($request->getContent(), true); // SIN true devuelve un objeto | CON true devuelve un array asociativo ( array['ejemplo1'] )

            if( !isset($data['roles']) || !isArray($data['roles']) ) {
                return $this->json(['error' => 'Credenciales invalidas, los roles no pueden estar vacios',], 400);
            }
            else 
            {
                if( $data['roles'] != ['ROLE_USER'] || $data['roles'] != ['ROLE_ADMIN'] ) {
                    return $this->json(['message' => 'Roles invalidos',
                                        'roles_disponibles' => 'ROLE_USER, ROLE_ADMIN'], 400);
                }
            }

            $usuario->setRoles( $data['roles'] );
            $em->flush();

            return new JsonResponse(['message' => 'Roles actualizados'], 200);
    }

    // ======================= CREAR NOTICIA =======================
    #[Route(path:'/noticias/crear', methods:['POST'])]
    #[OA\Post(
        path: '/api/admin/noticias/crear',
        summary: 'Crea una noticia',
        tags: ['Admin - Noticias'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'titulo', type: 'string', example: 'Curso gratis de Symfony'),
                new OA\Property(property: 'categoria', type: 'string', example: 'Backend'),
                new OA\Property(property: 'texto', type: 'text', example: 'Se ha lanzado ya el nuevo curso de symfony con mas de mil videos...')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Crea una noticia'
    )]
    public function crearNoticia(
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em
        ): JsonResponse {

            $data = json_decode($request->getContent(), true);

            // mapear datos al dto
            $noticiaDto = new NoticiaDto();
            $noticiaDto->titulo = $data['titulo'];
            $noticiaDto->categoria = $data['categoria'];
            $noticiaDto->texto = $data['texto'];

            // validar dto
            $errores = $validator->validate($noticiaDto);
            if( count($errores) > 0 ) {
                return $this->json(['errores' => (string) $errores], 400);
            } 

            // verificar si hay una noticia con el mismo titulo
            $titulo_existente = $em
                ->getRepository(Noticia::class)
                ->findOneBy(['titulo' => $noticiaDto->titulo]);

            if($titulo_existente) {
                return $this->json(['error' => 'Ya hay una noticia con ese titulo'], 409);
            }

            $noticia = new Noticia();
            $noticia->setTitulo($noticiaDto->titulo);
            $noticia->setCategoria($noticiaDto->categoria);
            $noticia->setTexto($noticiaDto->texto);

            $em->persist($noticia);
            $em->flush();

            $resultados = [
                'message' => 'Noticia creada correctamente',
                'noticia_creada' => $this->toArrayNoticia($noticia)
            ];

            return $this->json($resultados, 201);
    }

    // ======================= ACTUALIZAR NOTICIA (PATCH) =======================
    #[Route(path:'/noticias/actualizar/{noticia_id}', methods:['PATCH'])]
    #[OA\Patch(
        path: '/api/admin/noticias/actualizar/{noticia_id}',
        summary: 'Actualiza parcialmente una noticia',
        tags: ['Admin - Noticias'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'titulo', type: 'string', example: 'Curso gratis de Symfony (Opcional)'),
                new OA\Property(property: 'categoria', type: 'string', example: 'Backend (Opcional)'),
                new OA\Property(property: 'texto', type: 'text', example: 'Se ha lanzado ya el nuevo curso... (Opcional)')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Noticia actualizada correctamente'
    )]
    public function actualizarNoticia(EntityManagerInterface $em, Request $request, ValidatorInterface $validator, int $noticia_id): JsonResponse {
        $noticia = $em->getRepository(Noticia::class)->find($noticia_id);

        if(!$noticia) {
            return $this->json(['message' => 'Noticia no encontrada'], 404);
        }

        $data = json_decode($request->getContent(), true) ?? []; 

        $noticiaDto = new NoticiaDto();
        $noticiaDto->titulo = $data['titulo'] ?? $noticia->getTitulo();
        $noticiaDto->categoria = $data['categoria'] ?? $noticia->getCategoria();
        $noticiaDto->texto = $data['texto'] ?? $noticia->getTexto();

        $errores = $validator->validate($noticiaDto);
        if( count($errores) > 0 ) {
            return $this->json(['errores' => (string) $errores], 400);
        } 

        // verifica si hay una noticia con el mismo titulo si se esta enviando un titulo nuevo
        if (isset($data['titulo']) && $data['titulo'] !== $noticia->getTitulo()) {
            $titulo_existente = $em
                ->getRepository(Noticia::class)
                ->findOneBy(['titulo' => $noticiaDto->titulo]);

            if($titulo_existente) {
                return $this->json(['error' => 'Ya hay otra noticia con ese titulo'], 409);
            }
        }

        $noticia->setTitulo($noticiaDto->titulo);
        $noticia->setCategoria($noticiaDto->categoria);
        $noticia->setTexto($noticiaDto->texto);

        $resultados = [
            'message' => 'Noticia actualizada parcialmente con éxito',
            'noticia_actualizada' => $this->toArrayNoticia($noticia)
        ];

        $em->flush();

        return $this->json($resultados, 200);
    }

    // ======================= ELIMINAR NOTICIA =======================
    #[Route(path:'/noticias/eliminar/{noticia_id}', methods:['DELETE'])]
    #[OA\Delete(
        path: '/api/admin/noticias/eliminar/{noticia_id}',
        summary: 'Elimina de la base de datos una noticia con un id específico',
        tags: ['Admin - Noticias'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Elimina de la base de datos una noticia con un id específico'
    )]
    public function deleteNoticia(EntityManagerInterface $em, int $noticia_id): JsonResponse {
        $noticia = $em->getRepository(Noticia::class)->find($noticia_id);

        if(!$noticia) {
            return $this->json(['message' => 'Noticia no encontrada'], 404);
        }

        $em->remove($noticia);
        $em->flush();

        return $this->json(['message' => 'Noticia eliminada correctamente'], 200);
    }

    // ======================= HELPERS =======================
    private function toArrayUsuario(Usuario $usuario): array {
        return [
            'id' => $usuario->getId(),
            'email' => $usuario->getEmail(),
            'roles' => $usuario->getRoles()
        ];
    }

    private function toArrayNoticia(Noticia $noticia): array {
        return [
            'id' => $noticia->getId(),
            'titulo' => $noticia->getTitulo(),
            'categoria' => $noticia->getCategoria(),
            'texto' => $noticia->getTexto()
        ];
    }

}