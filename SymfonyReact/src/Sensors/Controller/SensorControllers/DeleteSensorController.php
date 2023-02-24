<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\SensorServices\SensorDeletion\SensorDeletionInterface;
use App\Sensors\Voters\SensorVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors', name: 'new-sensor')]

class DeleteSensorController extends AbstractController
{
    use HomeAppAPITrait;

    public const DELETE_SENSOR_SUCCESS_MESSAGE = 'Sensor deleted successfully';

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    #[Route('/{sensorID}/delete', name: 'delete-sensor', methods: [Request::METHOD_DELETE])]
    public function deleteSensor(Sensor $sensor, SensorDeletionInterface $sensorDeletionHandler): Response
    {
        try {
            $this->denyAccessUnlessGranted(SensorVoter::DELETE_SENSOR, $sensor);
        } catch (AccessDeniedException) {
            $this->logger->info('User tried to delete sensor without permission', [
                'user' => $this->getUser()?->getUserIdentifier(),
                'sensor' => $sensor->getSensorID()
            ]);

            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $sensorResponseData = SensorResponseDTOBuilder::buildSensorResponseDTO($sensor);

        try {
            $normalizedSensorResponseData = $this->normalizeResponse($sensorResponseData);
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
