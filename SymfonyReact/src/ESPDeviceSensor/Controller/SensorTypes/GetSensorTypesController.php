<?php

namespace App\ESPDeviceSensor\Controller\SensorTypes;

use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-types')]
class GetSensorTypesController extends AbstractController
{
    use HomeAppAPIResponseTrait;
    use ValidatorProcessorTrait;

    #[Route('/all-sensor-types', name: 'get-sensor-types', methods: [Request::METHOD_GET])]
    public function getAllSensorTypes(SensorTypeRepositoryInterface $sensorTypeRepository): Response
    {
        $sensorTypes = $sensorTypeRepository->findAll();

        try {
            $normalisedResponse = $this->normalizeResponse($sensorTypes);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['error preparing data']);
        }

        return $this->sendSuccessfulJsonResponse($normalisedResponse);
    }
}
