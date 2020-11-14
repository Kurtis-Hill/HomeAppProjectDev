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
//            dd('hey');
            return $this->sendInternelServerErrorResponse(['Something went wrong we are logging you out']);
        }

        return $this->sendSuccessfulResponse($cardData);
    }

    /**
     * @Route("/room-view", name="roomCardData")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     */
    public function returnAllRoomCardData(CardDataService $cardDataService, Request $request): JsonResponse
    {
        $deviceName = $request->query->get('device-name');
        $deviceGroup = $request->query->get('device-group');
        $deviceRoom = $request->query->get('device-room');

        $deviceDetails = ['deviceName' => $deviceName, 'deviceGroup' => $deviceGroup, 'deviceRoom' => $deviceRoom];

        try {
            $cardData = $cardDataService->prepareAllDevicePageCardData('JSON', $deviceDetails);
        } catch(\Exception $e){
            $errorMessage[] = $e->getMessage();
        }

        if (empty($cardData)) {
            return $this->sendBadRequestResponse(['errors' => 'No card data found query if you have devices please logout and back in again please']);
        }

        if (!empty($errorMessage)) {
            return $this->sendInternelServerErrorResponse();
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

        $deviceDetails = ['deviceName' => $deviceName, 'deviceGroup' => $deviceGroup, 'deviceRoom' => $deviceRoom];

        $cardData = $cardDataService->prepareAllDevicePageCardData('JSON', $deviceDetails);

        if ($cardData instanceof \Exception || $cardData instanceof \PDOException) {

            return new JsonResponse($cardData, 500);
        }

        if (!$cardData) {
            return new JsonResponse(['errors' => 'No card data found query error please logout and back in again please'], 400);
        }

        return new JsonResponse($cardData);
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
     * @Route("/update-cardview", name="updateCardView")
     * @param Request $request
     * @param CardDataService $cardDataService
     * @return JsonResponse
     *
     * Refactor form into service
     */
    public function updateCardView(Request $request, CardDataService $cardDataService): JsonResponse
    {
        $errors = [];
        $cardViewID = $request->get('cardViewID');

        $cardSensorData = $this->getDoctrine()->getRepository(Cardview::class)->getUsersCurrentCardData(['id' => $cardViewID, 'userID' =>  $cardDataService->getUserID()]);

        if (!$cardSensorData) {
            return new JsonResponse('No Sensor Found', 404);
        }

        $sensorType = $cardSensorData['cardView']->getSensornameid()->getSensortypeid()->getSensortype();

        $cardViewForm = $this->createForm(CardViewModalFormType::class, $cardSensorData['cardView']);

        $cardViewForm->submit([
                'cardcolourid' => $request->get('cardColour'),
                'cardiconid' => $request->get('icon'),
                'cardstateid' => $request->get('cardViewState'),
            ]
        );

        if ($cardViewForm->isSubmitted() && $cardViewForm->isValid()) {
            $form = null;

            if ($sensorType === Sensortype::DHT_SENSOR) {
                $form = $this->createForm(DHTTempCardModalForm::class, $cardSensorData['temp']);
                $formData = [
                    'hightemp' => $request->get('tempHighReading'),
                    'lowtemp' => $request->get('tempLowReading'),
                    'constrecord' => $request->get('constRecord'),
                ];

                $secondForm = $this->createForm(DHTHumidCardModalForm::class, $cardSensorData['humid']);
                $secondFormData = [
                    'highhumid' => $request->get('humidHighReading'),
                    'lowhumid' => $request->get('humidLowReading'),
                    'constrecord' => $request->get('secondConstRecord')
                ];
            }

            if ($sensorType === Sensortype::DALLAS_TEMPERATURE) {
                $form = $this->createForm(DallasTempCardModalForm::class, $cardSensorData['temp']);
                $formData = [
                    'hightemp' => $request->get('tempHighReading'),
                    'lowtemp' => $request->get('tempLowReading'),
                    'constrecord' => $request->get('constRecord')
                ];
            }

            if ($sensorType === Sensortype::SOIL_SENSOR) {
                $form = $this->createForm(SoilFormType::class, $cardSensorData['analog']);
                $formData = [
                    'highanalog' => $request->get('analogHighReading'),
                    'lowanalog' => $request->get('analogLowReading'),
                    'constrecord' => $request->get('constRecord')
                ];
            }

            if ($form !== null) {
                $processedForm = $cardDataService->processForm($form, $formData);
                if ($processedForm instanceof FormInterface) {
                    foreach ($processedForm->getErrors(true, true) as $value) {
                        array_push($errors, $value->getMessage());
                    }
                }

                if (isset($secondForm)) {
                    $secondProcessedForm = $cardDataService->processForm($secondForm, $secondFormData);
                    if ($secondProcessedForm instanceof FormInterface) {
                        foreach ($secondProcessedForm->getErrors(true, true) as $value) {
                            array_push($errors, $value->getMessage());
                        }
                    }
                }
            }
            else {
                return new JsonResponse($errors[] = 'Sensor Not Recognised', 500);
            }
        }

        else {
            $errors[] = "CardView Form Not Valid";
        }

        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors], 400);
        }
        else {
            $em = $this->getDoctrine()->getManager();
            try {
                $em->flush();
            } catch (\Exception $e) {
                $e->getMessage();
            }

            return new JsonResponse('success', 200);
        }
    }
}