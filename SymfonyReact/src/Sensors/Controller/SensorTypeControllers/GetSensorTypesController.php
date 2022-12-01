<?php

namespace App\Sensors\Controller\SensorTypeControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Sensors\Builders\SensorTypeDTOBuilders\SensorTypeResponseDTOBuilder;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-types')]
class GetSensorTypesController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('/all', name: 'get-sensor-types', methods: [Request::METHOD_GET])]
    public function getAllSensorTypes(SensorTypeRepositoryInterface $sensorTypeRepository, SensorTypeResponseDTOBuilder $responseDTOBuilder): Response
    {
        $sensorTypes = $sensorTypeRepository->findAllSensorTypes();

        foreach ($sensorTypes as $sensorType) {
            $sensorTypeResponseDTO[] = $responseDTOBuilder->buildSensorTypeResponseDTO($sensorType);
        }

        if (empty($sensorTypeResponseDTO)) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Sensor types')]);
        }

        try {
            $normalisedResponse = $this->normalizeResponse($sensorTypeResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['error preparing data']);
        }

        return $this->sendSuccessfulJsonResponse($normalisedResponse);
    }
}
