<?php


namespace App\Controller\Sensors;



use App\Entity\Devices\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\Services\Devices\DeviceServiceUser;
use App\Traits\API\HomeAppAPIResponseTrait;
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

//    /**
//     * @var UserPasswordEncoder
//     */
//    private $userPasswordEncoder;
//
//    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
//    {
//        $this->userPasswordEncoder = $userPasswordEncoder;
//    }

    /**
     * @Route("/add-new-device", name="add-new-device")
     * @param Request $request
     * @param DeviceServiceUser $deviceService
     * @return JsonResponse
     */
    public function addNewDevice(Request $request, DeviceServiceUser $deviceService): JsonResponse
    {
        $deviceData = [
            'deviceName' => $request->get('deviceName'),
            'groupNameObject' => $request->get('deviceGroup'),
            'roomObject' => $request->get('deviceRoom')
        ];

        if (!isset($deviceData['deviceGroup'], $deviceData['deviceRoom'], $deviceData['deviceName'])) {
            return $this->sendBadRequestResponse(['errors' => 'Bad request somethings wrong with your form data, if the problem persists log out an back in again']);
        }

        $newDevice = new Devices();

        $addNewDeviceForm = $this->createForm(AddNewDeviceForm::class, $newDevice);

        $handledForm = $deviceService->handleNewDeviceSubmission($deviceData, $addNewDeviceForm);

        if (!empty($deviceService->getServerErrors())) {
            //dd()
            return $this->sendInternelServerErrorResponse(['errors' => 'Something went wrong please try again']);
        }
        if (!empty($deviceService->getUserInputErrors())) {
            return $this->sendBadRequestResponse($deviceService->getUserInputErrors());
        }

        $secret = $handledForm->getData()->getDeviceSecret();
        $deviceID = $handledForm->getData()->getDeviceNameID();

        return $this->sendCreatedResourceResponse(['secret' => $secret, 'deviceID' => $deviceID]);
    }
}
