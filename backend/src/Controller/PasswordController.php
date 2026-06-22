<?php

namespace App\Controller;

use App\Dto\ForgotPasswordDto;
use App\Dto\ResetPasswordDto;
use App\Entity\Usuario;
use App\Service\MailService;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Attributes as OA;


#[Route(path:'/api/password')]
final class PasswordController extends AbstractController {

    // ======================= ENVIA UN CORREO AL USUARIO QUE SE LA HA OLVIDADO LA CONTRASEÑA =======================
    #[Route(path:'/forgot', methods:['POST'])]
    #[OA\Post(
        path: '/api/password/forgot',
        summary: 'Envia un correo al usuario que se le ha olvidado la contraseña',
        tags: ['Password'],
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'email', type: 'string', example: 'usuario@gmail.com'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Envia un correo al usuario que se le ha olvidado la contraseña | Si sale error -> eliminar el token de la base de datos para que funcione'
    )]
    public function enviarEmailPasswordForgot(
        EntityManagerInterface $em,
        Request $request,
        ValidatorInterface $validator,
        MailService $mail_service,
        ResetPasswordHelperInterface $resetPasswordHelper, // bundle de reset password
    ): JsonResponse {
        
        $data = json_decode($request->getContent(), true) ?? [];
        
        $forgotPasswordDto = new ForgotPasswordDto();
        $forgotPasswordDto->email = $data['email'];

        $errores = $validator->validate($forgotPasswordDto);
            if( count($errores) > 0 ) {
                return $this->json(['errores' => (string) $errores], 400);
            } 

        $usuario = $em->getRepository(Usuario::class)->findOneBy(['email' => $forgotPasswordDto->email]);

        if ($usuario) {
            try {
                // el bundle crea el token, lo encripta, le pone caducidad y lo guarda en la tabla ResetPasswordRequest automáticamente.
                $resetToken = $resetPasswordHelper->generateResetToken($usuario);
                
                // se saca el token en texto plano (solo dura vivo esta fracción de segundo)
                $plainToken = $resetToken->getToken();

                // ruta al formulario para resetear la contraseña del frontend
                $urlFrontend = 'https://laligamanager-daw.vercel.app/restablecer-password?token=' . $plainToken;

                $mail_service->send(
                $usuario->getEmail(), // $to                          
                'Restablecimiento de contraseña', // subject    
                'emails/reset_password.html.twig', // ruta a la plantilla que se mandara
                [
                    // variables para twig
                    'enlace_recuperacion' => $urlFrontend,
                    'nombre_usuario' => $usuario->getEmail(),
                ]              
                );

            } catch (ResetPasswordExceptionInterface $e) {
                return $this->json(['error' => 'Este usuario ya ha pedido un restablecimiento de la contraseña en la ultima hora']);
            }
        }

        // mensaje por defecto por seguridad para evitar ataques de enumeracion
        return $this->json(['message' => 'Si el email existe recibirás instrucciones']);
    }

    // ======================= RESET PASSWORD =======================
    #[Route(path:'/reset', methods:['POST'])]
    #[OA\Post(
        path: '/api/password/reset',
        summary: 'Cambia la contraseña antigua por la nueva usando el token que te mandan por correo al pedir el restablecimiento de contraseña',
        tags: ['Password'],
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(
            type: 'object',
            properties: [
                new OA\Property(property: 'token', type: 'string', example: 'abcdefg123456'),
                new OA\Property(property: 'password', type: 'string', example: 'password'),
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: 'Cambia la contraseña antigua por la nueva usando el token que te mandan por correo al pedir el restablecimiento de contraseña'
    )]
    public function resetPassword(
        EntityManagerInterface $em,
        Request $request,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $password_hasher,
        ResetPasswordHelperInterface $resetPasswordHelper // bundle de reset password
    ): JsonResponse {
        
        $data = json_decode($request->getContent(), true) ?? [];
        
        $resetPasswordDto = new ResetPasswordDto();
        $resetPasswordDto->token = $data['token'];
        $resetPasswordDto->password = $data['password'];

        $errores = $validator->validate($resetPasswordDto);
            if( count($errores) > 0 ) {
                return $this->json(['errores' => (string) $errores], 400);
            } 

        try {
            // funcion del bundle
            // busca el token en la base de datos, comprueba que no esta caducado y si todo es correcto devuelve al usuario dueño de ese token
            $usuario = $resetPasswordHelper->validateTokenAndFetchUser($resetPasswordDto->token);
        }
        catch (ResetPasswordExceptionInterface $e) {
            return $this->json(['error' => 'El enlace es invalido o ha caducado']);
        }
            
        $hashed_password = $password_hasher->hashPassword($usuario, $resetPasswordDto->password);
        $usuario->setPassword($hashed_password);
        $em->flush();

        // elimina el token de la base de datos para que nadie pueda volver a usarlo
        $resetPasswordHelper->removeResetRequest($resetPasswordDto->token);

        return $this->json(['message' => 'Contraseña actualizada correctamente']);
    }

}
