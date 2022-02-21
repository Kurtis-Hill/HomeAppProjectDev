<?php

namespace App\Devices\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\DeviceServices\NewDevice\NewDeviceBuilderInterface;
use App\Devices\DeviceServices\NewDevice\NewESP8266DeviceBuilder;
use App\Devices\DTO\NewDeviceDTO;
use App\Devices\DTO\Request\NewDeviceRequestDTO;
use App\Devices\DTO\Response\NewDeviceSuccessResponseDTO;
use App\Devices\Voters\DeviceVoter;
use App\User\Entity\Room;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\User\Services\GroupServices\GroupCheck\GroupCheckServiceInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices', name: 'add-new-user-devices')]
class AddNewDeviceController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('/add-new-device', name: 'add-new-esp-device', methods: [Request::METHOD_POST])]
    public function addNewDevice(
        Request $request,
        RoomRepositoryInterface $roomRepository,
        NewDeviceBuilderInterface $newDeviceBuilder,
        GroupCheckServiceInterface $groupCheckService,
        DevicePasswordEncoderInterface $devicePasswordEncoder,
    ): JsonResponse {
        $newDeviceRequestDTO = new NewDeviceRequestDTO();

        $this->deserializeRequest(
            $request->getContent(),
            NewDeviceRequestDTO::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $newDeviceRequestDTO]
        );

        $requestValidationErrors = $newDeviceBuilder->validateDeviceRequestObject($newDeviceRequestDTO);
        if (!empty($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($requestValidationErrors);
        }

        try {
            $groupNameObject = $groupCheckService->checkForGroupById($newDeviceRequestDTO->getDeviceGroup());
        } catch (GroupNameNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
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
        $newDeviceCheckDTO = new NewDeviceDTO(
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
        $device = $newDeviceBuilder->createNewDevice($newDeviceCheckDTO);
        $errors = $newDeviceBuilder->validateNewDevice($device);

        if (!empty($errors)) {
            return $this->sendBadRequestJsonResponse($errors);
        }

        $devicePasswordEncoder->encodeDevicePassword($device);
        $deviceSaved = $newDeviceBuilder->saveNewDevice($device);
        if ($deviceSaved === false) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to save device']);
        }
        $secret = $device->getDeviceSecret();
        $deviceID = $device->getDeviceNameID();

        $newDeviceResponseDTO = new NewDeviceSuccessResponseDTO($secret, $deviceID);

        try {
            $response = $this->normalizeResponse($newDeviceResponseDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to normalize response']);
        }

        return $this->sendCreatedResourceJsonResponse($response);
    }
}
