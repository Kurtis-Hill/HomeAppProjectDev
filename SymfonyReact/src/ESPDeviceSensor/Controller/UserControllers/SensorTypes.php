<?php

namespace App\ESPDeviceSensor\Controller\UserControllers;

use App\ESPDeviceSensor\Entity\SensorType;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/HomeApp/api/sensors')]
class SensorTypes extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @throws ExceptionInterface
     */
    #[Route('/all-types', name: 'get-sensor-types', methods: [Request::METHOD_GET])]
    public function returnAllSensorTypes(): Response
    {
        $sensorTypes = $this->getDoctrine()->getManager()->getRepository(SensorType::class)->findAll();

        $normaliser = [new ObjectNormalizer()];
        $serializer = new Serializer($normaliser);

        try {
            $normalisedResponse = $serializer->normalize($sensorTypes);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['error preparing data']);
        }
//        dd($sensorTypes);

        return $this->sendSuccessfulJsonResponse($normalisedResponse);
    }
}
