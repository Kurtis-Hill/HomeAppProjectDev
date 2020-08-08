<?php

namespace App\Controller;

use App\Entity\Core\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @Route("/HomeApp/csrfToken", name="csrf")
     */
    public function getToken(CsrfTokenManagerInterface $csrfTokenManager,  Request $request)
    {

        $token = $csrfTokenManager->getToken('authenticate')->getValue();
        $refreshToken = $csrfTokenManager;


        return new JsonResponse(['token' => $token, 'refreshToken' => $refreshToken]);
    }

    /**
     * @Route("/HomeApp/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('index');
         }

        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/HomeApp/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('app_login');
    }

    /**FOR DEVELOPMENT ONLY
     * @Route("/ssl")
     */
    public function showSSL()
    {

        echo \PDO::MYSQL_ATTR_SSL_KEY;
        echo "<br>";
        echo \PDO::MYSQL_ATTR_SSL_CERT;
        echo "<br>";
        echo \PDO::MYSQL_ATTR_SSL_CA;
        echo "<br>";
        echo \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT;
        echo "<br>";
        $this->getDoctrine()->getRepository(User::class)->showSSL();
    }
}
