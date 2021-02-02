<?php


namespace App\Controller\Sensors;



use App\Entity\Sensors\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\Services\Devices\DeviceService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/HomeApp/api/devices", name="devices")
 */
class DeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

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

//dd($handledForm);
        if (!empty($deviceService->getServerErrors())) {
            return $this->sendBadRequestResponse($deviceService->getServerErrors());
        }
        if (!empty($deviceService->getUserInputErrors())) {
            return $this->sendBadRequestResponse($deviceService->getUserInputErrors());
        }

        $secret = $handledForm->getData()->getDeviceSecret();
        $deviceID = $handledForm->getData()->getDeviceNameID();

        return $this->sendCreatedResourceResponse(['secret' => $secret, 'deviceID' => $deviceID]);
    }
}
