<?php


namespace App\Controller\Sensors;


use App\Services\Devices\DeviceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $deviceName = $request->get('device-name');

        $deviceName .= time();

        $secret = hash("md5", $deviceName);

        if (empty($errors)) {
            return new JsonResponse($secret, 200);
        }
    }

    /**
     * @Route("/{deviceName}", name="navbar")
     */
    public function showDeviceSettings($deviceName, Request $request)
    {
        //query for device if no device redirect
        return $this->render('index');
    }
}