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
        $cardData = $cardDataService->returnAllCardSensorData('json');
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

//    /**
//     * @Route("/CardViewForm&id={cardviewid}", name="cardViewForm")
//     */
//    public function cardViewForm(Request $request, $cardviewid)
//    {
//       // $formData = json_decode($request->getContent());
//
//        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]);
//
//        $sensorName = $cardSensorData[0]->getSensornameid()->getSensorname();
//
//        $sensorNameRepository = $this->getDoctrine()->getRepository(Sensornames::class)->findOneBy(['sensorname' => $sensorName]);
//
//        $sensorType = $sensorNameRepository->getSensortypeid()->getSensortype();
//
//        $cardView = new Cardview();
//
//        if ($sensorType == "Temp&Humid") {
//            $form = $this->createForm(CardViewFormType::class, $cardView, [
//                'cardIcon' => $cardSensorData[0]->getCardiconid(),
//                'cardColour' => $cardSensorData[0]->getCardcolourid(),
//                'cardSensorStateOne' => $cardSensorData[1]->getcardstateid()->getstate(),
//                'cardSensorStateTwo' => $cardSensorData[2]->getcardstateid()->getstate(),
//                'sensorType' => $sensorType,
//            ]);
//        }
//        else {
//            $form = $this->createForm(CardViewFormType::class, $cardView, [
//                'cardIcon' => $cardSensorData[0]->getCardiconid(),
//                'cardColour' => $cardSensorData[0]->getCardcolourid(),
//                'cardSensorStateOne' => $cardSensorData[1]->getcardstateid()->getstate(),
//                'sensorType' => $sensorType
//            ]);
//        }
//
////        $form->submit((array)$formData);
////        dd($form);
//        //$form->handleRequest($request);
//
//        $errors = [];
//
//        if ($form->isSubmitted() && !$form->isValid()) {
//            //$submittedData = $form->getData();
//            foreach ($form->getErrors(true, true) as $error) {
//                $propertyName = $error->getOrigin()->getName();
//                $errors[$propertyName] = $error->getMessage();
//            }
//            return new JsonResponse();
//
//        }
//
//        return new JsonResponse($form);
//
//    }
}