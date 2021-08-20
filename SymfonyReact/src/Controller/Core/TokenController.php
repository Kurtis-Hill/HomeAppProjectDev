<?php


namespace App\Controller\Core;


use App\Entity\Core\User;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;

/**
 * @Route("/HomeApp/token")
*/
class TokenController extends AbstractController
{

    /**
     * @Route("/", name="token")
     * DEV stuff
     */
    public function newTokenAction(Request $request, EncoderFactoryInterface $encoderFactory, JWTEncoderInterface $JWTEncoder)
    {
        if (!str_starts_with($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        }

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['email' => $this->getUser()->getUsername()]);

        if (!$user) {
            throw new BadCredentialsException();
        }

        $security = $encoderFactory->getEncoder($user);

        $userName = $user->getUsername();


        $token = $JWTEncoder->encode(['username' => $user->getUsername(), 'exp' => time() + 3600]);

        return new JsonResponse(['token' => $token]);
    }
}
