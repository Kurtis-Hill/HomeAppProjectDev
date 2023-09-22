<?php

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\DeviceServices\Request\DeviceRestartRequestHandler;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices/')]
class RestartDeviceController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route(
    path: '{deviceID}/restart',
    name: 'restart-esp-device',
    methods: [Request::METHOD_GET]
    )]
    public function restartDevice(Devices $device, DeviceRestartRequestHandler $deviceRestartRequestHandler): JsonResponse
    {
        $userAllowedToRestart = $this->isGranted(DeviceVoter::RESTART_DEVICE, $device);

        if ($userAllowedToRestart === false) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $restartSuccess = $deviceRestartRequestHandler->restartDevice($device);
        } catch (DeviceIPNotSetException) {
            return $this->sendBadRequestJsonResponse(['Device IP not set']);
        }
        if ($restartSuccess === false) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::DEVICE_RESTART_FAILED]);
        }

        return $this->sendSuccessfulJsonResponse(title: 'Device restarted successfully');
    }
}
