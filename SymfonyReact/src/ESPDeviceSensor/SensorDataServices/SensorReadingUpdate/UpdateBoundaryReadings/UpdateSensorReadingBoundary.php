<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings;

use App\Entity\Devices\Devices;
use App\Entity\Sensors\SensorType;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepository;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\AbstractSensorUpdateService;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use UnexpectedValueException;

class UpdateSensorReadingBoundary extends AbstractSensorUpdateService implements UpdateBoundaryReadingsInterface, APIErrorInterface
{
    private array $userInputErrors = [];

    private array $serverErrors = [];

    public function handleSensorReadingBoundaryUpdate(Devices $device, string $sensorName, array $updateData): void
    {
        try {
            $sensorTypeObjects = $this->getSensorReadingTypeObjectsToUpdate($device, $sensorName);
            if ($sensorTypeObjects === null) {
                throw new UnexpectedValueException('No reading types were found for your request, please make sure your app is up to date');
            }
            $firstSensorTypeObject = $sensorTypeObjects[0];
            $sensorType = $firstSensorTypeObject->getSensorObject()->getSensorTypeID();

            $sensorFormData = $this->prepareSensorFormData(
                $sensorType,
                $updateData,
                SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY
            );

            if (empty($sensorFormData)) {
                throw new BadRequestException('something went wrong with processing the sensor reading update form');
            }

            $this->processSensorForm($sensorFormData, $sensorTypeObjects->toArray());
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        }catch (UnexpectedValueException $exception) {
            $this->serverErrors[] = $exception->getMessage();
        }
    }

    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }

    #[Pure] public function getUserInputErrors(): array
    {
        return array_merge($this->getAllFormInputErrors(), $this->userInputErrors);
    }


}
