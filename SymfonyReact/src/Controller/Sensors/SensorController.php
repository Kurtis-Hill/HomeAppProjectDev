<?php


namespace App\Controller\Sensors;

use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Services\CardUserDataService;
use App\Services\ESPDeviceSensor\SensorData\SensorDeviceDataService;
use App\Services\ESPDeviceSensor\SensorData\SensorUserDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/HomeApp/api/sensors", name="devices")
 */
class SensorController extends AbstractController
{
    use HomeAppAPIResponseTrait;


    /**
     * UPDATE SENSOR METHODS
     *
     * @Route("/update/current-reading", name="update-current-reading")
     * @param Request $request
     * @param SensorDeviceDataService $sensorDataService
     * @return Response
     */
    public function updateSensorsCurrentReading(Request $request, SensorDeviceDataService $sensorDataService): Response
    {
        if (empty($request->request->get('sensor-type'))) {
            return $this->sendBadRequestJsonResponse();
        }

        $sensorFormData = $sensorDataService->processSensorReadingUpdateRequest($request);

        if (empty($sensorFormData)) {
            return $this->sendInternelServerErrorJsonResponse();
        }


    }

    /**
     * @Route("/add-new-sensor", name="add-new-sensor")
     * @param Request $request
     * @param SensorUserDataService $sensorService
     * @param CardUserDataService $cardDataService
     * @return JsonResponse
     */
    public function addNewSensor(Request $request, SensorUserDataService $sensorService, CardUserDataService $cardDataService): JsonResponse
    {
        $sensorData = [
            'sensorName' => $request->request->get('sensor-name'),
            'sensorTypeID' => $request->get('sensor-type'),
            'deviceNameID' => $request->get('device-id')
        ];

        if (empty($sensorData['sensorName'] || $sensorData['sensorTypeID'] || $sensorData['deviceNameID'])) {
            return $this->sendBadRequestJsonResponse(['errors' => 'Bad request somethings wrong with your form data, if the problem persists log out an back in again']);
        }

        $newSensorForm = $sensorService->createNewSensor($sensorData);

        if (!empty($sensorService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($sensorService->getUserInputErrors());
        }
        if (!empty($sensorService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse(['errors' => 'Something went wrong please try again']);
        }

        $sensor = $newSensorForm->getData();
        if ($sensor instanceof Sensors) {
            $newSensorCard = $cardDataService->createNewSensorCard($newSensorForm->getData());
            $sensorID = $newSensorForm->getData()->getSensorNameID();
            $sensorService->handleSensorCreation($newSensorForm->getData(), $newSensorCard, $sensorData);

            if (!empty($sensorService->getUserInputErrors())) {
                return $this->sendBadRequestJsonResponse($sensorService->getUserInputErrors());
            }

            if (!empty($sensorService->getServerErrors())) {
                return $this->sendInternelServerErrorJsonResponse(['errors' => 'Something went wrong please try again']);
            }

            return $this->sendCreatedResourceJsonResponse(['sensorNameID' => $sensorID]);
        }

        return $this->sendBadRequestJsonResponse();
    }

    /**
     * @Route("/types", name="get-sensor-types")
     * @return JsonResponse
     */
    public function returnAllSensorTypes(): Response
    {
        $sensorTypes = $this->getDoctrine()->getManager()->getRepository(SensorType::class)->findAll();

        $encoders = [new JsonEncoder()];
        $normaliser = [new ObjectNormalizer()];

        $serializer = new Serializer($normaliser, $encoders);

        return $this->sendSuccessfulResponse($serializer->serialize($sensorTypes, 'json'));
    }


}
