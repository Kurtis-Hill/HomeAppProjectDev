<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controller\CardData;

use App\Entity\Card\Cardview;
use App\Form\CardViewForms\CardViewForm;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use App\Services\CardDataService;
use App\Services\SensorDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
     * @Route("/index-view", name="indexCardData")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @param Serializer $serializer
     * @return JsonResponse
     */
    public function returnIndexAllCardData(Request $request, CardDataService $cardDataService): JsonResponse
    {

        $cardData = $cardDataService->prepareAllIndexCardDTOs();

        if (empty($cardData)) {
            return $this->sendInternelServerErrorResponse(['Something went wrong we are logging you out']);
        }
//        dd($serializer->serialize($cardData, 'json'));
        return $this->sendSuccessfulResponse($cardData);
    }

    /**
     * @Route("/device-view", name="deviceCardData")
     */
    public function returnAllDeviceCardData(Request $request, CardDataService $cardDataService): JsonResponse
    {
        $deviceName = $request->query->get('device-name');
        $deviceGroup = $request->query->get('device-group');
        $deviceRoom = $request->query->get('device-room');

        if (empty($deviceName || $deviceGroup || $deviceRoom)) {
            return $this->sendBadRequestResponse(['errors' => 'No card data found query if you have devices please logout and back in again please']);
        }

        $deviceDetails = ['deviceName' => $deviceName, 'deviceGroup' => $deviceGroup, 'deviceRoom' => $deviceRoom];

        $cardData = $cardDataService->prepareAllDevicePageCardData('JSON', $deviceDetails);

        if (empty($cardData)) {
            return $this->sendInternelServerErrorResponse();
        }

        return $this->sendSuccessfulResponse($cardData);
    }

    /**
     * @Route("/room-view", name="roomCardData")
     */
    public function returnAllRoomDeviceCardData(Request $request, CardDataService $cardDataService): JsonResponse
    {
        $deviceName = $request->query->get('device-name');
        $deviceGroup = $request->query->get('device-group');
        $deviceRoom = $request->query->get('device-room');

        if (empty($deviceName || $deviceGroup || $deviceRoom)) {
            return $this->sendBadRequestResponse(['errors' => 'No card data found query if you have devices please logout and back in again please']);
        }

        $deviceDetails = ['deviceName' => $deviceName, 'deviceGroup' => $deviceGroup, 'deviceRoom' => $deviceRoom];

        $cardData = $cardDataService->prepareAllRoomPageCardData('JSON', $deviceDetails);

        if (empty($cardData)) {
            return $this->sendInternelServerErrorResponse();
        }

        return $this->sendSuccessfulResponse($cardData);
    }

    /**
     * @Route("/card-state-view-form&id={cardviewid}", name="cardViewForm")
     *
     * @param mixed $cardviewid
     */
    public function showCardViewForm(Request $request, SensorDataService $sensorDataService, $cardviewid): JsonResponse
    {
        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]);

        if (empty($cardSensorData)) {
            return $this->sendBadRequestResponse();
        }

        $formData = $sensorDataService->getFormData($cardSensorData);

        return $this->sendSuccessfulResponse($formData);
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

        return $this->sendSuccessfulResponse();
    }
}
