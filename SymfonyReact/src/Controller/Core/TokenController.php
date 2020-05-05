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
     * @Route("/", name="token")
 * @TODO Delete just messing around
     */
    public function newTokenAction(Request $request, EncoderFactoryInterface $encoderFactory, JWTEncoderInterface $JWTEncoder)
    {
        var_dump($request->getContent());
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
//            $request->request->replace(is_array($data) ? $data : array());
        }


     //   var_dump($data);
       // dd($this->getUser()->getUsername());
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['email' => $this->getUser()->getUsername()]);
      //  dd($user);
        dd($user->getUsername());

        if (!$user) {
            throw new BadCredentialsException();
        }
        $security = $encoderFactory->getEncoder($user);
        $isValid = $security->isPasswordValid($user->getPassword(), 'Dreadnaught1', null);


//        if (!$isValid) {
//            throw new BadCredentialsException();
//        }

        $token = $JWTEncoder->encode(['username' => $user->getUsername(), 'exp' => time() + 3600]);

//        dd($isValid);
        return new JsonResponse(['token' => $token]);
    }
}
