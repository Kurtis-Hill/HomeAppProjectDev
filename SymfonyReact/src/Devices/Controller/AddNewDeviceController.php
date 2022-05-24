<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Devices\Builders\DeviceUpdate\DeviceDTOBuilder;
use App\Devices\Builders\DeviceUpdate\DeviceUpdateResponseDTOBuilder;
use App\Devices\DeviceServices\DeleteDevice\DeleteDeviceServiceInterface;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\DeviceServices\NewDevice\NewDeviceServiceInterface;
use App\Devices\DTO\Request\NewDeviceRequestDTO;
use App\Devices\Voters\DeviceVoter;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices', name: 'add-new-user-devices')]
class AddNewDeviceController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('/add-new-device', name: 'add-new-esp-device', methods: [Request::METHOD_POST])]
    public function addNewDevice(
        Request $request,
        ValidatorInterface $validator,
        RoomRepositoryInterface $roomRepository,
        NewDeviceServiceInterface $newDeviceBuilder,
        GroupNameRepositoryInterface $groupNameRepository,
        DevicePasswordEncoderInterface $devicePasswordEncoder,
        DeleteDeviceServiceInterface $deleteDeviceHandler,
    ): JsonResponse {
        $newDeviceRequestDTO = new NewDeviceRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                NewDeviceRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newDeviceRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $requestValidationErrors = $validator->validate($newDeviceRequestDTO);
        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        $groupNameObject = $groupNameRepository->findOneById($newDeviceRequestDTO->getDeviceGroup());
        if (!$groupNameObject instanceof GroupNames) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'Groupname'
                ),
            ]);
        }

        $roomObject = $roomRepository->findOneById($newDeviceRequestDTO->getDeviceRoom());
        if (!$roomObject instanceof Room) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'Room'
                ),
            ]);
        }
        $newDeviceCheckDTO = DeviceDTOBuilder::buildNewDeviceDTO(
            $this->getUser(),
            $groupNameObject,
            $roomObject,
            $newDeviceRequestDTO->getDeviceName(),
        );

        try {
            $this->denyAccessUnlessGranted(DeviceVoter::ADD_NEW_DEVICE, $newDeviceCheckDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $errors = $newDeviceBuilder->processNewDevice($newDeviceCheckDTO);
        if (!empty($errors)) {
            return $this->sendBadRequestJsonResponse($errors);
        }

        $device = $newDeviceCheckDTO->getNewDevice();
        $devicePasswordEncoder->encodeDevicePassword($device);
        $deviceSaved = $newDeviceBuilder->saveNewDevice($device);
        if ($deviceSaved === false) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to save device']);
        }

        $newDeviceResponseDTO = DeviceUpdateResponseDTOBuilder::buildDeviceResponseDTO($device, true);
        try {
            $response = $this->normalizeResponse($newDeviceResponseDTO);
        } catch (ExceptionInterface) {
            $deleteDeviceHandler->deleteDevice($device);

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendCreatedResourceJsonResponse($response);
    }
}
