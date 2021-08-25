<?php


namespace App\Controller\Device;

use App\Services\ESPDeviceSensor\SensorData\SensorDeviceDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/api/device', name: 'device')]
class DeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * UPDATE SENSOR METHODS
     * UNDER DEV
     *
     * @param Request $request
     * @param SensorDeviceDataService $sensorDataService
     * @return Response
     */
    #[Route('/update/current-reading', name: 'update-current-reading', methods: [Request::METHOD_PUT])]
    public function updateSensorsCurrentReading(Request $request, SensorDeviceDataService $sensorDataService): JsonResponse|Response
    {
        try {
            $sensorData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['the format sent is not expected, please send requests in JSON']);
        }

        foreach ($sensorData['sensors'] as $sensor) {

        }

        if (empty($request->request->get('sensor-type'))) {
            return $this->sendBadRequestJsonResponse();
        }

        $sensorQueueSuccess = $sensorDataService->processSensorReadingUpdateRequest($sensorData);

        if (empty($sensorQueueSuccess)) {
            return $this->sendInternelServerErrorJsonResponse();
        }
    }

}
