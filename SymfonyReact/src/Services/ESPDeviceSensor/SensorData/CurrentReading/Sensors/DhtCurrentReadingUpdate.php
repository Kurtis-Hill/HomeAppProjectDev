<?php

namespace App\Services\ESPDeviceSensor\SensorData\CurrentReading\Sensors;

use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\ErrorLogs;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class DhtCurrentReadingUpdate extends AbstractUpdateCurrentReadingSensorService implements AllSensorUpdateReadingServiceInterface
{
    public function updateCurrentReading(UpdateSensorReadingDTO $updateSensorReadingDTO, ArrayCollection $sensorReadingTypeObjects): bool
    {
        foreach ($sensorReadingTypeObjects as $sensorReadingTypeObject) {
            $updateData[] = [
                'currentReading' => $updateSensorReadingDTO->getCurrentReadings()[$sensorReadingTypeObjects->get(0)->getSensorTypeName() . 'Reading'],
                'sensorType' => $sensorReadingTypeObject->getSensorTypeName()
            ];
        }

        $this->prepareAndProcessSensorForms(
            $sensorReadingTypeObjects,
            $updateSensorReadingDTO,
            $updateData
        );

        $this->handleOutOfBoundsReadingsCheck($sensorReadingTypeObjects);
        $this->handleConstRecordReadingsCheck($sensorReadingTypeObjects);

        try {
            $this->em->flush();
        } catch (Exception $exception) {
            error_log($exception->getMessage(), ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            return false;
        }

        dd($updateSensorReadingDTO);
        return true;
    }
}
