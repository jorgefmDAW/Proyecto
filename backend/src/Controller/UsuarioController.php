<?php

namespace App\Controller;

use App\Dto\UsuarioDto;
use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route(path:'/api/usuario')]
final class UsuarioController extends AbstractController {

    // ======================= REGISTRAR USUARIO =======================
    #[Route(path:'/registro', methods:['POST'])]
    #[OA\Post(
        path: '/api/usuario/registro',
        summary: 'Registrar a un usuario con un email y password',
        tags: ['Usuarios'],
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'usuario@gmail.com'),
                new OA\Property(property: 'username', type: 'string', example: 'usuario'),
                new OA\Property(property: 'password', type: 'string', example: 'password')
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Registra a un usuario con un email, un username y password'
    )]
    public function registrarUsuario(
        Request $request,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $password_hasher,
        EntityManagerInterface $em
        ): JsonResponse {

            $data = json_decode($request->getContent(), true);

            // mapear datos al dto
            $usuarioDto = new UsuarioDto();
            $usuarioDto->email = $data['email'];
            $usuarioDto->username = $data['username'];
            $usuarioDto->password = $data['password'];

            // validar dto
            $errores = $validator->validate($usuarioDto);
            if( count($errores) > 0 ) {
                return $this->json(['errores' => (string) $errores], 400);
            } 

            // verificar si el email existe usadno el dto
            $email_existente = $em
                ->getRepository(Usuario::class)
                ->findOneBy(['email' => $usuarioDto->email]);

            if($email_existente) {
                return $this->json(['error' => 'El email está en uso'], 409);
            }

            $rol_usuario = ['ROLE_USER']; // por defecto todos los usuarios que se creen tendrán rol user

            $usuario = new Usuario();
            $usuario->setEmail($usuarioDto->email);
            $usuario->setUsername($usuarioDto->username);
            $usuario->setPassword( $password_hasher->hashPassword($usuario, $usuarioDto->password) );
            $usuario->setRoles($rol_usuario);

            $em->persist($usuario);
            $em->flush();

            $resultados = [
                'message' => 'Usuario creado correctamente',
                'usuario_creado' => $this->toArray($usuario)
            ];

            return $this->json($resultados, 201);
    }

    #[Route('/logout', methods: ['POST'])]
    #[OA\Post(
        path: '/api/usuario/logout',
        summary: 'Cierra la sesión del usuario actual',
        tags: ['Usuarios'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'refresh_token', type: 'string', example: 'eyJhbGciOiJIUzI1...')
            ]
        )
    )]
    #[OA\Response(response: 200, description: 'Sesión cerrada con éxito')]
    #[OA\Response(response: 401, description: 'No autorizado')]
    public function logout(
        Request $request, 
        RefreshTokenManagerInterface $refreshTokenManager, 
        #[CurrentUser] ?Usuario $usuario
    ): JsonResponse {

        // verifica que el usuario este loggeado
        if (!$usuario) {
            return new JsonResponse(['error' => 'No autorizado'], 401);
        }

        $data = json_decode($request->getContent(), true);
        $refreshTokenString = $data['refresh_token'] ?? null;

        if ($refreshTokenString) {
            $refreshToken = $refreshTokenManager->get($refreshTokenString);

            // verifica que el token exista y que pertenezca a el usuario que esta intentando borrarlo
            if ($refreshToken && $refreshToken->getUsername() === $usuario->getUsername()) {
                $refreshTokenManager->delete($refreshToken);
            }
        }

        return new JsonResponse(['message' => 'Sesión cerrada con éxito'], 200);
    }

    // ======================= MOSTRAR PERFIL DE USUARIO =======================
    #[Route(path:'/perfil', methods:['GET'])]
    #[OA\Get(
        path: '/api/usuario/perfil',
        summary: 'Obtiene el perfil de tu usuario basado en tu token',
        tags: ['Usuarios'],
        security: [['bearerAuth' => []]]
    )]
    #[OA\Response(
        response: 200,
        description: 'Devuelve el perfil del usuario autenticado'
    )]
    public function getPerfilUsuario(): JsonResponse {
        // metodo heredado de AbstractController
        // recibe el token que esta dentro del header de la peticion (GET en este caso) que hace el usuario al ejecutar el endpoint
        // decodifica el token y lee el email del usuario y entonces saca de la base de datos toda la informacion del usuario a partir del email
        // el metodo devuelve el objeto resultante con la informacion de ese usuario (email y roles, password NO)
        $usuario = $this->getUser(); 

        if(!$usuario) {
            return $this->json(['message' => 'Usuario no encontrado'], 404);
        }

        return $this->json($this->toArray($usuario));
    }

    // ======================= HELPERS =======================
    private function toArray(Usuario $usuario): array {
        return [
            'id' => $usuario->getId(),
            'email' => $usuario->getEmail(),
            'username' => $usuario->getUsername(),
            'roles' => $usuario->getRoles()
        ];
    }

    
}