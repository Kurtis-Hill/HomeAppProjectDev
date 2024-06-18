<?php
declare(strict_types=1);

namespace App\Controller\Device;

use App\Entity\Device\Devices;
use App\Exceptions\Device\DeviceIPNotSetException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Device\Request\DevicePingRequestHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

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
