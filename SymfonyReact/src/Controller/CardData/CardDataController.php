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

        if (empty($cardViewID)) {
            return $this->sendBadRequestResponse();
        }

        $cardViewData = [
            'cardcolourid' => $request->get('cardColour'),
            'cardiconid' => $request->get('icon'),
            'cardstateid' => $request->get('cardViewState'),
        ];

        $cardViewID = $request->get('cardViewID');

        $cardSensorData = $cardDataService->prepareUsersCurrentCardData($cardViewID);

        if (empty($cardSensorData)) {
            return $this->sendNotFoundResponse();
        }

        $cardViewForm = $this->createForm(CardViewModalFormType::class, $cardSensorData['cardViewObject']);



        $handledCardViewForm = $cardDataService->processForm($cardViewForm, $cardViewData);

        if (!empty($handledCardViewForm->getErrors())) {
            foreach ($handledCardViewForm->getErrors(true, true) as $value) {
                array_push($errors, $value->getMessage());
            }

            return $this->sendBadRequestResponse([$errors]);
        } elseif ($handledCardViewForm->isSubmitted() && $handledCardViewForm->isValid()) {
            $sensorType = $cardSensorData['cardViewObject']->getSensornameid()->getSensortypeid()->getSensortype();

            $cardDataService->handleSensorDataCardFormSubmission($request, $cardSensorData);

            switch ($sensorType) {
                case Sensortype::DALLAS_TEMPERATURE:
                    $firstSensorDataForm = $this->createForm(DallasTempCardModalForm::class, $cardSensorData['temp']);

                    $formData = [
                        'hightemp' => $request->get('tempHighReading'),
                        'lowtemp' => $request->get('tempLowReading'),
                        'constrecord' => $request->get('constRecord')
                    ];
                    break;

                case Sensortype::SOIL_SENSOR:
                    $firstSensorDataForm = $this->createForm(SoilFormType::class, $cardSensorData['analog']);

                    $formData = [
                        'highanalog' => $request->get('analogHighReading'),
                        'lowanalog' => $request->get('analogLowReading'),
                        'constrecord' => $request->get('constRecord')
                    ];
                    break;

                case Sensortype::DHT_SENSOR:
                    $firstSensorDataForm = $this->createForm(DHTTempCardModalForm::class, $cardSensorData['temp']);
                    $secondSensorDataForm = $this->createForm(DHTHumidCardModalForm::class, $cardSensorData['humid']);

                    $formData = [
                        'hightemp' => $request->get('tempHighReading'),
                        'lowtemp' => $request->get('tempLowReading'),
                        'constrecord' => $request->get('constRecord'),
                    ];

                    $secondFormData = [
                        'highhumid' => $request->get('humidHighReading'),
                        'lowhumid' => $request->get('humidLowReading'),
                        'constrecord' => $request->get('secondConstRecord')
                    ];
                    break;
            }
        }

        if (!empty($firstSensorDataForm && $formData)) {
            $firstForm = $cardDataService->processForm($firstSensorDataForm, $formData);

            if (!empty($firstForm->getErrors())) {
                foreach ($firstForm->getErrors(true, true) as $value) {
                    array_push($errors, $value->getMessage());
                }

                return $this->sendBadRequestResponse($errors);
            }
        }
        if (!empty($secondFormData && $secondFormData)) {
            $firstForm = $cardDataService->processForm($secondFormData, $secondFormData);

            if (!empty($firstForm->getErrors())) {
                foreach ($firstForm->getErrors(true, true) as $value) {
                    array_push($errors, $value->getMessage());
                }

                return $this->sendBadRequestResponse($errors);
            }

        }

        if (!empty($errors)) {
            return $this->sendBadRequestResponse($errors);
        } else {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->sendSuccessfulResponse();
        }
    }
}