<?php

namespace App\UserInterface\Controller\Card;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\User\Entity\User;
use App\UserInterface\Builders\NewCardOptionsDTOBuilder\NewCardOptionsBuilder;
use App\UserInterface\DTO\RequestDTO\NewCardRequestDTO;
use App\UserInterface\Services\Cards\CardCreation\CardCreationHandlerInterface;
use App\UserInterface\Voters\CardViewVoter;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'card/')]
class AddCardController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('add', name: 'add-card-form-v2', methods: [Request::METHOD_POST])]
    public function addCardForUser(Request $request, ValidatorInterface $validator, SensorRepositoryInterface $sensorRepository, CardCreationHandlerInterface $cardCreationHandler): JsonResponse
    {
        $newCardRequestDTO = new NewCardRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewCardRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newCardRequestDTO]
            );

        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $requestValidationErrors = $validator->validate($newCardRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $sensor = $sensorRepository->find($newCardRequestDTO->getSensorID());
        if (!$sensor) {
            return $this->sendBadRequestJsonResponse([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'sensor')]);
        }


        try {
            $this->denyAccessUnlessGranted(CardViewVoter::CAN_ADD_NEW_CARD, $sensor);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $cardOptionsDTO = NewCardOptionsBuilder::buildNewCardOptionsDTO(
            $newCardRequestDTO->getCardIcon(),
            $newCardRequestDTO->getCardColour(),
            $newCardRequestDTO->getCardState(),
        );

        try {
            $errors = $cardCreationHandler->createUserCardForSensor($sensor, $user, $cardOptionsDTO);
            if (!empty($errors)) {
                return $this->sendBadRequestJsonResponse($errors);
            }
        } catch (UniqueConstraintViolationException $e) {
            return $this->sendBadRequestJsonResponse(['You Already have a card view for this sensor']);
        }

        return $this->sendSuccessfulJsonResponse();
    }
}
