<?php

namespace App\Devices\Controller;

use App\Devices\DeviceServices\NewDevice\NewESP8266DeviceService;
use App\Devices\DTO\DeviceDTO;
use App\Devices\Voters\DeviceVoter;
use App\User\Entity\GroupNames;
use App\Form\FormMessages;
use App\Traits\API\HomeAppAPIResponseTrait;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/HomeApp/api/user-devices', name: 'user-devices')]
class AddNewDeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;
    /**
     * @param Request $request
     * @param NewESP8266DeviceService $deviceService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    #[Route('/add-new-device', name: 'add-new-esp-device', methods: [Request::METHOD_POST])]
    public function addNewDevice(
        Request $request,
        NewESP8266DeviceService $deviceService,
        UserPasswordEncoderInterface $passwordEncoder
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

        $em = $this->getDoctrine()->getManager();

        $groupNameObject = $em->getRepository(GroupNames::class)->findOneBy(['groupNameID' => $deviceGroup]);

        if (!$groupNameObject instanceof GroupNames) {
            return $this->sendBadRequestJsonResponse(['Cannot find group name to add device too']);
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

//        $deviceData = [
//            'deviceName' => $deviceName,
//            'groupNameObject' => $deviceGroup,
//            'roomObject' => $deviceRoom
//        ];

        $device = $deviceService->handleNewDeviceSubmission($deviceData);

        if ($device === null || !empty($deviceService->getServerErrors())) {
            return $this->sendInternalServerErrorJsonResponse($deviceService->getServerErrors() ?? ['Something went wrong please try again']);
        }
        if (!empty($deviceService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($deviceService->getUserInputErrors() ?? ['the form you have submitted has failed']);
        }

        $device->setPassword(
            $passwordEncoder->encodePassword(
                $device,
                $device->getDeviceSecret()
            )
        );
        $em = $this->getDoctrine()->getManager();
        $em->persist($device);
        $em->flush();

        $secret = $device->getDeviceSecret();
        $deviceID = $device->getDeviceNameID();

        return $this->sendCreatedResourceJsonResponse(['secret' => $secret, 'deviceID' => $deviceID]);
    }
}
