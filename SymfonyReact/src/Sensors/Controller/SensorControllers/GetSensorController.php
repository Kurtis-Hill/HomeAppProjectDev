<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Builders\Request\RequestDTOBuilder;
use App\Common\Services\PaginationCalculator;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Request\GetSensorRequestDTO\GetSensorRequestDTO;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\GetSensorReadingTypeHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors')]
class GetSensorController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('/all', name: 'get-all-sensors', methods: [Request::METHOD_GET])]
    public function getAllSensors(Request $request, ValidatorInterface $validator, GetSensorReadingTypeHandler $getSensorReadingTypeHandler, SensorRepositoryInterface $sensorRepository): JsonResponse
    {
        $sensorRequestDTO = new GetSensorRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getQueryString(),
                GetSensorRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $sensorRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

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

        $sensors = $sensorRepository->findSensorsByQueryParameters($getSensorQueryDTO);

        $sensorDTOs = [];
        if ($sensorRequestDTO->getResponseType() === RequestDTOBuilder::REQUEST_TYPE_ONLY) {
            foreach ($sensors as $sensor) {
                $sensorDTOs[] = SensorResponseDTOBuilder::buildSensorResponseDTO($sensor);
            }
        } elseif ($sensorRequestDTO->getResponseType() === RequestDTOBuilder::REQUEST_TYPE_FULL) {
            foreach ($sensors as $sensor) {
                $sensorDTOs[] = $getSensorReadingTypeHandler->handleSensorReadingTypeDTOCreating($sensor);
            }
        }

        if (empty($sensorDTOs)) {
            return $this->sendSuccessfulJsonResponse([], 'No sensors found');
        }

        try {
            $normalizedResponse = $this->normalizeResponse($sensorDTOs);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
