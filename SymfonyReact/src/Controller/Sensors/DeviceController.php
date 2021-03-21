<?php


namespace App\Controller\Sensors;



use App\Entity\Devices\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\Services\Devices\DeviceService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

/**
 * @Route("/HomeApp/api/devices")
 */
class DeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @var UserPasswordEncoder
     */
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoder $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @Route("/new-device/submit-form-data", name="add-new-device")
     * @param Request $request
     * @param DeviceService $deviceService
     * @return JsonResponse
     */
    public function addNewDevice(Request $request, DeviceService $deviceService): JsonResponse
    {
        $deviceName = $request->get('deviceName');
        $deviceGroup = $request->get('deviceGroup');
        $deviceRoom = $request->get('deviceRoom');


        if (!isset($deviceGroup, $deviceRoom, $deviceName)) {
            return $this->sendBadRequestResponse(['errors' => 'Bad request somethings wrong with your form data, if the problem persists log out an back in again']);
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
