<?php

namespace App\Controller\Core;

use App\Entity\Core\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public const API_USER_LOGIN = '/HomeApp/api/login_check';

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @return JsonResponse
     *
     * DEV USE ONLY
     */
    #[Route('/HomeApp/api/csrfToken', name: 'csrf')]
    public function getToken(CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $token = $csrfTokenManager->getToken('authenticate')->getValue();

        return new JsonResponse(['token' => $token]);
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @param Request $request
     * @return RedirectResponse|Response
     */
    #[Route('/HomeApp/WebApp/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request)
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('index', ['route' => 'index']);
         }

        return $this->render('index/index.html.twig');
    }

    /**
     * @Route("/HomeApp/WebApp/logout", name="app_logout")
     */
    public function logout()
    {

    }

    /**
     * FOR DEVELOPMENT ONLY
     */
    #[Route('/HomeApp/ssl', name: 'ssl')]
    public function showSSLConfig()
    {
        $ssl = $this->getDoctrine()->getRepository(User::class)->showSSL();

        foreach ($ssl as $value) {
            foreach ($value as $key => $sslConfig) {

                echo $key. $sslConfig. '<br/>';
            }
        }

        echo \PDO::MYSQL_ATTR_SSL_KEY;
        echo '<br>';
        echo \PDO::MYSQL_ATTR_SSL_CERT;
        echo '<br>';
        echo \PDO::MYSQL_ATTR_SSL_CA;
        echo '<br>';
        echo \PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT;
        echo '<br>';
        echo "<br>";
        die;
    }

    /**
     * FOR DEVELOPMENT ONLY
     */
    #[Route('/HomeApp/xdebug', name: 'xdebug')]
    public function showxDebug()
    {
        return new Response(xdebug_info());
    }

    /**
     * FOR DEVELOPMENT ONLY
     */
    #[Route('/HomeApp/driver', name: 'driver')]
    public function driverCheck()
    {
        $driver = \PDO::getAvailableDrivers();

        return new Response(print_r($driver));
    }

    /**
     * FOR DEVELOPMENT ONLY
     */
    #[Route('/HomeApp/JIT', name: 'JIT')]
    public function checkJITDriver()
    {
        dd(\opcache_get_status()['jit']);
    }

}
