<?php
declare(strict_types=1);

namespace App\Devices\Builders\DeviceGet;

use App\Devices\DTO\Internal\GetDeviceDTO;

class GetDeviceDTOBuilder
{
    public static function buildGetDeviceDTO(
        int $limit,
        int $offset,
    ): GetDeviceDTO {
        return new GetDeviceDTO(
            $limit,
            $offset,
        );
    }
}
