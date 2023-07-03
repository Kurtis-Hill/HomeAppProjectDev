<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\CommonURL;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\Factories\SensorReadingType\SensorReadingUpdateFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(CommonURL::HOMEAPP_WEBAPP_URL_BASE . 'sensor-reading-type')]
class SwitchSensorController extends AbstractController
{
    #[Route('/switch-sensor', name: 'switch-sensor', methods: [Request::METHOD_POST])]
    public function switchSensorAction(Request $request, SensorReadingUpdateFactory $sensorReadingUpdateFactory): JsonResponse
    {

//        try {
//            $boolCurrentReadingUpdateRequestDTO new BoolCurrentReadingUpdateRequestDTO()
//        }
        //
    }
}
