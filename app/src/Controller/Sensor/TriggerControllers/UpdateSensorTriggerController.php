<?php

namespace App\Controller\Sensor\TriggerControllers;

use App\Builders\Sensor\Internal\Trigger\UpdateTriggerDTOBuilder;
use App\Builders\Sensor\Response\TriggerResponseBuilder\SensorTriggerResponseDTOBuilder;
use App\DTOs\Sensor\Request\Trigger\SensorTriggerUpdateRequestDTO;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\User\User;
use App\Repository\Sensor\SensorTriggerRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Sensor\Trigger\UpdateTrigger\UpdateTriggerHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\SensorVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-trigger/')]
class UpdateSensorTriggerController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
    ) {
        $this->logger = $logger;
    }

    #[
        Route(
            path: '{sensorTrigger}',
            name: 'update-esp-device',
            methods: [Request::METHOD_PUT, Request::METHOD_PATCH]
        )
    ]
    public function updateSensorTrigger(
        Request $request,
        SensorTrigger $sensorTrigger,
        ValidatorInterface $validator,
        UpdateTriggerDTOBuilder $updateTriggerDTOBuilder,
        UpdateTriggerHandler $updateTriggerHandler,
        SensorTriggerResponseDTOBuilder $sensorTriggerResponseDTOBuilder,
        SensorTriggerRepository $sensorTriggerRepository,
    ): JsonResponse {
        $sensorTriggerUpdateRequestDTO = new SensorTriggerUpdateRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                SensorTriggerUpdateRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $sensorTriggerUpdateRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([], APIErrorMessages::FORMAT_NOT_SUPPORTED);
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $grantedAccess = $this->isGranted(SensorVoter::CAN_UPDATE_TRIGGER, $sensorTrigger);
        if ($grantedAccess !== true) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }
        $requestValidationErrors = $validator->validate($sensorTriggerUpdateRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        $updateTriggerInternalDTO = $updateTriggerDTOBuilder->buildTriggerUpdateDTO($sensorTriggerUpdateRequestDTO);

        $processErrors = $updateTriggerHandler->handleUpdateOfTrigger($sensorTrigger, $updateTriggerInternalDTO);
        if (!empty($processErrors)) {
            return $this->sendBadRequestJsonResponse($processErrors);
        }

        $sensorTriggerRepository->flush();

        $this->logger->info(
            sprintf(
                'Sensor Trigger with id %d updated by user with id %d',
                $sensorTrigger->getSensorTriggerID(),
                $user->getUserID(),
            )
        );

        try {
            $sensorTriggerUpdateResponseDTO = $sensorTriggerResponseDTOBuilder->buildFullSensorTriggerResponseDTO($sensorTrigger);
            $normalizedResponse = $this->normalize($sensorTriggerUpdateResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
