<?php

namespace App\Controller\UserInterface\Card;

use App\Entity\UserInterface\Card\CardView;
use App\Exceptions\Sensor\SensorTypeException;
use App\Exceptions\UserInterface\CardFormTypeNotRecognisedException;
use App\Exceptions\UserInterface\SensorTypeBuilderFailureException;
use App\Factories\UserInterface\CardViewTypeFactories\CardViewFormDTOFactory;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\UserInterface\Cards\CardPreparation\CardViewFormPreparationHandlerInterface;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\CardViewVoter;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card-form/')]
class GetCardViewFormController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('{id}', name: 'get-card-view-form-v2', methods: [Request::METHOD_GET])]
    public function getCardViewForm(
        CardView $cardViewObject,
        CardViewFormPreparationHandlerInterface $cardViewFormPreparationService,
    ): JsonResponse {
        try {
            $this->denyAccessUnlessGranted(CardViewVoter::CAN_VIEW_CARD_VIEW_FORM, $cardViewObject);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $cardViewFormDTO = $cardViewFormPreparationService->createCardViewFormDTO(
                $cardViewObject,
                CardViewFormDTOFactory::SENSOR_TYPE_READING_FORM_CARD
            );
        } catch (
            SensorTypeException
            | CardFormTypeNotRecognisedException
            | SensorTypeBuilderFailureException $e
        ) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        } catch (ORMException $e) {
            $this->logger->error('Query failure for card view form id: ' . $cardViewObject->getCardViewID());

            return $this->sendInternalServerErrorJsonResponse(['Query failure   ']);
        }

        try {
            $normalizedResponseData = $this->normalize($cardViewFormDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponseData);
    }
}
