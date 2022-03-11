<?php

namespace App\Devices\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\Devices\DeviceServices\UpdateDevice\UpdateDeviceObjectBuilderInterface;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\DTO\UpdateDeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use App\User\Entity\Room;
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

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices', name: 'update-user-devices')]
class UpdateDeviceController extends AbstractController
{
    use HomeAppAPITrait;

    #[
        Route(
            path: '/update-device/{deviceNameID}',
            name: 'update-esp-device',
            methods: [Request::METHOD_PUT]
        )
    ]
    public function updateDevice(
        Devices $deviceToUpdate,
        Request $request,
        UpdateDeviceObjectBuilderInterface $updateDeviceObjectBuilder,
        RoomRepositoryInterface $roomRepository,
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

        $requestValidationErrors = $updateDeviceObjectBuilder->validateDeviceRequestObject($deviceUpdateRequestDTO);

        if (!empty($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($requestValidationErrors);
        }

        if (!empty($deviceUpdateRequestDTO->getDeviceRoom())) {
            try {
                $room = $roomRepository->findOneById($deviceUpdateRequestDTO->getDeviceRoom());
            } catch (NonUniqueResultException | ORMException $e) {
                return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Room')]);
            }
            if (!$room instanceof Room) {
                $validationErrors[] = [sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Room')];
            }
        }
        $updateDeviceDTO = new UpdateDeviceDTO($deviceUpdateRequestDTO, $deviceToUpdate, $room ?? null);
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::UPDATE_DEVICE, $updateDeviceDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $deviceUpdateValidationErrors = $updateDeviceObjectBuilder->updateDeviceAndValidate($updateDeviceDTO);

        if (!empty($validationErrors)) {
            $deviceUpdateValidationErrors = array_merge($deviceUpdateValidationErrors, $validationErrors);
        }
        if (!empty($deviceUpdateValidationErrors)) {
            return $this->sendBadRequestJsonResponse($deviceUpdateValidationErrors);
        }

        $savedDevice = $updateDeviceObjectBuilder->saveNewDevice($deviceToUpdate);

        if ($savedDevice !== true) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Saving device')]);
        }

        $deviceUpdateSuccessResponseDTO = $updateDeviceObjectBuilder->buildSensorSuccessResponseDTO($deviceToUpdate);

        try {
            $normalizedResponse = $this->normalizeResponse($deviceUpdateSuccessResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([sprintf(APIErrorMessages::SERIALIZATION_FAILURE, 'device update success response DTO')]);
        }
        return $this->sendSuccessfulUpdateJsonResponse($normalizedResponse);
    }
}
