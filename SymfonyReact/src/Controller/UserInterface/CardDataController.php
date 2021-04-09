<?php


namespace App\Controller\CardData;

use App\Form\CardViewForms\CardViewForm;
use App\Services\CardUserDataService;
use App\Services\ESPDeviceSensor\SensorData\SensorUserDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
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

        $cardData = $cardDataService->prepareAllCardDTOs($route, $deviceId);

        if (!empty($cardDataService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse(['errors' => 'Something went wrong']);
        }

        if (empty($cardData)) {
            return $this->sendSuccessfulResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        $serializedCards = $serializer->serialize($cardData, 'json');

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

        if (empty($cardViewID)) {
            return $this->sendBadRequestJsonResponse();
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

        $cardColourID = $request->get('card-colour');
        $cardIconID = $request->get('card-icon');
        $cardStateID = $request->get('card-view-state');

        if (empty($cardColourID) || empty($cardIconID) || empty($cardStateID) || empty($cardViewID)) {
            return $this->sendBadRequestJsonResponse(['errors' => 'empty form data']);
        }

        $cardViewData = [
            'cardColourID' => $cardColourID,
            'cardIconID' => $cardIconID,
            'cardStateID' => $cardStateID,
        ];

        $cardSensorReadingObject = $cardDataService->editSelectedCardData($cardViewID);
        $cardViewObject = array_shift($cardSensorReadingObject);

        $cardViewForm = $this->createForm(CardViewForm::class, $cardViewObject);

        $cardViewForm->submit($cardViewData);

        if ($cardViewForm->isSubmitted() && $cardViewForm->isValid()) {
            $validFormData = $cardViewForm->getData();
            try {
                $this->getDoctrine()->getManager()->persist($validFormData);
            } catch (ORMException | \Exception $e) {
                return $this->sendBadRequestJsonResponse();
            }
        }

        $sensorTypeObject = $cardViewObject->getSensorNameID();

        $sensorDataService->handleSensorReadingBoundary($request, $sensorTypeObject, $cardSensorReadingObject);

        if (!empty($sensorDataService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($sensorDataService->getUserInputErrors());
        }

        if (!empty($sensorDataService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse();
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->sendSuccessfulUpdateJsonResponse();
    }


}
