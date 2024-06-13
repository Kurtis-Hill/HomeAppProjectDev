<?php
declare(strict_types=1);

namespace App\Builders\Device\DeviceResponse;

use App\DTOs\Device\Response\DeviceIPResponseDTO;
use App\Entity\Common\IPLog;

class DeviceIPResponseDTOBuilder
{
    public function buildDeviceIPResponseDTOBuilder(IPLog $ipLog): DeviceIPResponseDTO
    {
        return new DeviceIPResponseDTO(
            $ipLog->getIPAddress(),
        );
    }
}
