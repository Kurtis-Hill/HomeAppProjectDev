<?php

namespace App\ESPDeviceSensor\Controller\SensorTypes;

use App\API\Traits\HomeAppAPIResponseTrait;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Repository\ORM\SensorType\SensorTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/HomeApp/api/sensors')]
class GetSensorTypesController extends AbstractController
{
    use HomeAppAPIResponseTrait;


    #[Route('/all-types', name: 'get-sensor-types', methods: [Request::METHOD_GET])]
    public function getAllSensorTypes(SensorTypeRepositoryInterface $sensorTypeRepository): Response
    {
        $sensorTypes = $sensorTypeRepository->findAll();

        $normaliser = [new ObjectNormalizer()];
        $serializer = new Serializer($normaliser);

        try {
            $normalisedResponse = $serializer->normalize($sensorTypes);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['error preparing data']);
        }

        return $this->sendSuccessfulJsonResponse($normalisedResponse);
    }
}
