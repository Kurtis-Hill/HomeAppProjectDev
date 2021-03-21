<?php


namespace App\Controller\CardData;

use App\Form\CardViewForms\CardViewForm;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use App\Services\CardDataService;
use App\Services\SensorDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Proxies\__CG__\App\Entity\Sensors\SensorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
     * @Route("/cards", name="indexCardData", methods={"GET"})
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return Response|JsonResponse
     */
    public function returnCardDataDTOs(Request $request, CardDataService $cardDataService): Response|JsonResponse
    {
        $cardData = $cardDataService->prepareAllCardDTOs($request);

        if (!empty($cardDataService->getServerErrors())) {
            return $this->sendInternelServerErrorResponse(['errors' => 'Something went wrong we are logging you out']);
        }

        if (empty($cardData)) {
            return $this->sendSuccessfulResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        if (!empty($cardDataService->getCardErrors())) {
            return $this->sendPartialContentResponse($serializer->serialize($cardData, 'json'));
        }

        return $this->sendSuccessfulResponse($serializer->serialize($cardData, 'json'));
    }


    /**
     * @Route("/card-state-view-form", name="cardViewForm", methods={"GET"})
     *
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return Response|JsonResponse
     */
    public function showCardViewForm(Request $request, CardDataService $cardDataService): Response|JsonResponse
    {
        $cardViewID = $request->query->get('cardViewID');

        if (empty($cardViewID)) {
            return $this->sendBadRequestResponse();
        }

        $cardFormDTO = $cardDataService->getCardViewFormDTO($cardViewID);

        if ($cardFormDTO === null || !empty($cardDataService->getServerErrors())) {
            return $this->sendInternelServerErrorResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $this->sendSuccessfulResponse($serializer->serialize($cardFormDTO, 'json'));
    }


    /**
     * @Route("/update-card-view", name="updateCardView", methods={"POST"})
     * @param Request $request
     * @param SensorDataService $sensorDataService
     * @param CardDataService $cardDataService
     * @return Response|JsonResponse
     */
    public function updateCardView(Request $request, SensorDataService $sensorDataService, CardDataService $cardDataService): Response|JsonResponse
    {
        $cardViewID = $request->get('cardViewID');

        $cardColourID = $request->get('cardColour');
        $cardIconID = $request->get('cardIcon');
        $cardStateID = $request->get('cardViewState');

        if (empty($cardColourID) || empty($cardIconID) || empty($cardStateID) || empty($cardViewID)) {
            return $this->sendBadRequestResponse(['errors' => 'empty form data']);
        }

        $cardViewData = [
            'cardColourID' => $request->get('cardColour'),
            'cardIconID' => $request->get('cardIcon'),
            'cardStateID' => $request->get('cardViewState'),
        ];

        $cardSensorReadingObject = $cardDataService->editSelectedCardData($cardViewID);
        $cardViewObject = array_shift($cardSensorReadingObject);
//        dd($cardViewObject);
        $cardViewForm = $this->createForm(CardViewForm::class, $cardViewObject);

        $handledCardViewForm = $sensorDataService->processForm($cardViewForm, $cardViewData);
        if ($handledCardViewForm instanceof FormInterface) {
            $sensorDataService->processSensorFormErrors($handledCardViewForm);

            return $this->sendBadRequestResponse($sensorDataService->getUserInputErrors());
        }

        $sensorTypeObject = $cardViewObject->getSensorNameID()->getSensorTypeID();

        //put this in service and swap request for sensor details array
//        $sensorFormData = $sensorDataService->prepareUpdateForOutOfBoundsForm($request, $sensorTypeObject);
        $sensorFormData = $sensorDataService->prepareSensorFormData($request, $sensorTypeObject,SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY);

        if (empty($sensorFormData)) {
            return $this->sendInternelServerErrorResponse($sensorDataService->getServerErrors());
        }

        foreach ($sensorFormData as $sensorType => $sensorData) {
            foreach ($cardSensorReadingObject as $sensorObject) {
//                dd($sensorFormData, $cardSensorReadingObject);
//                dd($sensorType, $sensorObject, $cardSensorReadingObject,  $sensorType == $sensorObject::class);
                if ($sensorType === $sensorObject::class) {
                                      //  dd('success', $sensorData);
                    $sensorForm = $this->createForm($sensorData['formToProcess'], $sensorObject, ['formSensorType' => new $sensorData['object']]);
                    $handledSensorForm = $sensorDataService->processForm($sensorForm, $sensorData['formData']);

                    if ($handledSensorForm instanceof FormInterface) {
                        $sensorDataService->processSensorFormErrors($handledSensorForm);
                    }
                    continue;
                }
            }
        }

        if (!empty($sensorDataService->getUserInputErrors())) {
            return $this->sendBadRequestResponse($sensorDataService->getUserInputErrors());
        }

        if (!empty($sensorDataService->getServerErrors())) {
            return $this->sendBadRequestResponse($sensorDataService->getServerErrors());
        }

        $this->getDoctrine()->getManager()->flush();

        return $this->sendSuccessfulUpdateJsonResponse();
    }

}
