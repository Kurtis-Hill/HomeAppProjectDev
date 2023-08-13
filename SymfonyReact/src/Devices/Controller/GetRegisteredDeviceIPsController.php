<?php

namespace App\Devices\Controller;

use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Repository\IPLogRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GetRegisteredDeviceIPsController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route(CommonURL::DEVICE_HOMEAPP_API_URL . 'registered-devices', name: 'get-new-device', methods: [Request::METHOD_POST])]
    public function getRegisteredDeviceIPs(IPLogRepository $IPLogRepository): JsonResponse
    {
        $allDeviceIPs = $IPLogRepository->findAll();

        foreach ($allDeviceIPs as $deviceIP) {
            $deviceIPs[] = $deviceIP->getIpAddress();
        }

        return $this->sendSuccessfulJsonResponse($deviceIPs ?? []);
    }
}
