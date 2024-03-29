<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\DeviceServices\Request\DevicePingRequestHandler;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices/')]
class PingDeviceController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route(
        path: '{deviceID}/ping',
        name: 'ping-esp-device',
        methods: [Request::METHOD_GET]
    )]
    public function pingDevice(Devices $device, DevicePingRequestHandler $devicePingRequestHandler): JsonResponse
    {
        $userAllowedToPing = $this->isGranted(DeviceVoter::PING_DEVICE, $device);

        if ($userAllowedToPing === false) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $pingSuccess = $devicePingRequestHandler->pingDevice($device);
        } catch (DeviceIPNotSetException) {
            return $this->sendBadRequestJsonResponse(['Device IP not set']);
        }
        if ($pingSuccess === false) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::DEVICE_PING_FAILED]);
        }

        return $this->sendSuccessfulJsonResponse(title: 'Device pinged successfully');
    }
}
