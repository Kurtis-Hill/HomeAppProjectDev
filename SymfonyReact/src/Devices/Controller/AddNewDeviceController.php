<?php

namespace App\Devices\Controller;

use App\Devices\DeviceServices\NewDevice\NewDeviceServiceInterface;
use App\Devices\DTO\DeviceDTO;
use App\Devices\DTO\NewDeviceCheckDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Voters\DeviceVoter;
use App\Form\FormMessages;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\User\Entity\Room;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
use App\User\Repository\ORM\RoomRepositoryInterface;
use App\User\Services\GroupServices\GroupCheck\GroupCheckServiceInterface;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/HomeApp/api/user-devices', name: 'user-devices')]
class AddNewDeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('/add-new-device', name: 'add-new-esp-device', methods: [Request::METHOD_POST])]
    public function addNewDevice(
        Request $request,
        RoomRepositoryInterface $roomRepository,
        NewDeviceServiceInterface $newDeviceService,
        GroupCheckServiceInterface $groupCheckService,
    ): JsonResponse {
        try {
            $newDeviceData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['Request not formatted correctly']);
        }
        $deviceName = $newDeviceData['deviceName'] ?? null;
        $deviceGroup = $newDeviceData['deviceGroup'] ?? null;
        $deviceRoom = $newDeviceData['deviceRoom'] ?? null;

        if (!is_int($deviceGroup)) {
            $errorMessage = sprintf(FormMessages::MALFORMED_REQUEST_DATA, 'group', 'int');
            return $this->sendBadRequestJsonResponse([$errorMessage]);
        }
        if (!is_int($deviceRoom)) {
            $errorMessage = sprintf(FormMessages::MALFORMED_REQUEST_DATA, 'room', 'int');
            return $this->sendBadRequestJsonResponse([$errorMessage]);
        }

        try {
            $groupNameObject = $groupCheckService->checkForGroupById($deviceGroup);
        } catch (GroupNameNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }

        $roomObject = $roomRepository->findOneById($deviceRoom);

        if (!$roomObject instanceof Room) {
            return $this->sendBadRequestJsonResponse(['Room not found']);
        }

        $newDeviceCheckDTO = new NewDeviceCheckDTO(
            $groupNameObject,
            $roomObject,
        );
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::ADD_NEW_DEVICE, $newDeviceCheckDTO);
        } catch (AccessDeniedException) {
            return $this->sendBadRequestJsonResponse([FormMessages::ACCESS_DENIED]);
        }

        $deviceData = new DeviceDTO(
            $this->getUser(),
            $groupNameObject,
            $roomObject,
            $deviceName,
        );
        $device = $newDeviceService->createNewDevice($deviceData);

        $errors = $newDeviceService->validateNewDevice($device);

        if (!empty($errors)) {
            return $this->sendBadRequestJsonResponse($errors);
        }

        $deviceSaved = $newDeviceService->encodeAndSaveNewDevice($device);

        if ($deviceSaved === false) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to save device']);
        }
        $secret = $device->getDeviceSecret();
        $deviceID = $device->getDeviceNameID();

        return $this->sendCreatedResourceJsonResponse(['secret' => $secret, 'deviceID' => $deviceID]);
    }
}
