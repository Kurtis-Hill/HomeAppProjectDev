<?php


namespace App\Controller\Sensors;


use App\Entity\Core\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\Services\Devices\DeviceService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
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
     * @Route("/new-device/submit-form-data", name="navbar-new-device-data")
     * @param Request $request
     * @param DeviceService $deviceService
     * @return JsonResponse
     */
    public function addNewDeviceModalData(Request $request, DeviceService $deviceService): JsonResponse
    {
        $deviceGroup = $request->get('device-group');
        $deviceRoom = $request->get('device-room');

        if (empty($deviceGroup || $deviceRoom)) {
            return $this->sendBadRequestResponse(['errors' => 'Bad request somethings wrong with your form data, if the problem persists log out an back in again']);
        }

        $deviceData = [
            'devicename' => $request->get('device-name'),
            'groupnameid' => $deviceGroup,
            'roomid' => $deviceRoom
        ];

        $newDevice = new Devices();

        $addNewDeviceForm = $this->createForm(AddNewDeviceForm::class, $newDevice);

        $handledForm = $deviceService->handleNewDeviceSubmission($deviceData, $addNewDeviceForm);

        $errors = $deviceService->returnAllErrors();

        if (!empty($errors)) {
            return $this->sendBadRequestResponse($errors);
        }
        else {
            $secret = $handledForm->getData()->getSecret();
dd($newDevice->getDevicenameid());
            return $this->sendCreatedResourceResponse(['secret' => $secret, 'deviceID' => $newDevice->getDevicenameid()]);
        }
    }
}