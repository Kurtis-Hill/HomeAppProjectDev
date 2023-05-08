<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Builders\Request\RequestDTOBuilder;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\PaginationCalculator;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Request\GetSensorRequestDTO\GetSensorRequestDTO;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\GetSensorHandler;
use App\Sensors\SensorServices\GetSensorReadingTypeHandler;
use App\User\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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

    #[Route('/all', name: 'get-all-sensors', methods: [Request::METHOD_GET])]
    public function getAllSensors(
        Request $request,
        ValidatorInterface $validator,
        SensorResponseDTOBuilder $sensorResponseDTOBuilder,
        GetSensorHandler $getSensorHandler,
        SensorRepositoryInterface $sensorRepository
    ): JsonResponse {
        $responseType = $request->query->get('responseType');
        $limit = $request->query->get('limit', self::GET_SENSOR_DEFAULT_LIMIT);
        $page = $request->query->get('page', 1);
        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $responseType,
                $page,
                $limit,
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $deviceIDs = $request->query->all()['deviceIDs'] ?? null;
        $deviceNames = $request->query->all()['deviceNames'] ?? null;
        $groupIDs = $request->query->all()['groupIDs'] ?? null;

        $sensorRequestDTO = new GetSensorRequestDTO();
        $sensorRequestDTO->setLimit($requestDTO->getLimit());
        $sensorRequestDTO->setPage($requestDTO->getPage());
        $sensorRequestDTO->setDeviceIDs($deviceIDs);
        $sensorRequestDTO->setDeviceNames($deviceNames);
        $sensorRequestDTO->setGroupIDs($groupIDs);

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
        );

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
        }

        $errors = $getSensorHandler->validateUserIsAllowedToGetSensors($getSensorQueryDTO, $user);

        $sensors = $sensorRepository->findSensorsByQueryParameters($getSensorQueryDTO);

        $sensorDTOs = [];
//        if ($requestDTO->getResponseType() === RequestDTOBuilder::REQUEST_TYPE_ONLY) {
        foreach ($sensors as $sensor) {
            $sensorDTOs[] = $sensorResponseDTOBuilder->buildFullSensorResponseDTO($sensor, [$requestDTO->getResponseType()]);
//            dd($sensorDTOs);
        }
//        } elseif ($requestDTO->getResponseType() === RequestDTOBuilder::REQUEST_TYPE_FULL) {
//            foreach ($sensors as $sensor) {
//                $sensorDTOs[] =  SensorResponseDTOBuilder::buildSensorResponseDTO(
//                    $sensor,
//                    $getSensorReadingTypeHandler->handleSensorReadingTypeDTOCreating($sensor)
//                );
//            }
//        }

        if (empty($sensorDTOs)) {
            if (!empty($errors)) {
                return $this->sendMultiStatusJsonResponse($errors, [], self::SOME_ISSUES_WITH_REQUEST);
            }
            return $this->sendSuccessfulJsonResponse([], 'No sensors found');
        }

        try {
            $normalizedResponse = $this->normalizeResponse($sensorDTOs, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        if (!empty($errors)) {
            return $this->sendMultiStatusJsonResponse($errors, $normalizedResponse, 'Some issues were found with your request');
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
