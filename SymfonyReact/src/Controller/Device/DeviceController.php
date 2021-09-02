<?php


namespace App\Controller\Device;

use App\AMQP\Producers\UpdateCurrentDataProducer;
use App\Traits\API\HomeAppAPIResponseTrait;
use Exception;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
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

    private $currentReadingAMQPProducer;

    public function __construct(ProducerInterface $uploadService)
    {
        $this->currentReadingAMQPProducer = $uploadService;
    }

    /**
     * UPDATE SENSOR METHODS
     * UNDER DEV
     *
     * @param Request $request
     * @param Security $security
     * @return JsonResponse|Response
     */
    #[Route('/update/current-reading', name: 'update-current-reading', methods: [Request::METHOD_PUT])]
    public function updateSensorsCurrentReading(
        Request $request,
        Security $security,
    ): JsonResponse|Response {
        try {
            $sensorData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['the format sent is not expected, please send requests in JSON']);
        }

        $isCallable = [$security->getUser(), 'getDeviceNameID'];

        if (!is_callable($isCallable) || !$security->getUser()->getDeviceNameID()) {
            return $this->sendBadRequestJsonResponse(['No device id found for device']);
        }

        $deviceId = $security->getUser()->getDeviceNameID();

//        $rabbitMQ = $this->get('old_sound_rabbit_mq.current-reading-upload-sensor-data');

        try {
            foreach ($sensorData['sensorData'] as $sensorUpdateData) {
//                dd($sensorUpdateData);
//                $sensorUpdateData['deviceId'] = $deviceId;

                $this->currentReadingAMQPProducer->publish(serialize($sensorUpdateData));
            }
        } catch (Exception $exception) {
            return $this->sendBadRequestJsonResponse(['Failed to produce messages ' .$exception->getMessage()]);
        }

        return $this->sendSuccessfulJsonResponse(['Sensor data accepted']);
    }

    /**
     * @param ProducerInterface $producer
     */
    public function setCurrentReadingProducer(ProducerInterface $producer)
    {
        $this->currentReadingAMQPProducer = $producer;
    }
}
