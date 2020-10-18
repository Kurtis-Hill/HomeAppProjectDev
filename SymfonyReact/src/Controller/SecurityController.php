<?php

namespace App\Controller;

use App\Entity\Core\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{

    /**
     * @Route("/HomeApp/csrfToken", name="csrf")
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return JsonResponse
     */
    public function getToken(CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $token = $csrfTokenManager->getToken('authenticate')->getValue();
        $refreshToken = $csrfTokenManager;

        return new JsonResponse(['token' => $token, 'refreshToken' => $refreshToken]);
    }

    /**
     * @Route("/HomeApp/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('index', ['route' => 'index']);
         }

        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/HomeApp/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->render('index/index.html.twig');
    }

    /**
     * FOR DEVELOPMENT ONLY
     * @Route("/HomeApp/api/ssl", name="ssl")
     */
    public function showSSLConfig()
    {
        $ssl = $this->getDoctrine()->getRepository(User::class)->showSSL();

        foreach ($ssl as $key => $value) {
            echo 'ssl key ='.$key. 'key location'. $value. '<br/>';
        }

        echo \PDO::MYSQL_ATTR_SSL_KEY;
        echo "<br>";
        echo \PDO::MYSQL_ATTR_SSL_CERT;
        echo "<br>";
        echo \PDO::MYSQL_ATTR_SSL_CA;
        echo "<br>";
        echo \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT;
        echo "<br>";


    }
}
