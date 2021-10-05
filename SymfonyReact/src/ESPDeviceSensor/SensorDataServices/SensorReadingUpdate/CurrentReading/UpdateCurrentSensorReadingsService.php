<?php
namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Exceptions\ConstRecordEntityException;
use App\ESPDeviceSensor\Exceptions\OutOfBoundsEntityException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotSupportedException;
use App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord\SensorConstantlyRecordServiceInterface;
use App\ESPDeviceSensor\SensorDataServices\ConstantlyRecord\SensorConstantlyRecordServiceService;
use App\ESPDeviceSensor\SensorDataServices\OutOfBounds\OutOfBoundsSensorServiceInterface;
use App\ESPDeviceSensor\SensorDataServices\OutOfBounds\SensorOutOfBoundsSensorService;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\AbstractSensorUpdateService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;

class UpdateCurrentSensorReadingsService extends AbstractSensorUpdateService implements UpdateCurrentSensorReadingInterface
{
    private SensorConstantlyRecordServiceService $constantlyRecordService;

    private SensorOutOfBoundsSensorService $outOfBoundsServiceService;

    #[Pure] public function __construct(
        EntityManagerInterface                 $em,
        FormFactoryInterface                   $formFactory,
        SensorConstantlyRecordServiceInterface $constantlyRecordService,
        OutOfBoundsSensorServiceInterface      $outOfBoundsServiceService,
    )
    {
        $this->outOfBoundsServiceService = $outOfBoundsServiceService;
        $this->constantlyRecordService = $constantlyRecordService;
        parent::__construct($em, $formFactory);
    }

    /**
     * @param UpdateSensorReadingDTO $updateSensorReadingDTO
     * @param Devices $device
     * @return bool
     * @throws ReadingTypeNotSupportedException
     */
    public function handleUpdateCurrentReadingSensorData(UpdateSensorReadingDTO $updateSensorReadingDTO, Devices $device): bool
    {
//        try {
            $sensorReadingTypeObjects = $this->getSensorReadingTypeObjects($updateSensorReadingDTO, $device);
            $this->processSensorDataToUpdate($updateSensorReadingDTO, $sensorReadingTypeObjects);
            $this->handleOutOfBoundsReadingsCheck($sensorReadingTypeObjects);
            $this->handleConstRecordReadingsCheck($sensorReadingTypeObjects);

            $this->sensorRepository->flush();

            return true;
//        } catch (
//            BadRequestException
//            | SensorNotFoundException
//            | UnexpectedValueException $exception
//        ) {
//            dd($exception->getMessage());
//            error_log($exception->getMessage(), 0, ErrorLogs::USER_INPUT_ERROR_LOG_LOCATION);
//
//        } catch (
//            ORMException
//            | RuntimeException
//            | ReadingTypeNotSupportedException $exception) {
//            dd($exception->getMessage());
//            error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
//        }

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
