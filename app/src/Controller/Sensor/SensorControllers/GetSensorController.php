<?php

namespace App\Controller\Sensor\SensorControllers;

use App\Builders\Sensor\Request\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\DTOs\RequestDTO;
use App\DTOs\Sensor\Request\GetSensorRequestDTO\GetSensorRequestDTO;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\PaginationCalculator;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Sensor\SensorUserFilter;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors')]
class GetSensorController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    public const GET_SENSOR_DEFAULT_LIMIT = 100;

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('', name: 'get-all-sensors', methods: [Request::METHOD_GET])]
    public function getAllSensors(
        Request $request,
        ValidatorInterface $validator,
        SensorResponseDTOBuilder $sensorResponseDTOBuilder,
        SensorUserFilter $sensorUserFilter,
        SensorRepositoryInterface $sensorRepository,
        #[MapQueryString]
        ?RequestDTO $requestDTO = null,
    ): JsonResponse {
        $requestDTO ??= new RequestDTO();
        $deviceIDs = $request->query->all()['deviceIDs'] ?? null;
        $deviceNames = $request->query->all()['deviceNames'] ?? null;
        $groupIDs = $request->query->all()['groupIDs'] ?? null;
        $cardViewIDs = $request->query->all()['cardViewIDs'] ?? null;

        $sensorRequestDTO = new GetSensorRequestDTO();
        $sensorRequestDTO->setLimit($requestDTO->getLimit());
        $sensorRequestDTO->setPage($requestDTO->getPage());
        $sensorRequestDTO->setDeviceIDs($deviceIDs);
        $sensorRequestDTO->setDeviceNames($deviceNames);
        $sensorRequestDTO->setGroupIDs($groupIDs);
        $sensorRequestDTO->setCardViewIDs($cardViewIDs);

        $validationErrors = $validator->validate($sensorRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        $offset = PaginationCalculator::calculateOffset($sensorRequestDTO->getLimit(), $sensorRequestDTO->getPage());
        $getSensorQueryDTO = GetSensorQueryDTOBuilder::buildGetSensorQueryDTO(
            $sensorRequestDTO->getLimit(),
            $offset,
            $sensorRequestDTO->getPage(),
            $sensorRequestDTO->getDeviceIDs(),
            $sensorRequestDTO->getDeviceNames(),
            $sensorRequestDTO->getGroupIDs(),
            $sensorRequestDTO->getCardViewIDs(),
        );

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
        }
        $sensors = $sensorRepository->findSensorsByQueryParameters($getSensorQueryDTO);

        $allowedSensors = $sensorUserFilter->filterSensorsAllowedForUser($sensors, $getSensorQueryDTO);
        foreach ($allowedSensors as $sensor) {
            $sensorDTOs[] = $sensorResponseDTOBuilder->buildFullSensorResponseDTOWithPermissions($sensor, [$requestDTO->getResponseType()]);
        }

        if (empty($sensorDTOs)) {
            if (!empty($sensorUserFilter->getErrors())) {
                return $this->sendBadRequestJsonResponse($sensorUserFilter->getErrors());
            }

            return $this->sendSuccessfulJsonResponse([], 'No sensors found');
        }

        try {
            $normalizedResponse = $this->normalize($sensorDTOs, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        if (!empty($sensorUserFilter->getErrors())) {
            return $this->sendMultiStatusJsonResponse($sensorUserFilter->getErrors(), $normalizedResponse, 'Some issues were found with your request');
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
