<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Voters\SensorVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor')]
class GetSingleSensorsController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('/{sensorID}/get', name: 'get-single-sensor', methods: [Request::METHOD_GET])]
    public function getSingleSensor(Sensor $sensor, Request $request, SensorResponseDTOBuilder $sensorResponseDTOBuilder): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(SensorVoter::GET_SENSOR, $sensor);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::SENSITIVE_ONLY->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        try {
            $sensorReadingTypeResponseDTOs = $sensorResponseDTOBuilder->buildFullSensorResponseDTOWithPermissions($sensor, [$requestDTO->getResponseType()]);
        } catch (ReadingTypeNotExpectedException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }

        try {
            $normalizedResponse = $this->normalize($sensorReadingTypeResponseDTOs, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
