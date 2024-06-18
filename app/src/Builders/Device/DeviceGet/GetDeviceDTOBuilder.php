<?php
declare(strict_types=1);

namespace App\Builders\Device\DeviceGet;

use App\DTOs\Device\Internal\GetDeviceDTO;

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
