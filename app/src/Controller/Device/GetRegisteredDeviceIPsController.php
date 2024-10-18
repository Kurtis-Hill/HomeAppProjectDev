<?php
declare(strict_types=1);

namespace App\Controller\Device;

use App\Builders\Device\DeviceResponse\DeviceIPResponseDTOBuilder;
use App\Repository\Common\ORM\IPLogRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
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
            $normalizedResponse = $this->normalize($deviceIPs);

            return $this->sendSuccessfulJsonResponse($normalizedResponse);
        } catch (NotNormalizableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        } catch (ExceptionInterface $e) {
            return $this->sendInternalServerErrorJsonResponse();
        }
    }
}
