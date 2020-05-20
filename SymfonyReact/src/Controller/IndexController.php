<?php


namespace App\Controller;


use App\Entity\Core\User;

use App\PageData\LiveSensorData;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


/**
 * @Route("/HomeApp")
 */
class IndexController extends AbstractController
{

    /**
     * @Route("/index", name="index")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request) :Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }

    /**
     * @Route("/csrfToken", name="csrf")
     */
    public function viewToken(CsrfTokenManagerInterface $csrfTokenManager, Request $request)
    {

        $token = $csrfTokenManager->getToken('authenticate')->getValue();
       // dd($token);
        return new JsonResponse(['token' => $token]);
    }


    /**
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