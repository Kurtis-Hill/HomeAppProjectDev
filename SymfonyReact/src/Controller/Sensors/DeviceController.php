<?php


namespace App\Controller\Sensors;


use App\Entity\Core\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\Services\Devices\DeviceService;
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
    /**
     * @Route("/new-device/modal-data", name="navbar-new-device-data")
     */
    public function addNewDeviceModalData(DeviceService $deviceService, Request $request): JsonResponse
    {
        $errors = [];

        $newDevice = new Devices();

        $addNewDeviceForm = $this->createForm(AddNewDeviceForm::class, $newDevice);

        $handledForm = $deviceService->handleNewDeviceSubmission($request, $addNewDeviceForm);

        if ($handledForm instanceof FormInterface) {
            foreach ($handledForm->getErrors(true, true) as $value) {
                array_push($errors, $value->getMessage());
            }
        }
        if (is_array($handledForm)) {
            array_push($errors, $handledForm);
        }
        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors], 400);
        }
        else {
            $secret = $handledForm;

            return new JsonResponse(['secret' => $secret, 'deviceID' => $newDevice->getDevicenameid()], 200);
        }
    }
}