<?php

namespace App\Controller\Sensor\SensorControllers;

use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\DTOs\RequestDTO;
use App\Entity\Sensor\Sensor;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Sensor\SensorDeletion\SensorDeletionHandler;
use App\Traits\HomeAppAPITrait;
use App\Voters\SensorVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor', name: 'delete-sensor')]
class DeleteSensorController extends AbstractController
{
    public const DELETE_SENSOR_SUCCESS_MESSAGE = 'Sensor deleted successfully';

    use HomeAppAPITrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('/{sensorID}', name: 'delete-sensor', methods: [Request::METHOD_DELETE])]
    #[IsGranted(SensorVoter::DELETE_SENSOR, subject: 'sensor')]
    public function deleteSensor(
        Sensor $sensor,
        SensorDeletionHandler $sensorDeletionHandler
    ): Response {
        $requestDTO ??= new RequestDTO();

        $sensorResponseData = SensorResponseDTOBuilder::buildSensorResponseDTO($sensor);

        try {
            $normalizedSensorResponseData = $this->normalize($sensorResponseData, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface $e) {
            $this->logger->error($e->getMessage(), ['user' => $this->getUser()?->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        $sensorDeletedSuccessfully = $sensorDeletionHandler->deleteSensor($sensor);
        if ($sensorDeletedSuccessfully === false) {
            return $this->sendInternalServerErrorJsonResponse();
        }

        return $this->sendSuccessfulJsonResponse($normalizedSensorResponseData, self::DELETE_SENSOR_SUCCESS_MESSAGE);
    }
}
