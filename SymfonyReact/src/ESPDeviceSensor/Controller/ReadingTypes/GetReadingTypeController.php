<?php

namespace App\ESPDeviceSensor\Controller\ReadingTypes;

use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\ESPDeviceSensor\DTO\Response\ReadingTypes\ReadingTypeResponseDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

//@TODO FINISH THIS CONTROLLER
#[Route(CommonURL::USER_HOMEAPP_API_URL . 'reading-types/', name: 'reading_types')]
class GetReadingTypeController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('all', name: 'all-reading-types')]
    public function getReadingTypes(): JsonResponse
    {
        foreach (ReadingTypes::SENSOR_READING_TYPE_DATA as $readingTypeName =>$readingType) {
            $readingTypes[] = new ReadingTypeResponseDTO(
                1,
                $readingTypeName
            );
        }
        $normaliser = [new ObjectNormalizer()];
        $serializer = new Serializer($normaliser);

        try {
            $normalizedReadingTypesDTOs = $serializer->normalize($readingTypes ?? []);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to format response']);
        }

        return $this->sendSuccessfulJsonResponse($normalizedReadingTypesDTOs);
    }
}
