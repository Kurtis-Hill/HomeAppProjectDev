<?php


namespace App\Controller\CardData;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Icons;
use App\Entity\Core\Sensortype;
use App\Form\CardViewForms\CardViewModalFormType;
use App\Form\CardViewForms\DallasTempCardModalForm;
use App\Form\CardViewForms\DHTHumidCardModalForm;
use App\Form\CardViewForms\DHTTempCardModalForm;
use App\Form\CardViewForms\SoilFormType;
use App\Services\CardDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Doctrine\DBAL\DBALException;
use Doctrine\Instantiator\Exception\ExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CardDataController
 * @package App\Controller\CardData
 * @Route("/HomeApp/api/card-data")
 *
 * hyphenate urls
 */
class CardDataController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/index-view", name="indexCardData")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     */
    public function returnIndexAllCardData(Request $request, CardDataService $cardDataService): JsonResponse
    {
        $cardData = $cardDataService->prepareAllIndexCardData('JSON');

        if (empty($cardData)) {
            return $this->sendInternelServerErrorResponse(['Something went wrong we are logging you out']);
        }

        return $this->sendSuccessfulResponse($cardData);
    }


    /**
     * @Route("/device-view", name="roomCardData")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     */
    public function returnAllDeviceCardData(CardDataService $cardDataService, Request $request): JsonResponse
    {
        $deviceName = $request->query->get('device-name');
        $deviceGroup = $request->query->get('device-group');
        $deviceRoom = $request->query->get('device-room');

        if (!$deviceName || $deviceGroup || $deviceRoom) {
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
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     */
    public function returnAllRoomDeviceCardData(CardDataService $cardDataService, Request $request): JsonResponse
    {
        $deviceName = $request->query->get('device-name');
        $deviceGroup = $request->query->get('device-group');
        $deviceRoom = $request->query->get('device-room');

        if (!$deviceName || $deviceGroup || $deviceRoom) {
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
     * @Route("/card-state-view-form&id={cardviewid}", name="cardViewForm")
     * @param Request $request
     * @return JsonResponse
     */
    public function showCardViewForm(Request $request, $cardviewid): JsonResponse
    {
        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getCardFormData(['id' => $cardviewid]);

        $icons = $this->getDoctrine()->getRepository(Icons::class)->getAllIcons();
        $colours = $this->getDoctrine()->getRepository(Cardcolour::class)->getAllColours();
        $states = $this->getDoctrine()->getRepository(Cardstate::class)->getAllStates();

        $cardFormData = ['cardSensorData' => $cardSensorData, 'icons' => $icons, 'colours' => $colours, 'states' => $states];

        return new JsonResponse($cardFormData);
    }

    /**
     * @Route("/update-card-view", name="updateCardView")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     *
     */
    public function updateCardView(Request $request, CardDataService $cardDataService): JsonResponse
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

        $cardSensorData = $cardDataService->prepareUsersCurrentCardData($cardViewID);

        if (empty($cardSensorData)) {
            return $this->sendNotFoundResponse();
        }

        $sensorType = $cardSensorData['cardViewObject']->getSensornameid()->getSensortypeid()->getSensortype();

        $prepareSensorForm = $cardDataService->prepareSensorFormData($request, $cardSensorData, $sensorType);

        $cardViewForm = $this->createForm(CardViewModalFormType::class, $cardSensorData['cardViewObject']);

        $sensorDataForm = $this->createForm($prepareSensorForm['formClass'], $prepareSensorForm['object']);

        if (!empty($prepareSensorForm['secondObject'])) {
            $secondSensorDataForm = $this->createForm($prepareSensorForm['secondFormClass'], $prepareSensorForm['secondObject']);

            $secondHandledSensorDataForm = $cardDataService->processForm($secondSensorDataForm, $prepareSensorForm['secondFormData']);

            if ($secondHandledSensorDataForm instanceof FormInterface) {
                foreach ($secondHandledSensorDataForm->getErrors(true, true) as $error) {
                    array_push($errors, $error->getMessage());
                }
            }
        }

        $handledCardViewForm = $cardDataService->processForm($cardViewForm, $cardViewData);
        $handledSensorDataForm = $cardDataService->processForm($sensorDataForm, $prepareSensorForm['formData']);

        if ($handledCardViewForm instanceof FormInterface) {
            foreach ($secondHandledSensorDataForm->getErrors(true, true) as $error) {
                array_push($errors, $error->getMessage());
            }
        }

        if ($handledSensorDataForm instanceof FormInterface) {
            foreach ($handledSensorDataForm->getErrors(true, true) as $error) {
                array_push($errors, $error->getMessage());
            }
        }


        if (!empty($errors)) {
            return $this->sendBadRequestResponse($errors);
        } else {
            $this->getDoctrine()->getManager()->flush();

            return $this->sendSuccessfulResponse();
        }
    }
}