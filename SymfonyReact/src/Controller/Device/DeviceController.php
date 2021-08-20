<?php


namespace App\Controller\Device;

use App\Services\ESPDeviceSensor\SensorData\SensorDeviceDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function updateSensorsCurrentReading(Request $request, SensorDeviceDataService $sensorDataService): Response
    {
        dd($request->getContent());


        if (empty($request->request->get('sensor-type'))) {
            return $this->sendBadRequestJsonResponse();
        }

        $sensorFormData = $sensorDataService->processSensorReadingUpdateRequest($request);

        if (empty($sensorFormData)) {
            return $this->sendInternelServerErrorJsonResponse();
        }
    }

}
