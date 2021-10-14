<?php

namespace App\Controller\Devices;

use App\Traits\API\HomeAppAPIResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/HomeApp/api/device/esp/', name: 'user-ip-controller')]
class UpdateSensorRequest extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('ip-update', name: 'user-ip-update')]
    public function updateDeviceIpAddress(Request $request)
    {

    }
}
