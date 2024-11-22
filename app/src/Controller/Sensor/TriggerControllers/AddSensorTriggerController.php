<?php

namespace App\Controller\Sensor\TriggerControllers;

use App\Builders\Sensor\Internal\Trigger\CreateNewTriggerDTOBuilder;
use App\Builders\Sensor\Response\TriggerResponseBuilder\SensorTriggerResponseDTOBuilder;
use App\DTOs\Sensor\Request\Trigger\NewTriggerRequestDTO;
use App\Entity\User\User;
use App\Exceptions\Sensor\BaseReadingTypeNotFoundException;
use App\Exceptions\Sensor\OperatorNotFoundException;
use App\Exceptions\Sensor\TriggerTypeNotFoundException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Sensor\Trigger\TriggerCreationHandler\TriggerCreationHandlerInterface;
use App\Services\Sensor\Trigger\TriggerHelpers\TriggerDateTimeConvertor;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\SensorVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-trigger')]
class AddSensorTriggerController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('', name: 'create-sensor-trigger', methods: [Request::METHOD_POST])]
    public function handleSensorTriggerFormSubmission(
        Request $request,
        ValidatorInterface $validator,
        CreateNewTriggerDTOBuilder $createNewTriggerDTOBuilder,
        TriggerCreationHandlerInterface $triggerCreationHandler,
        SensorTriggerResponseDTOBuilder $sensorTriggerResponseDTOBuilder,
    ): JsonResponse {
        $newTriggerRequestDTO = new NewTriggerRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewTriggerRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newTriggerRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $requestValidationErrors = $validator->validate($newTriggerRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $createNewTriggerDTO = $createNewTriggerDTOBuilder->buildCreateNewTriggerDTOFromValues(
                $newTriggerRequestDTO->getOperator(),
                $newTriggerRequestDTO->getTriggerType(),
                $newTriggerRequestDTO->getValueThatTriggers(),
                $newTriggerRequestDTO->getDays(),
                $user,
                $newTriggerRequestDTO->getStartTime() !== null ? TriggerDateTimeConvertor::prepareTimes($newTriggerRequestDTO->getStartTime()) : null,
                $newTriggerRequestDTO->getEndTime() !== null ? TriggerDateTimeConvertor::prepareTimes($newTriggerRequestDTO->getEndTime()) : null,
                $newTriggerRequestDTO->getBaseReadingTypeThatTriggers(),
                $newTriggerRequestDTO->getBaseReadingTypeThatIsTriggered(),
            );
        } catch (OperatorNotFoundException | TriggerTypeNotFoundException | BaseReadingTypeNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }

        try {
            $this->denyAccessUnlessGranted(
                SensorVoter::CAN_CREATE_TRIGGER,
                $createNewTriggerDTO,
            );
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $errors = $triggerCreationHandler->createTrigger($createNewTriggerDTO);
        if (!empty($errors)) {
            return $this->sendBadRequestJsonResponse($errors);
        }

        $responseDTO = $sensorTriggerResponseDTOBuilder->buildFullSensorTriggerResponseDTO($createNewTriggerDTO->getNewSensorTrigger());
        try {
            $normalizedResponse = $this->normalize($responseDTO);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::PROCESS_SUCCESS_COULD_NOT_CREATE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
