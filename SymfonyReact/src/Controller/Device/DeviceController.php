<?php


namespace App\Controller\Device;



use App\Entity\Core\GroupNames;
use App\Form\FormMessages;
use App\Services\ESPDeviceSensor\Devices\DeviceServiceUser;
use App\Services\ESPDeviceSensor\SensorData\SensorDeviceDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\Voters\DeviceVoter;
use OldSound\RabbitMqBundle\OldSoundRabbitMqBundle;
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
     * @return Response
     */
    public function queSensorReadingsForProcessing(Request $request, OldSoundRabbitMqBundle $bundle): Response
    {
//        dd($request->request->all());
        $postData = $request->request;
        if (empty($postData->get('sensor-type') || $postData->get('device-name') || $postData->get('sensor-name1'))) {
            return $this->sendBadRequestJsonResponse(['missing some required fields']);
        }

        $rabbitMq = $this->get('old_sound_rabbit_mq.upload_current_reading_sensor_data')->publish(serialize($postData->all()));;

//        $sensorFormData = $sensorDataService->processSensorReadingUpdateRequest($request);

//        if (empty($sensorFormData)) {
//            return $this->sendInternelServerErrorJsonResponse();
//        }
    }

}
