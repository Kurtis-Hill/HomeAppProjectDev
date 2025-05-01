<?php

namespace App\Controller\Device;

use App\Builders\Device\DeviceResponse\DeviceResponseDTOBuilder;
use App\Builders\Device\DeviceUpdate\DeviceDTOBuilder;
use App\DTOs\Device\Request\DeviceUpdateRequestDTO;
use App\DTOs\RequestDTO;
use App\Entity\Device\Devices;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Device\UpdateDevice\UpdateDeviceHandlerInterface;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\DeviceVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
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
            path: '{deviceID}',
            name: 'update-esp-device',
            methods: [Request::METHOD_PUT, Request::METHOD_PATCH]
        )
    ]
    public function updateDevice(
        Devices $deviceToUpdate,
        UpdateDeviceHandlerInterface $updateDeviceHandler,
        DeviceResponseDTOBuilder $deviceResponseDTOBuilder,
        DeviceDTOBuilder $deviceDTOBuilder,
        #[MapRequestPayload]
        DeviceUpdateRequestDTO $deviceUpdateRequestDTO,
        #[MapQueryString]
        ?RequestDTO $requestDTO = null,
    ): JsonResponse {
        $requestDTO ??= new RequestDTO();

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'User')]);
        }

        try {
            $updateDeviceDTO = $deviceDTOBuilder->buildUpdateDeviceInternalDTO(
                $deviceUpdateRequestDTO,
                $deviceToUpdate,
            );
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

        $sendUpdateRequestToDevice = ($updateDeviceDTO->getDeviceUpdateRequestDTO()->getDeviceName() || $updateDeviceDTO->getDeviceUpdateRequestDTO()->getPassword());
        $savedDevice = $updateDeviceHandler->saveDevice($deviceToUpdate, $sendUpdateRequestToDevice);
        if ($savedDevice !== true) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Saving device')]);
        }

        $deviceUpdateSuccessResponseDTO = $deviceResponseDTOBuilder->buildDeviceResponseDTOWithDevicePermissions($deviceToUpdate);
        try {
            $normalizedResponse = $this->normalize($deviceUpdateSuccessResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::SERIALIZATION_FAILURE, 'device update success response DTO')]);
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

        return $this->sendSuccessfulJsonResponse($normalizedResponse, 'Device Successfully Updated');
    }
}
