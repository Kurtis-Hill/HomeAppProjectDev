<?php

namespace App\UserInterface\Controller\Card;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\Exceptions\SensorTypeException;
use App\UserInterface\Builders\CardUpdateDTOBuilders\CardUpdateDTOBuilder;
use App\UserInterface\DTO\Internal\CardUpdateDTO\CardUpdateDTO;
use App\UserInterface\DTO\RequestDTO\CardViewRequestDTO;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Exceptions\CardFormTypeNotRecognisedException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use App\UserInterface\Factories\CardViewTypeFactories\CardViewFormDTOFactory;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepositoryInterface;
use App\UserInterface\Services\Cards\CardPreparation\CardViewFormPreparationServiceInterface;
use App\UserInterface\Services\Cards\CardViewUpdateService\CardViewUpdateServiceInterface;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card-form-data/')]
class CardViewFormController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('get/{id}', name: 'get-card-view-form-v2', methods: [Request::METHOD_GET])]
    public function getCardViewForm(
        CardView $cardViewObject,
        CardViewFormPreparationServiceInterface $cardViewFormPreparationService,
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
            | SensorTypeBuilderFailureException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['Query failure']);
        }

        try {
            $normalizedResponseData = $this->normalizeResponse($cardViewFormDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponseData);
    }

    #[Route('update/{id}', name: 'update-card-view-form-v2', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function updateCardView(
        CardView $cardViewObject,
        Request $request,
        CardViewRepositoryInterface $cardViewRepository,
        CardViewUpdateServiceInterface $cardViewUpdateService,
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

        $standardCardUpdateDTO = CardUpdateDTOBuilder::buildCardIDUpdateDTO(
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
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

        $cardViewResponseDTO = CardUpdateDTOBuilder::buildCardUpdateResponseDTO($cardViewObject);
        try {
            $normalizedResponseData = $this->normalizeResponse($cardViewResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], ['Request Successful']);
        }

        return $this->sendSuccessfulUpdateJsonResponse($normalizedResponseData);
    }
}
