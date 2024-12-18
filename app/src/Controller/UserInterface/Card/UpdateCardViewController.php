<?php

namespace App\Controller\UserInterface\Card;

use App\Builders\UserInterface\CardUpdateDTOBuilders\CardResponseDTOBuilder;
use App\DTOs\UserInterface\RequestDTO\CardViewRequestDTO;
use App\Entity\UserInterface\Card\CardView;
use App\Repository\UserInterface\ORM\CardRepositories\CardViewRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\UserInterface\Cards\CardViewUpdate\CardViewUpdateFacade;
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
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card/')]
class UpdateCardViewController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('{id}', name: 'update-card-view-form-v2', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function updateCardView(
        CardView $cardViewObject,
        Request $request,
        CardViewRepositoryInterface $cardViewRepository,
        CardViewUpdateFacade $cardViewUpdateService,
        ValidatorInterface $validator,
    ): JsonResponse {
        $cardViewRequestDTO = new CardViewRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                CardViewRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $cardViewRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $validationErrors = $validator->validate($cardViewRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        try {
            $this->denyAccessUnlessGranted(CardViewVoter::CAN_EDIT_CARD_VIEW_FORM, $cardViewObject);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $standardCardUpdateDTO = CardResponseDTOBuilder::buildCardIDUpdateDTO(
            $cardViewRequestDTO->getCardColour(),
            $cardViewRequestDTO->getCardIcon(),
            $cardViewRequestDTO->getCardViewState(),
        );

        $validationErrors = $cardViewUpdateService->updateAllCardViewObjectProperties($standardCardUpdateDTO, $cardViewObject);
        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse($validationErrors);
        }

        try {
            $cardViewRepository->persist($cardViewObject);
            $cardViewRepository->flush();

            $this->logger->info('Card view form updated successfully for id: ' . $cardViewObject->getCardViewID() . ' time: ' . date('d-m-Y-H-i-s'));
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

        $cardViewResponseDTO = CardResponseDTOBuilder::buildCardResponseDTO($cardViewObject);
        try {
            $normalizedResponseData = $this->normalize($cardViewResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], 'Request Successful');
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponseData);
    }
}
