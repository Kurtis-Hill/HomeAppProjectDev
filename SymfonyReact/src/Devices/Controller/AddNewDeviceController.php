<?php

namespace App\Devices\Controller;

use App\Devices\DeviceServices\NewDevice\NewDeviceServiceInterface;
use App\Devices\DTO\DeviceDTO;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Voters\DeviceVoter;
use App\Form\FormMessages;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\User\Exceptions\GroupNameExceptions\GroupNameNotFoundException;
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
        NewDeviceServiceInterface $deviceService,
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

        if (!isset($deviceGroup, $deviceRoom)) {
            return $this->sendBadRequestJsonResponse([FormMessages::FORM_PRE_PROCESS_FAILURE]);
        }

        try {
            $groupNameObject = $groupCheckService->checkForGroupById($deviceGroup);
        } catch (GroupNameNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }
        try {
            $this->denyAccessUnlessGranted(DeviceVoter::ADD_NEW_DEVICE, $groupNameObject);
        } catch (AccessDeniedException) {
            return $this->sendBadRequestJsonResponse([FormMessages::ACCESS_DENIED]);
        }

        $deviceData = new DeviceDTO(
            $deviceName,
            $deviceGroup,
            $deviceRoom,
        );

        try {
            $device = $deviceService->handleNewDeviceCreation($deviceData);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to save device']);
        } catch (DuplicateDeviceException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }

        if ($device === null || !empty($deviceService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($deviceService->getUserInputErrors() ?? ['the form you have submitted has failed']);
        }

        $deviceSaved = $deviceService->encodeAndSaveNewDevice($device);

        if ($deviceSaved === false) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to save device']);
        }
        $secret = $device->getDeviceSecret();
        $deviceID = $device->getDeviceNameID();

        return $this->sendCreatedResourceJsonResponse(['secret' => $secret, 'deviceID' => $deviceID]);
    }
}
