<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Exceptions\ConstRecordEntityException;
use App\ESPDeviceSensor\Exceptions\OutOfBoundsEntityException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\Exceptions\SensorNotFoundException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepository;
use App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord\SensorConstantlyRecordServiceInterface;
use App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord\SensorConstantlyRecordServiceService;
use App\ESPDeviceSensor\SensorDataServices\OutOfBounds\OutOfBoundsSensorServiceInterface;
use App\ESPDeviceSensor\SensorDataServices\OutOfBounds\SensorOutOfBoundsSensorService;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\AbstractSensorFormsUpdateService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use UnexpectedValueException;

class UpdateCurrentSensorFormReadingsService extends AbstractSensorFormsUpdateService implements UpdateCurrentSensorFormReadingInterface
{
    private SensorConstantlyRecordServiceService $constantlyRecordService;

    private SensorOutOfBoundsSensorService $outOfBoundsServiceService;

    #[Pure] public function __construct(
        SensorRepository $sensorRepository,
        SensorReadingTypeFactoryInterface $sensorReadingTypeFactory,
        FormFactoryInterface $formFactory,
        SensorConstantlyRecordServiceInterface $constantlyRecordService,
        OutOfBoundsSensorServiceInterface $outOfBoundsServiceService,
    ) {
        $this->outOfBoundsServiceService = $outOfBoundsServiceService;
        $this->constantlyRecordService = $constantlyRecordService;

        parent::__construct($sensorRepository,
            $sensorReadingTypeFactory,
            $formFactory
        );
    }

    /**
     * @param UpdateSensorReadingDTO $updateSensorReadingDTO
     * @param Devices $device
     * @return bool
     */
    public function handleUpdateSensorCurrentReading(UpdateSensorReadingDTO $updateSensorReadingDTO, Devices $device): bool
    {
        try {
            $sensorReadingTypeObjects = $this->getSensorReadingTypeObjects($updateSensorReadingDTO, $device);
            $this->processSensorDataToUpdate($updateSensorReadingDTO, $sensorReadingTypeObjects);
            $this->handleOutOfBoundsReadingsCheck($sensorReadingTypeObjects);
            $this->handleConstRecordReadingsCheck($sensorReadingTypeObjects);

            $this->sensorRepository->flush();

            return true;
        } catch (
            BadRequestException
            | SensorNotFoundException
            | UnexpectedValueException $exception
        ) {
            error_log($exception->getMessage(), 0, ErrorLogs::USER_INPUT_ERROR_LOG_LOCATION);

        } catch (
            ORMException
            | RuntimeException
            | ReadingTypeNotSupportedException $exception) {
            error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
        }

        return true;
    }

    private function processSensorDataToUpdate(UpdateSensorReadingDTO $updateSensorReadingDTO, ArrayCollection $sensorReadingTypeObjects): void
    {
        if ($sensorReadingTypeObjects->isEmpty()) {
            throw new ReadingTypeNotSupportedException(
                sprintf(
                    ReadingTypeNotSupportedException::READEING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                    $updateSensorReadingDTO->getSensorName()
                )
            );
        }

        foreach ($sensorReadingTypeObjects as $sensorReadingTypeObject) {
            $updateData[] = [
                'currentReading' => $updateSensorReadingDTO->getCurrentReadings()[$sensorReadingTypeObject->getSensorTypeName() . 'Reading'],
                'sensorType' => $sensorReadingTypeObject->getSensorTypeName()
            ];
        }
        $this->prepareAndProcessSensorForms(
            $sensorReadingTypeObjects,
            $updateSensorReadingDTO,
            $updateData ?? []
        );
    }

    /**
     * @param ArrayCollection<AllSensorReadingTypeInterface> $sensorTypeObjects
     */
    private function handleConstRecordReadingsCheck(ArrayCollection $sensorTypeObjects): void
    {
        foreach ($sensorTypeObjects as $sensorTypeObject) {
            try {
                $this->constantlyRecordService->checkAndProcessConstRecord($sensorTypeObject);
            } catch (ConstRecordEntityException | ReadingTypeNotSupportedException $exception) {
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }
        }
    }

    /**
     * @param ArrayCollection<<AllSensorReadingTypeInterface> $sensorTypeObjects
     */
    private function handleOutOfBoundsReadingsCheck(ArrayCollection $sensorTypeObjects): void
    {
        foreach ($sensorTypeObjects as $sensorTypeObject) {
            try {
                $this->outOfBoundsServiceService->checkAndHandleSensorReadingOutOfBounds($sensorTypeObject);
            } catch (OutOfBoundsEntityException | ReadingTypeNotSupportedException $exception) {
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }
        }
    }
}
