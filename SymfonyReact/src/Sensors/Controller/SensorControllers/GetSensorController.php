<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\DTO\Request\GetSensorRequestDTO\GetSensorRequestDTO;
use App\Sensors\SensorServices\GetSensorReadingTypeHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors')]
class GetSensorController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('/all', name: 'get-all-sensors', methods: [Request::METHOD_GET])]
    public function getAllSensors(Request $request, GetSensorReadingTypeHandler $getSensorReadingTypeHandler): JsonResponse
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

        dd($sensorRequestDTO->getOffset());
    }
}
