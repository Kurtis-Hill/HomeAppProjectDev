<?php

namespace App\Controller;

use App\Entity\Core\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{

    /**
     * @Route("/HomeApp/api/csrfToken", name="csrf")
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return JsonResponse
     *
     * Needs refactor, attach to axios headers
     */
    public function getToken(CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $token = $csrfTokenManager->getToken('authenticate')->getValue();

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @Route("/HomeApp/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return RedirectResponse|Response
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
     * @Route("/HomeApp/ssl", name="ssl")
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

    /**
     * FOR DEVELOPMENT ONLY
     * @Route("/HomeApp/xdebug", name="xdebug")
     */
    public function showxDebug()
    {
        return new Response(xdebug_info());
    }

    /**
     * FOR DEVELOPMENT ONLY
     * @Route("/HomeApp/driver", name="driver")
     */
    public function driverCheck()
    {
        $driver = \PDO::getAvailableDrivers();

        return new Response(print_r($driver));
    }

}
