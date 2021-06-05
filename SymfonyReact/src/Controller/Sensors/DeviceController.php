<?php


namespace App\Controller\Sensors;



use App\Entity\Core\GroupNames;
use App\Form\FormMessages;
use App\Services\ESPDeviceSensor\Devices\DeviceServiceUser;
use App\Traits\API\HomeAppAPIResponseTrait;
use App\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/HomeApp/api/devices")
 */
class DeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/add-new-device", name="add-new-device")
     * @param Request $request
     * @param DeviceServiceUser $deviceService
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     */
    public function addNewDevice(Request $request, DeviceServiceUser $deviceService, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        $deviceName = $request->get('device-name');
        $deviceGroup = $request->get('device-group');
        $deviceRoom = $request->get('device-room');

        if (!isset($deviceGroup, $deviceRoom)) {
            return $this->sendBadRequestJsonResponse([
                'errors' => FormMessages::FORM_PRE_PROCESS_FAILURE
            ]);
        }

        $em = $this->getDoctrine()->getManager();

        $groupNameObject = $em->getRepository(GroupNames::class)->findOneBy(['groupNameID' => $deviceGroup]);

        try {
            $this->denyAccessUnlessGranted(DeviceVoter::ADD_NEW_DEVICE, $groupNameObject);
        } catch (\Exception) {
            return $this->sendBadRequestJsonResponse(['errors' => FormMessages::ACCES_DENIED]);
        }

        $deviceData = [
            'deviceName' => $deviceName,
            'groupNameObject' => $deviceGroup,
            'roomObject' => $deviceRoom
        ];

        $device = $deviceService->handleNewDeviceSubmission($deviceData);

        if ($device === null || !empty($deviceService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse($deviceService->getServerErrors() ?? ['errors' => 'Something went wrong please try again']);
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
