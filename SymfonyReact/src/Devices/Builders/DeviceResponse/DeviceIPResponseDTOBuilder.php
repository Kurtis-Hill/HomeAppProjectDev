<?php
declare(strict_types=1);

namespace App\Devices\Builders\DeviceResponse;

use App\Common\Entity\IPLog;
use App\Devices\DTO\Response\DeviceIPResponseDTO;

class DeviceIPResponseDTOBuilder
{
    public function buildDeviceIPResponseDTOBuilder(IPLog $ipLog): DeviceIPResponseDTO
    {
        return new DeviceIPResponseDTO(
            $ipLog->getIPAddress(),
        );
    }
}
