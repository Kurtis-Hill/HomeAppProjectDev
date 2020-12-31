<?php


namespace App\Controller\CardData;

use App\Form\CardViewForms\CardViewForm;
use App\HomeAppSensorCore\Interfaces\StandardSensorInterface;
use App\Services\CardDataService;
use App\Services\SensorDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
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
     * @param SensorDataService $sensorDataService
     * @return Response|JsonResponse
     */
    public function showCardViewForm(Request $request, SensorDataService $sensorDataService): Response|JsonResponse
    {
        $cardViewID = $request->query->get('cardViewID');

        if (empty($cardViewID)) {
            return $this->sendBadRequestResponse();
        }

        $cardFormDTO = $sensorDataService->getCardViewFormData($cardViewID);

        if ($cardFormDTO === null || !empty($sensorDataService->getServerErrors())) {
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
     * @return Response|JsonResponse
     */
    public function updateCardView(Request $request, SensorDataService $sensorDataService): Response|JsonResponse
    {
        $cardViewID = $request->get('cardViewID');

        $cardViewData = [
            'cardColourObject' => $request->get('cardColour'),
            'cardIconObject' => $request->get('cardIcon'),
            'cardStateObject' => $request->get('cardViewState'),
        ];

        foreach ($cardViewData as $data) {
            if ($data === null) {
                return $this->sendBadRequestResponse(['errors' => 'empty form data']);
            }
        }

        $cardSensorData = $sensorDataService->prepareUsersCurrentCardData($cardViewID);
        $cardViewObject = array_shift($cardSensorData);

        $cardViewForm = $this->createForm(CardViewForm::class, $cardViewObject);

        $handledCardViewForm = $sensorDataService->processForm($cardViewForm, $cardViewData);
        if ($handledCardViewForm instanceof FormInterface) {
            $sensorDataService->processSensorFormErrors($handledCardViewForm);

            return $this->sendBadRequestResponse($sensorDataService->getUserInputErrors());
        }

        $sensorTypeObject = $cardViewObject->getSensorObject()->getSensorTypeObject();
        $sensorFormData = $sensorDataService->prepareSensorFormData($request, $sensorTypeObject,'outOfBounds');

        if (empty($sensorFormData)) {
            return $this->sendInternelServerErrorResponse($sensorDataService->getServerErrors());
        }

        foreach ($sensorFormData as $sensorType => $sensorData) {
            foreach ($cardSensorData as $sensorObject) {
                if ($sensorType === $sensorObject::class) {
                   // dd($sensorData['object']);
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

        return $this->sendSuccessfulJsonResponse();
    }

}
