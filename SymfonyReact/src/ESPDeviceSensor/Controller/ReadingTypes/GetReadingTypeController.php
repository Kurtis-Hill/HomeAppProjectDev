<?php

namespace App\ESPDeviceSensor\Controller\ReadingTypes;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\ESPDeviceSensor\DTO\Response\ReadingTypes\ReadingTypeResponseDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\ReadingTypes;
use App\ESPDeviceSensor\Repository\ORM\SensorReadingType\ReadingTypeRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'reading-types/', name: 'reading_types')]
class GetReadingTypeController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('all', name: 'all-reading-types')]
    public function getReadingTypes(ReadingTypeRepositoryInterface $readingTypeRepository): JsonResponse
    {
        $allReadingTypes = $readingTypeRepository->findAll();

        foreach ($allReadingTypes as $readingTypeObject) {
            if ($readingTypeObject instanceof ReadingTypes) {
                $readingTypeResponseDTO[] = new ReadingTypeResponseDTO(
                    $readingTypeObject->getReadingTypeID(),
                    $readingTypeObject->getReadingType(),
                );
            }
        }

        if (empty($readingTypeResponseDTO)) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Reading types')]);
        }

        try {
            $normalizedReadingTypesDTOs = $this->normalizeResponse($readingTypeResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedReadingTypesDTOs);
    }
}
