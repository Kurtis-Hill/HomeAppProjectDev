<?php


namespace App\Controller\Sensors;



use App\Entity\Devices\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\Services\ESPDeviceSensor\Devices\DeviceServiceUser;
use App\Traits\API\HomeAppAPIResponseTrait;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
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
        if (!empty($deviceService->getFatalErrors())) {
            $this->sendForbiddenAccessJsonResponse();
        }

        $deviceName = $request->get('deviceName');
        $deviceGroup = $request->get('deviceGroup');
        $deviceRoom = $request->get('deviceRoom');


        if (!isset($deviceGroup, $deviceRoom, $deviceName)) {
            return $this->sendBadRequestJsonResponse(['errors' => 'Bad request somethings wrong with your form data, if the problem persists log out an back in again']);
        }

        $deviceData = [
            'deviceName' => $deviceName,
            'groupNameObject' => $deviceGroup,
            'roomObject' => $deviceRoom
        ];

        $newDevice = new Devices();

        $addNewDeviceForm = $this->createForm(AddNewDeviceForm::class, $newDevice);

        $handledForm = $deviceService->handleNewDeviceSubmission($deviceData, $addNewDeviceForm);

        if (!empty($deviceService->getServerErrors())) {
            return $this->sendInternelServerErrorJsonResponse(['errors' => 'Something went wrong please try again']);
        }
        if (!empty($deviceService->getUserInputErrors()) || !$handledForm->getData() instanceof Devices) {
            return $this->sendBadRequestJsonResponse($deviceService->getUserInputErrors() ?? ['form is not an instance of device']);
        }

        $newDevice = $handledForm->getData();

        $newDevice->setPassword(
            $passwordEncoder->encodePassword(
                $newDevice,
                $newDevice->getDeviceSecret()
            )
        );
        $em = $this->getDoctrine()->getManager();
        $em->persist($newDevice);
        $em->flush();
//dd('hi');

        $secret = $handledForm->getData()->getDeviceSecret();
//        dd($newDevice->getUserID());
        $deviceID = $newDevice->getDeviceNameID();


        return $this->sendCreatedResourceJsonResponse(['secret' => $secret, 'deviceID' => $deviceID]);
    }
}
