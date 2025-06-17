<?php

namespace App\DTOs\Sensor\Request;

use App\Entity\Sensor\Sensor;
use App\CustomValidators\Device\DeviceIDExists;
use App\CustomValidators\NoSpecialCharactersNameConstraint;
use App\CustomValidators\Sensor\SensorType\SensorTypeDoesntExistConstraint;
use App\CustomValidators\Sensor\UniqueSensorForDevice;
use Symfony\Component\Validator\Constraints as Assert;

#[UniqueSensorForDevice]
class AddNewSensorRequestDTO implements CanAdjustSensorDeviceIDAndSensorNameInterface
{
    #[
        Assert\Type(type: 'integer', message: 'sensorTypeID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorTypeID cannot be null"
        ),
        SensorTypeDoesntExistConstraint,
    ]
    private int $sensorTypeID;

    #[
        Assert\Type(type: 'integer', message: 'deviceNameID must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "deviceID name cannot be null"
        ),
        DeviceIDExists,
    ]
    private int $deviceID;

    #[
        Assert\Type(type: 'string', message: 'sensorName must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "sensorName name cannot be null"
        ),
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: Sensor::SENSOR_NAME_MIN_LENGTH,
            max: Sensor::SENSOR_NAME_MAX_LENGTH,
            minMessage: "Sensor name must be at least {{ limit }} characters long",
            maxMessage: "Sensor name cannot be longer than {{ limit }} characters"
        ),
    ]
    private string $sensorName;

    #[
        Assert\Range(
            notInRangeMessage: 'pinNumber must be greater than {{ min }}',
            minMessage: 'pinNumber must be greater than {{ value }}',
            invalidMessage: 'pinNumber must be an int you have provided {{ value }}',
            min: 0,
        ),
        Assert\NotNull(
            message: "pinNumber name cannot be null"
        ),
    ]
    private int $pinNumber;

    #[
        Assert\NotNull(
            message: "readingInterval cannot be null"
        ),
        Assert\Range(
            notInRangeMessage: "readingInterval must be greater than {{ min }}",
            invalidMessage: "readingInterval must be a number",
            min: Sensor::MIN_READING_INTERVAL
        ),
    ]
    private int $readingInterval;

    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(int $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    public function setDeviceID(int $deviceID): void
    {
        $this->deviceID = $deviceID;
    }

    public function getSensorName(): string
    {
        return $this->sensorName;
    }

    public function setSensorName(string $sensorName): void
    {
        $this->sensorName = $sensorName;
    }

    public function getPinNumber(): int
    {
        return $this->pinNumber;
    }

    public function setPinNumber(int $pinNumber): void
    {
        $this->pinNumber = $pinNumber;
    }

    public function getReadingInterval(): int
    {
        return $this->readingInterval;
    }

    public function setReadingInterval(int $readingInterval): void
    {
        $this->readingInterval = $readingInterval;
    }
}
