<?php


namespace App\Controller\Device;

use App\Producers\SensorProducers\SensorUpdateCurrentReadingProducer;
use App\Traits\API\HomeAppAPIResponseTrait;
use Exception;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

#[Route('/HomeApp/api/device', name: 'device')]
class DeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * UPDATE SENSOR METHODS
     * UNDER DEV
     *
     * @param Request $request
     * @param TokenInterface $security
     * @return JsonResponse|Response
     */
    #[Route('/update/current-reading', name: 'update-current-reading', methods: [Request::METHOD_PUT])]
    public function updateSensorsCurrentReading(
        Request $request,
        Security $security,
        $updateReadingProducer
//        SensorUpdateCurrentReadingProducer $currentReadingProducer
    ): JsonResponse|Response {
//        try {
//            $sensorData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
//        } catch (JsonException) {
//            return $this->sendBadRequestJsonResponse(['the format sent is not expected, please send requests in JSON']);
//        }

        $isCallable = [$security->getUser(), 'getDeviceNameID'];

        if (!is_callable($isCallable) || !$security->getUser()->getDeviceNameID()) {
            return $this->sendBadRequestJsonResponse(['No device id found for device']);
        }

        $deviceId = $security->getUser()->getDeviceNameID();


//        $rabbitMQ = $this->get('old_sound_rabbit_mq.current-reading-upload-sensor-data');
//        dd($updateReadingProducer);
//        $rabbitMQ = $this->get($updateReadingProducer);

        $sensorData = [
            'sensorData' => [
                'deviceId' => '1176',
                'sensorType' => 'Dht',
                'sensorName' => 'Bmp11',
                'currentReading' => '12'
            ]
        ];

        $hey = json_encode($sensorData);
//        dd($hey);

        try {
            foreach ($sensorData['sensorData'] as $sensorUpdateData) {
                $sensorUpdateData['deviceId'] = $deviceId;

//                $currentReadingProducer->publish(serialize($sensorUpdateData));
            }
        } catch (Exception $exception) {
            return $this->sendBadRequestJsonResponse(['Failed to produce messages ' . $exception->getMessage()]);
        }

        return $this->sendSuccessfulJsonResponse(['Sensor data accepted']);
    }

}
