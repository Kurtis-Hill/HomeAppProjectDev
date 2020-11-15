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
 * @Route("/HomeApp/devices", name="devices")
 */
class DeviceController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    /**
     * @Route("/new-device/submit-form-data", name="navbar-new-device-data")
     */
    public function addNewDeviceModalData(DeviceService $deviceService, Request $request): JsonResponse
    {
        $errors = [];

        $deviceName = $request->query->get('device-name');
        $deviceGroup = $request->query->get('device-group');
        $deviceRoom = $request->query->get('device-room');

        if (!$deviceName || $deviceGroup || $deviceRoom) {
            return $this->sendBadRequestResponse(['errors' => 'No card data found query if you have devices please logout and back in again please']);
        }

        $deviceData = [
            'deviceName' => $deviceName,
            'groupNameIds' => $deviceGroup,
            'roomId' => $deviceRoom
        ];


        $newDevice = new Devices();

        $addNewDeviceForm = $this->createForm(AddNewDeviceForm::class, $newDevice);

        $handledForm = $deviceService->handleNewDeviceSubmission($deviceData, $addNewDeviceForm);

        if (!empty($handledForm->getErrors())) {
            foreach ($handledForm->getErrors(true, true) as $value) {
                array_push($errors, $value->getMessage());
            }
        }

        if (!empty($deviceService->returnAllErrors())) {
            return new JsonResponse(['errors' => $errors], 400);
        }
        else {
            $secret = $handledForm->getData()->getSecret();

            return new JsonResponse(['secret' => $secret, 'deviceID' => $newDevice->getDevicenameid()], 200);
        }
    }
}