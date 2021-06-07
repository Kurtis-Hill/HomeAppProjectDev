<?php


namespace App\Controller\UserInterface;

use App\Entity\Card\CardView;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Form\CardViewForms\CardViewForm;
use App\Form\FormMessages;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\Services\CardUserDataService;
use App\Services\ESPDeviceSensor\SensorData\SensorUserDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\Voters\CardViewVoter;
use App\Voters\SensorVoter;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


/**
 * Class CardDataController.
 *
 * @Route("/HomeApp/api/card-data")
 *
 */
class CardDataController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/cards", name="card-data", methods={"GET"})
     * @param Request $request
     * @param CardUserDataService $cardDataService
     * @return Response|JsonResponse
     */
    public function returnCardDataDTOs(Request $request, CardUserDataService $cardDataService): Response|JsonResponse
    {
        $route = $request->get('view');
        $deviceId = $request->get('device-id');

        $em = $this->getDoctrine()->getManager();

        if (isset($deviceId) && is_numeric($deviceId)) {
            $device = $em->getRepository(Devices::class)->findOneBy(['deviceNameID' => $deviceId]);

            if ($device instanceof Devices) {
                $this->denyAccessUnlessGranted(SensorVoter::VIEW_DEVICE_CARD_DATA, $device);
            } else {
                return $this->sendBadRequestJsonResponse(['errors' => ['No device found']]);
            }
        }

        $cardDataDTOs = $cardDataService->prepareAllCardDTOs($route, $deviceId);

        if (!empty($cardDataService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse(['errors' => $cardDataService->getServerErrors()]);
        }
        if (!empty($cardDataService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse();
        }

        if (empty($cardDataDTOs)) {
            return $this->sendSuccessfulResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        $serializedCards = $serializer->serialize($cardDataDTOs, 'json');

        if (!empty($cardDataService->getCardErrors())) {
            return $this->sendPartialContentResponse($serializedCards);
        }

        return $this->sendSuccessfulResponse($serializedCards);
    }


    /**
     * @Route("/card-state-view-form", name="cardViewForm", methods={"GET"})
     *
     * @param Request $request
     * @param CardUserDataService $cardDataService
     * @return Response|JsonResponse
     */
    public function showCardViewForm(Request $request, CardUserDataService $cardDataService): Response|JsonResponse
    {
        $cardViewID = $request->query->get('cardViewID');

        if (empty($cardViewID) || !is_numeric($cardViewID)) {
            return $this->sendBadRequestJsonResponse(['errors' => ['malformed card view id request']]);
        }

        $em = $this->getDoctrine()->getManager();

        $cardViewObject = $em->getRepository(CardView::class)->findOneBy(['cardViewID' => $cardViewID]);

        if ($cardViewObject instanceof CardView) {
            try {
                $this->denyAccessUnlessGranted(CardViewVoter::CAN_VIEW_CARD_VIEW_FORM, $cardViewObject);
            } catch (\Exception) {
                return $this->sendForbiddenAccessJsonResponse(['errors' => [FormMessages::ACCES_DENIED]]);
            }
        } else {
            return $this->sendBadRequestJsonResponse(['errors' => ['Card view id not recognised']]);
        }

        $cardFormDTO = $cardDataService->getCardViewFormDTO($cardViewID);

        if ($cardFormDTO === null || !empty($cardDataService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $this->sendSuccessfulResponse($serializer->serialize($cardFormDTO, 'json'));
    }


    /**
     * @Route("/update-card-view", name="updateCardView", methods={"POST"})
     * @param Request $request
     * @param SensorUserDataService $sensorDataService
     * @param CardUserDataService $cardDataService
     * @return Response|JsonResponse
     */
    public function updateCardView(Request $request, SensorUserDataService $sensorDataService, CardUserDataService $cardDataService): Response|JsonResponse
    {
        $cardViewID = $request->get('card-view-id');

        if (empty($cardViewID) || !is_numeric($cardViewID)) {
            return $this->sendBadRequestJsonResponse(['errors' => 'malformed card view id not recognised']);
        }

        $em = $this->getDoctrine()->getManager();

        $cardViewObject = $em->getRepository(CardView::class)->findOneBy(['cardViewID' => $cardViewID]);

        if ($cardViewObject instanceof CardView) {
            try {
                $this->denyAccessUnlessGranted(CardViewVoter::CAN_EDIT_CARD_VIEW_FORM, $cardViewObject);
            } catch (\Exception) {
                return $this->sendForbiddenAccessJsonResponse(['errors' => [FormMessages::ACCES_DENIED]]);
            }
        } else {
            return $this->sendBadRequestJsonResponse(['errors' => ['card not found by the id given']]);
        }

        $cardViewForm = $this->createForm(CardViewForm::class, $cardViewObject);

        $cardViewData = [
            'cardColourID' => $request->get('card-colour'),
            'cardIconID' => $request->get('card-icon'),
            'cardStateID' => $request->get('card-view-state'),
        ];

//        $cardSensorReadingObject = $cardDataService->editSelectedCardData($cardViewID);
//        $cardViewObject = array_shift($cardSensorReadingObject);

//        dd($cardSensorReadingObject, $cardViewObject);
//        dd($request->request->all(), 'cont');
        $cardDataService->processForm($cardViewForm, $em, $cardViewData);

        if ($cardDataService->getUserInputErrors()) {
            return $this->sendBadRequestJsonResponse($cardDataService->getUserInputErrors());
        }

        $sensorObject = $cardViewObject->getSensorNameID();
//dd($sensorTypeObject);
        $sensorTypeObject = $em->getRepository(Sensors::class)->getSensorCardFormDataBySensor($sensorObject, SensorType::SENSOR_TYPE_DATA);

        if (!$sensorTypeObject instanceof StandardSensorTypeInterface) {
            return $this->sendBadRequestJsonResponse(['errors' => ['Sensor type object not found your app may need updating']]);
        }

//        $processedSensorRequestData = $sensorDataService->processSensorUpdateRequestObject($request, $sensorTypeObject);
        $sensorDataService->handleSensorReadingBoundary($sensorObject, $request->request->all());

        if (!empty($sensorDataService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($sensorDataService->getUserInputErrors());
        }

        if (!empty($sensorDataService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse();
        }

        $em->flush();

        return $this->sendSuccessfulUpdateJsonResponse();
    }

}
