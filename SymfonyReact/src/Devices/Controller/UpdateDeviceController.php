<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\DeviceUpdate\DeviceDTOBuilder;
use App\Devices\Builders\DeviceUpdate\DeviceUpdateResponseDTOBuilder;
use App\Devices\DeviceServices\UpdateDevice\UpdateDeviceServiceInterface;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices', name: 'update-user-devices')]
class UpdateDeviceController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[
        Route(
            path: '/update-device/{deviceNameID}',
            name: 'update-esp-device',
            methods: [Request::METHOD_PUT, Request::METHOD_PATCH]
        )
    ]
    public function updateDevice(
        Devices $deviceToUpdate,
        Request $request,
        ValidatorInterface $validator,
        UpdateDeviceServiceInterface $updateDeviceObjectBuilder,
        RoomRepositoryInterface $roomRepository,
        GroupNameRepositoryInterface $groupNameRepository
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
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $requestValidationErrors = $validator->validate($deviceUpdateRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        if (!empty($deviceUpdateRequestDTO->getDeviceRoom())) {
            try {
                $room = $roomRepository->findOneById($deviceUpdateRequestDTO->getDeviceRoom());
            } catch (NonUniqueResultException | ORMException) {
                return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Room')]);
            }
            if (!$room instanceof Room) {
                return $this->sendBadRequestJsonResponse(['The id provided for room doesnt match any room we have'], 'Room not found');
            }
        }
        if (!empty($deviceUpdateRequestDTO->getDeviceGroup())) {
            try {
                $groupName = $groupNameRepository->findOneById($deviceUpdateRequestDTO->getDeviceGroup());
            } catch (NonUniqueResultException | ORMException) {
                return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Group name')]);
            }
            if (!$groupName instanceof GroupNames) {
                return $this->sendBadRequestJsonResponse(['The id provided for groupname doesnt match any groupname we have'], 'Group name not found');
            }
        }

        $updateDeviceDTO = DeviceDTOBuilder::buildUpdateDeviceInternalDTO(
            $deviceUpdateRequestDTO,
            $deviceToUpdate,
            $room ?? null,
            $groupName ?? null
        );
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::UPDATE_DEVICE, $updateDeviceDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deviceUpdateValidationErrors = $updateDeviceObjectBuilder->updateDevice($updateDeviceDTO);
        if (!empty($deviceUpdateValidationErrors)) {
            return $this->sendBadRequestJsonResponse($deviceUpdateValidationErrors, APIErrorMessages::VALIDATION_ERRORS);
        }

        $savedDevice = $updateDeviceObjectBuilder->saveNewDevice($deviceToUpdate);
        if ($savedDevice !== true) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Saving device')]);
        }

        $deviceUpdateSuccessResponseDTO = DeviceUpdateResponseDTOBuilder::buildUpdateDeviceDTO($deviceToUpdate);
        try {
            $normalizedResponse = $this->normalizeResponse($deviceUpdateSuccessResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([sprintf(APIErrorMessages::SERIALIZATION_FAILURE, 'device update success response DTO')]);
        }

        return $this->sendSuccessfulUpdateJsonResponse($normalizedResponse, 'Device Successfully Updated');
    }
}
