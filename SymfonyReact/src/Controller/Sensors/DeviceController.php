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
 * @Route("/HomeApp/devices", name="navbar")
 */
class DeviceController extends AbstractController
{
    /**
     * @Route("/new-device/modal-data", name="navbar-new-device-data")
     */
    public function addNewDeviceModalData(DeviceService $deviceService, Request $request)
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
        if (is_string($handledForm)) {
            $secret = $handledForm;
        }

        if (!empty($errors)) {
            return new JsonResponse(['errors' => $errors], 400);
        }
        else {
            return new JsonResponse($secret, 200);
        }
    }

    /**
     * @Route("/{deviceName}", name="navbar")
     */
    public function showDeviceSettings(Request $request, $deviceName)
    {
        //query for device if no device redirect
        return $this->render('index');
    }
}