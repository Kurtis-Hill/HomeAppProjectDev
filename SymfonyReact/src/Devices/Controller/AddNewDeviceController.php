<?php

namespace App\Devices\Controller;

use App\API\APIErrorMessages;
use App\Devices\DeviceServices\NewDevice\NewESP8266DeviceValidatorService;
use App\Devices\DTO\DeviceDTO;
use App\Devices\DTO\NewDeviceDTO;
use App\Devices\Voters\DeviceVoter;
use App\Form\FormMessages;
use App\Traits\API\HomeAppAPIResponseTrait;
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

#[Route('/HomeApp/api/user-devices', name: 'user-devices')]
class AddNewDeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('/add-new-device', name: 'add-new-esp-device', methods: [Request::METHOD_POST])]
    public function addNewDevice(
        Request $request,
        RoomRepositoryInterface $roomRepository,
        NewESP8266DeviceValidatorService $newDeviceService,
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

        if (!is_numeric($deviceGroup) || !is_numeric($deviceRoom)) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }

        try {
            $groupNameObject = $groupCheckService->checkForGroupById($deviceGroup);
        } catch (GroupNameNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }

        $roomObject = $roomRepository->findOneById($deviceRoom);

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
            $deviceName,
        );
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::ADD_NEW_DEVICE, $newDeviceCheckDTO);
        } catch (AccessDeniedException) {
            return $this->sendBadRequestJsonResponse([FormMessages::ACCESS_DENIED]);
        }
        $device = $newDeviceService->createNewDevice($newDeviceCheckDTO);
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
