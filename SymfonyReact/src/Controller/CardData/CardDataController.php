<?php


namespace App\Controller\CardData;


use App\Entity\Card\CardView;
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
 * @Route("/HomeApp/api/card-data",  methods={"GET"})
 *
 */
class CardDataController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/cards", name="indexCardData")
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

        if (!empty($cardDataService->getUserInputErrors())) {
            return $this->sendBadRequestResponse(['errors' => 'No card data found for query if you have devices please logout and back in again please']);
        }

        if (empty($cardData)) {
            return $this->sendSuccessfulResponse('No Sensors Found');
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $this->sendSuccessfulResponse($serializer->serialize($cardData, 'json'));
    }

    /**
     * @Route("/card-state-view-form", name="cardViewForm")
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

        if (empty($cardFormDTO)) {
            return $this->sendBadRequestResponse();
        }

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);
//dd($serializer->serialize($cardFormDTO, 'json'));
        return $this->sendSuccessfulResponse($serializer->serialize($cardFormDTO, 'json'));
    }


    /**
     * @Route("/update-card-view", name="updateCardView")
     */
    public function updateCardView(Request $request, SensorDataService $sensorDataService): JsonResponse
    {
        $errors = [];
        $cardViewID = $request->get('cardViewID');

        $cardViewData = [
            'cardcolourid' => $request->get('cardColour'),
            'cardiconid' => $request->get('icon'),
            'cardstateid' => $request->get('cardViewState'),
        ];

        if (empty($cardViewID || $cardViewData['cardcolourid'] || $cardViewData['cardiconid'] || $cardViewData['cardstateid'])) {
            return $this->sendBadRequestResponse();
        }

        $cardSensorData = $sensorDataService->prepareUsersCurrentCardData($cardViewID);

        if (empty($cardSensorData)) {
            return $this->sendNotFoundResponse();
        }

        $sensorType = $cardSensorData['cardViewObject']->getSensornameid()->getSensortypeid()->getSensortype();

        $prepareSensorForm = $sensorDataService->prepareSensorFormData($request, $cardSensorData, $sensorType);

        if (!$prepareSensorForm['object'] instanceof StandardSensorInterface) {
            $errors[] = 'Sensor Not Recognised';
        }

        $cardViewForm = $this->createForm(CardViewForm::class, $cardSensorData['cardViewObject']);
        $sensorDataForm = $this->createForm($prepareSensorForm['formClass'], $prepareSensorForm['object']);

        if (!empty($prepareSensorForm['secondObject'])) {
            $secondSensorDataForm = $this->createForm($prepareSensorForm['secondFormClass'], $prepareSensorForm['secondObject']);

            $secondHandledSensorDataForm = $sensorDataService->processForm($secondSensorDataForm, $prepareSensorForm['secondFormData']);

            if ($secondHandledSensorDataForm instanceof FormInterface) {
                foreach ($secondHandledSensorDataForm->getErrors(true, true) as $error) {
                    $errors[] = $error->getMessage();
                }
            }
        }

        $handledCardViewForm = $sensorDataService->processForm($cardViewForm, $cardViewData);
        $handledSensorDataForm = $sensorDataService->processForm($sensorDataForm, $prepareSensorForm['formData']);

        if ($handledCardViewForm instanceof FormInterface) {
            foreach ($handledCardViewForm->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }
        }

        if ($handledSensorDataForm instanceof FormInterface) {
            foreach ($handledSensorDataForm->getErrors(true, true) as $error) {
                $errors[] = $error->getMessage();
            }
        }

        if (!empty($errors)) {
            return $this->sendBadRequestResponse($errors);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->sendSuccessfulJsonResponse();
    }
}
