<?php

namespace App\Devices\DTO;

class DeviceDTO
{
    private string $deviceName;

    private int $groupNameId;

    private int $roomId;

    public function __construct(string $deviceName, int $groupNameId, int $roomId)
    {
        $this->deviceName = $deviceName;
        $this->groupNameId = $groupNameId;
        $this->roomId = $roomId;
    }

    /**
     * @return string
     */
    public function getDeviceName(): string
    {
        return $this->deviceName;
    }

    /**
     * @return int
     */
    public function getGroupNameId(): int
    {
        return $this->groupNameId;
    }

    /**
     * @return int
     */
    public function getRoomId(): int
    {
        return $this->roomId;
    }
}
