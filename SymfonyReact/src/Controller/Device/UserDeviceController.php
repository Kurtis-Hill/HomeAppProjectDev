<?php


namespace App\Controller\Device;

use App\Entity\Core\GroupNames;
use App\Form\FormMessages;
use App\ESPDeviceSensor\Devices\NewDeviceService;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/HomeApp/api/user-devices', name: 'user-device')]
class UserDeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @param Request $request
     * @param NewDeviceService $deviceService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    #[Route('/add-new-device', name: 'add-new-device', methods: [Request::METHOD_POST])]
    public function addNewDevice(Request $request, NewDeviceService $deviceService, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $newDeviceData = json_decode($request->getContent(), true);
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

        $deviceData = [
            'deviceName' => $deviceName,
            'groupNameObject' => $deviceGroup,
            'roomObject' => $deviceRoom
        ];

        $device = $deviceService->handleNewDeviceSubmission($deviceData);

        if ($device === null || !empty($deviceService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse($deviceService->getServerErrors() ?? ['Something went wrong please try again']);
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
