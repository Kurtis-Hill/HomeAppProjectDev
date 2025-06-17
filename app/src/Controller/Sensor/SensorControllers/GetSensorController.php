<?php

namespace App\Controller\Sensor\SensorControllers;

use App\Builders\Sensor\Request\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\DTOs\RequestDTO;
use App\DTOs\Sensor\Internal\Sensor\GetSensorQueryDTO;
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

    #[Route('', name: 'get-all-sensors', methods: [Request::METHOD_GET])]
    public function getAllSensors(
        SensorResponseDTOBuilder $sensorResponseDTOBuilder,
        SensorUserFilter $sensorUserFilter,
        SensorRepositoryInterface $sensorRepository,
        #[MapQueryString]
        GetSensorQueryDTO $getSensorQueryDTO,
        #[MapQueryString]
        ?RequestDTO $requestDTO = null,
    ): JsonResponse {
        $requestDTO ??= new RequestDTO();

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
        }
        $sensors = $sensorRepository->findSensorsByQueryParameters($getSensorQueryDTO, $requestDTO);
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
