<?php


namespace App\Controller;

use App\Entity\Card\Cardview;
use App\Entity\Core\Sensornames;
use App\Entity\Core\Sensortype;
use App\Entity\Core\User;
use App\Entity\Sensors\Temp;
use App\Form\CardViewFormType;
use App\PageData\LiveSensorData;
use App\Services\CardDataService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/HomeApp/index", name="index")
 */
class IndexController extends AbstractController
{

    /**
     * @Route("/", name="indexview")
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request) :Response
    {
        return $this->render('index/index.html.twig', [
        ]);
    }

    /**
     * @Route("/CardData", name="cardData")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     */
    public function returnAllCardData(Request $request, CardDataService $cardDataService)
    {
        $cardData = $cardDataService->returnAllCardSensorData('json', 'index');
        return new JsonResponse($cardData);
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

    /**
     * @Route("/token")
     */
    public function newTokenAction()
    {
        $hey = "hery";
        return new JsonResponse($hey);
    }


}