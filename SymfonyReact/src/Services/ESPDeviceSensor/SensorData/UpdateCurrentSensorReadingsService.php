<?php

namespace App\Services\ESPDeviceSensor\SensorData;

use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;
use App\ErrorLogs;
use App\Exceptions\SensorNotFoundException;
use App\Services\ESPDeviceSensor\SensorData\CurrentReading\CurrentReadingUpdateSensorFactory;
use App\Services\ESPDeviceSensor\SensorData\CurrentReading\Sensors\AbstractUpdateCurrentReadingSensorService;
use App\Services\ESPDeviceSensor\SensorData\CurrentReading\UpdateCurrentSensorReadingInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use UnexpectedValueException;


class UpdateCurrentSensorReadingsService extends AbstractUpdateCurrentReadingSensorService implements UpdateCurrentSensorReadingInterface
{
    /**
     * @param UpdateSensorReadingDTO $updateSensorReadingDTO
     * @param Devices $device
     * @return bool
     */
        public function handleUpdateCurrentReadingSensorData(UpdateSensorReadingDTO $updateSensorReadingDTO, Devices $device): bool
        {
            try {
                $sensorReadingTypeObjects = $this->getSensorReadingTypeObjects($updateSensorReadingDTO, $device);

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
//                $sensorUpdateService->updateCurrentReading($updateSensorReadingDTO, $sensorTypeObjects);
            } catch (
                BadRequestException
                | SensorNotFoundException
                | UnexpectedValueException $exception
            ) {
                dd($exception->getMessage());
                error_log($exception->getMessage(), 0, ErrorLogs::USER_INPUT_ERROR_LOG_LOCATION);

            } catch (ORMException | RuntimeException $exception) {
                dd($exception->getMessage());
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }

            return true;
        }
}
