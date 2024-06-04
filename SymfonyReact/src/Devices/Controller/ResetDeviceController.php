<?php
declare(strict_types=1);

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\DeviceServices\Request\DeviceResetRequestHandler;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'user-devices/')]
class ResetDeviceController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route(
        path: '{deviceID}/reset',
        name: 'reset-esp-device',
        methods: [Request::METHOD_POST]
    )]
    public function resetDevice(Devices $device, DeviceResetRequestHandler $deviceResetRequestHandler): JsonResponse
    {
        $userAllowedToReset = $this->isGranted(DeviceVoter::RESET_DEVICE, $device);

        if ($userAllowedToReset === false) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $resetSuccess = $deviceResetRequestHandler->resetDevice($device);
        } catch (DeviceIPNotSetException) {
            return $this->sendBadRequestJsonResponse(['Device IP not set']);
        }

        if ($resetSuccess === false) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::DEVICE_RESET_FAILED]);
        }

        return $this->sendSuccessfulJsonResponse(title: 'Device reset successfully');
    }
}
