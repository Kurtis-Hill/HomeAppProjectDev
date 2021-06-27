<?php


namespace App\Controller\Device;



use App\Entity\Core\GroupNames;
use App\Form\FormMessages;
use App\Services\ESPDeviceSensor\Devices\DeviceServiceUser;
use App\Services\ESPDeviceSensor\SensorData\SensorDeviceDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/HomeApp/api/device")
 */
class DeviceController extends AbstractController
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
        dd($request->request->all());
        if (empty($request->request->get('sensor-type'))) {
            return $this->sendBadRequestJsonResponse();
        }

        $sensorFormData = $sensorDataService->processSensorReadingUpdateRequest($request);

        if (empty($sensorFormData)) {
            return $this->sendInternelServerErrorJsonResponse();
        }
    }

}
