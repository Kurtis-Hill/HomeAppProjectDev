<?php

namespace App\Controller\Sensor\SensorTypeControllers;

use App\Builders\Sensor\Response\SensorTypeDTOBuilders\SensorTypeResponseDTOBuilder;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Repository\Sensor\Sensors\SensorTypeRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-types')]
class GetSensorTypesController extends AbstractController
{
    use HomeAppAPITrait;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('/all', name: 'get-sensor-types', methods: [Request::METHOD_GET])]
    public function getAllSensorTypes(Request $request, SensorTypeRepositoryInterface $sensorTypeRepository, SensorTypeResponseDTOBuilder $responseDTOBuilder): Response
    {
        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }
        $sensorTypes = $sensorTypeRepository->findAllSensorTypes();
        foreach ($sensorTypes as $sensorType) {
            $sensorTypeResponseDTO[] = $responseDTOBuilder->buildSensorTypeResponseDTO($sensorType);
        }

        if (empty($sensorTypeResponseDTO)) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Sensor types')]);
        }

        try {
            $normalisedResponse = $this->normalize($sensorTypeResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['error preparing data']);
        }

        return $this->sendSuccessfulJsonResponse($normalisedResponse);
    }
}
