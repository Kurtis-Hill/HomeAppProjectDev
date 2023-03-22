<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\SensorServices\GetSensorReadingTypeHandler;
use App\Sensors\Voters\SensorVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors')]
class GetSingleSensorsController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('/{sensorID}/get', name: 'get-single-sensor', methods: [Request::METHOD_GET])]
    public function getSingleSensor(Sensor $sensor, GetSensorReadingTypeHandler $getSensorReadingTypeHandler): JsonResponse
    {
        try {
            $this->denyAccessUnlessGranted(SensorVoter::GET_SINGLE_SENSOR, $sensor);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $sensorReadingTypeResponseDTOs = $getSensorReadingTypeHandler->handleSensorReadingTypeDTOCreating($sensor);
        } catch (ReadingTypeNotExpectedException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }
        if (empty($sensorReadingTypeResponseDTOs)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        try {
            $normalizedResponse = $this->normalizeResponse($sensorReadingTypeResponseDTOs);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
