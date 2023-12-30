<?php
declare(strict_types=1);

namespace App\Devices\Controller;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Repository\IPLogRepository;
use App\Devices\Builders\DeviceResponse\DeviceIPResponseDTOBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class GetRegisteredDeviceIPsController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route(CommonURL::USER_HOMEAPP_API_URL . 'registered-devices', name: 'get-new-device', methods: [Request::METHOD_GET])]
    public function getRegisteredDeviceIPs(IPLogRepository $IPLogRepository, DeviceIPResponseDTOBuilder $deviceIPResponseDTOBuilder): JsonResponse
    {
        $allDeviceIPs = $IPLogRepository->findAll();
        foreach ($allDeviceIPs as $deviceIP) {
            $deviceIPs[] = $deviceIPResponseDTOBuilder->buildDeviceIPResponseDTOBuilder($deviceIP);
        }

        if (empty($deviceIPs)) {
            return $this->sendSuccessfulJsonResponse([], 'No devices registered');
        }

        try {
            $normalizedResponse = $this->normalizeResponse($deviceIPs);

            return $this->sendSuccessfulJsonResponse($normalizedResponse);
        } catch (NotNormalizableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        } catch (ExceptionInterface $e) {
            return $this->sendInternalServerErrorJsonResponse();
        }
    }
}
