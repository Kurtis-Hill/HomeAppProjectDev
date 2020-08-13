<?php


namespace App\Controller\Core;


use App\Entity\Core\User;
use Couchbase\PasswordAuthenticator;
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
     * For use in future if I create an app this would allow me to access a token
     * @TODO take request and brake down content and insert credentials in the query
     * @Route("/", name="token")
     */
    public function newTokenAction(Request $request, EncoderFactoryInterface $encoderFactory, JWTEncoderInterface $JWTEncoder)
    {
        if (0 !== strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
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
