<?php


namespace App\Controller\Sensors;

use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Form\FormMessages;
use App\Services\CardUserDataService;
use App\Services\ESPDeviceSensor\SensorData\SensorDeviceDataService;
use App\Services\ESPDeviceSensor\SensorData\SensorUserDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\Voters\SensorVoter;
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
     * UNDER DEV
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

        if (empty($sensorData['sensorTypeID'] || $sensorData['deviceNameID'])) {
            return $this->sendBadRequestJsonResponse([FormMessages::FORM_PRE_PROCESS_FAILURE]);
        }

        $em = $this->getDoctrine()->getManager();
        $device = $em->getRepository(Devices::class)->findOneBy(['deviceNameID' => $sensorData['deviceNameID']]);

        if (!$device instanceof Devices) {
            return $this->sendBadRequestJsonResponse(['Cannot find device to add sensor too']);
        }
        try {
            $this->denyAccessUnlessGranted(SensorVoter::ADD_NEW_SENSOR, $device);
        } catch (\Exception) {
            return $this->sendForbiddenAccessJsonResponse(['errors' => [FormMessages::ACCES_DENIED]]);
        }

        $sensor = $sensorService->createNewSensor($sensorData);

        if (!empty($sensorService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($sensorService->getUserInputErrors());
        }
        if ($sensor === null || !empty($sensorService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse($sensorService->getServerErrors());
        }
        if ($sensor instanceof Sensors) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            $newSensorCard = $cardDataService->createNewSensorCard($sensor, $this->getUser());

            if ($newSensorCard === null || !empty($sensorService->getServerErrors())) {
                return $this->sendInternelServerErrorJsonResponse($sensorService->getServerErrors() ?? ['errors' => 'Something went wrong please try again']);
            }
            $sensorReadingType = $sensorService->handleSensorReadingTypeCreation($sensor);

            if (!empty($sensorService->getUserInputErrors())) {
                $em->remove($sensor);
                $em->flush();
                return $this->sendBadRequestJsonResponse($sensorService->getUserInputErrors());
            }
            if (!empty($sensorService->getServerErrors() || $sensorReadingType === null)) {
                $em->remove($sensor);
                $em->flush();
                return $this->sendInternelServerErrorJsonResponse($sensorService->getServerErrors() ?? ['errors' => 'Something went wrong please try again']);
            }

            $em->persist($sensorReadingType);

            $em->flush();

            $sensorID = $sensor->getSensorNameID();

            return $this->sendCreatedResourceJsonResponse(['sensorNameID' => $sensorID]);
        }

        return $this->sendBadRequestJsonResponse(['Something trying to add a sensor didnt return a sensor, make sure your app is up to date']);
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
