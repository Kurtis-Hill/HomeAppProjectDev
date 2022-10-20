<?php

namespace App\Sensors\Controller\ReadingTypeControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Sensors\Builders\ReadingTypeResponseBuilders\ReadingTypeResponseBuilder;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Repository\SensorReadingType\ReadingTypeRepositoryInterface;
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
                $readingTypeResponseDTO[] = ReadingTypeResponseBuilder::buildReadingTypeResponseDTO($readingTypeObject);
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
