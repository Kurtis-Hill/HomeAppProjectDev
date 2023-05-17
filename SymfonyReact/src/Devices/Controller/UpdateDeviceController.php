<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Builders\Request\RequestDTOBuilder;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Devices\DeviceServices\UpdateDevice\UpdateDeviceHandlerInterface;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use App\User\Entity\User;
use App\User\Exceptions\GroupExceptions\GroupNotFoundException;
use App\User\Exceptions\RoomsExceptions\RoomNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices/', name: 'update-user-devices')]
class UpdateDeviceController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[
        Route(
            path: '{deviceID}/update',
            name: 'update-esp-device',
            methods: [Request::METHOD_PUT, Request::METHOD_PATCH]
        )
    ]
    public function updateDevice(
        Devices $deviceToUpdate,
        Request $request,
        ValidatorInterface $validator,
        UpdateDeviceHandlerInterface $updateDeviceHandler,
        DeviceResponseDTOBuilder $deviceResponseDTOBuilder,
    ): JsonResponse {
        $deviceUpdateRequestDTO = new DeviceUpdateRequestDTO();

        try {
            $this->deserializeRequest(
                $request->getContent(),
                DeviceUpdateRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $deviceUpdateRequestDTO],
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([], APIErrorMessages::FORMAT_NOT_SUPPORTED);
        }

        $requestValidationErrors = $validator->validate($deviceUpdateRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse(
                $this->getValidationErrorAsArray($requestValidationErrors),
                APIErrorMessages::VALIDATION_ERRORS
            );
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get('responseType', RequestTypeEnum::SENSITIVE_FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'User')]);
        }

        try {
            $updateDeviceDTO = $updateDeviceHandler->buildUpdateDeviceDTO(
                $deviceUpdateRequestDTO,
                $user,
                $deviceToUpdate,
            );
        } catch (NonUniqueResultException | ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Room or group name')]);
        } catch (GroupNotFoundException|RoomNotFoundException $e) {
            return $this->sendNotFoundResponse([$e->getMessage()]);
        }

        try {
            $this->denyAccessUnlessGranted(DeviceVoter::UPDATE_DEVICE, $updateDeviceDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deviceUpdateValidationErrors = $updateDeviceHandler->updateDevice($updateDeviceDTO);
        if (!empty($deviceUpdateValidationErrors)) {
            return $this->sendBadRequestJsonResponse($deviceUpdateValidationErrors, APIErrorMessages::VALIDATION_ERRORS);
        }

        $savedDevice = $updateDeviceHandler->saveDevice($deviceToUpdate);
        if ($savedDevice !== true) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Saving device')]);
        }

        $deviceUpdateSuccessResponseDTO = $deviceResponseDTOBuilder->buildDeviceResponseDTOWithDevicePermissions($deviceToUpdate);
        try {
            $normalizedResponse = $this->normalizeResponse($deviceUpdateSuccessResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([sprintf(APIErrorMessages::SERIALIZATION_FAILURE, 'device update success response DTO')]);
        }

        $this->logger->info(
            sprintf(
                'Device %s updated successfully',
                $deviceToUpdate->getDeviceID()
            ),
            [
                'user' => $this->getUser()?->getUserIdentifier()
            ]
        );

        return $this->sendSuccessfulUpdateJsonResponse($normalizedResponse, 'Device Successfully Updated');
    }
}
