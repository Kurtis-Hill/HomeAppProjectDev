<?php

namespace App\Sensors\Controller\TriggerControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\Trigger\CreateNewTriggerDTOBuilder;
use App\Sensors\Builders\TriggerResponseBuilder\SensorTriggerResponseDTOBuilder;
use App\Sensors\DTO\Request\Trigger\NewTriggerRequestDTO;
use App\Sensors\Exceptions\BaseReadingTypeNotFoundException;
use App\Sensors\Exceptions\OperatorNotFoundException;
use App\Sensors\Exceptions\TriggerTypeNotFoundException;
use App\Sensors\SensorServices\Trigger\TriggerCreationHandler\TriggerCreationHandlerInterface;
use App\Sensors\SensorServices\Trigger\TriggerHelpers\TriggerDateTimeConvertor;
use App\Sensors\Voters\SensorVoter;
use App\User\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-trigger/form')]
class AddSensorTriggerController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('/add', name: 'create-sensor-trigger', methods: [Request::METHOD_POST])]
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
                TriggerDateTimeConvertor::prepareTimesForComparison($newTriggerRequestDTO->getStartTime()),
                $newTriggerRequestDTO->getEndTime(),
                $user,
                $newTriggerRequestDTO->getBaseReadingTypeThatTriggers(),
                $newTriggerRequestDTO->getBaseReadingTypeThatIsTriggered(),
            );
        } catch (OperatorNotFoundException | TriggerTypeNotFoundException | BaseReadingTypeNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }
//dd($createNewTriggerDTO);
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
            $normalizedResponse = $this->normalizeResponse($responseDTO);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::PROCESS_SUCCESS_COULD_NOT_CREATE_RESPONSE]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
