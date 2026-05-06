<?php

declare(strict_types=1);

namespace App\Controller\Device;

use App\Entity\Device\Devices;
use App\Exceptions\Device\DeviceIPNotSetException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Device\Request\DeviceRestartRequestHandler;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\DeviceVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

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
        } catch (TransportExceptionInterface $e) {
            return $this->sendInternalServerErrorJsonResponse();
        }
        if ($restartSuccess === false) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::DEVICE_RESTART_FAILED]);
        }

        return $this->sendSuccessfulJsonResponse(title: 'Device restarted successfully');
    }
}
