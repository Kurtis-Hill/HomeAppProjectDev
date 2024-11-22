<?php

namespace App\Controller\Sensor\SensorControllers;

use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\Sensor\SensorDeletion\SensorDeletionInterface;
use App\Traits\HomeAppAPITrait;
use App\Voters\SensorVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor', name: 'new-sensor')]
class DeleteSensorController extends AbstractController
{
    use HomeAppAPITrait;

    public const DELETE_SENSOR_SUCCESS_MESSAGE = 'Sensor deleted successfully';

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('/{sensorID}', name: 'delete-sensor', methods: [Request::METHOD_DELETE])]
    public function deleteSensor(Sensor $sensor, Request $request, SensorDeletionInterface $sensorDeletionHandler): Response
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

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::ONLY->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

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
