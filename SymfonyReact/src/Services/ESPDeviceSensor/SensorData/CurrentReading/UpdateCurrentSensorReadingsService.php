<?php

namespace App\Services\ESPDeviceSensor\SensorData\CurrentReading;

use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;
use App\ErrorLogs;
use App\Exceptions\ConstRecordEntityException;
use App\Exceptions\OutOfBoundsEntityException;
use App\Exceptions\ReadingTypeNotSupportedException;
use App\Exceptions\SensorNotFoundException;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\Services\ESPDeviceSensor\SensorData\AbstractSensorUpdateService;
use App\Services\ESPDeviceSensor\SensorData\ConstantlyRecord\SensorConstantlyRecordServiceInterface;
use App\Services\ESPDeviceSensor\SensorData\ConstantlyRecord\SensorConstantlyRecordServiceService;
use App\Services\ESPDeviceSensor\SensorData\OutOfBounds\OutOfBoundsSensorServiceInterface;
use App\Services\ESPDeviceSensor\SensorData\OutOfBounds\SensorOutOfBoundsSensorService;
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
     */
    public function handleUpdateCurrentReadingSensorData(UpdateSensorReadingDTO $updateSensorReadingDTO, Devices $device): bool
    {
//        try {
            $sensorReadingTypeObjects = $this->getSensorReadingTypeObjects($updateSensorReadingDTO, $device);
            $this->processSensorDataToUpdate($updateSensorReadingDTO, $sensorReadingTypeObjects);
            $this->handleOutOfBoundsReadingsCheck($sensorReadingTypeObjects);
            $this->handleConstRecordReadingsCheck($sensorReadingTypeObjects);

            $this->em->flush();

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
//        dd('sdfg');
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

    private function handleConstRecordReadingsCheck(ArrayCollection $sensorTypeObjects)
    {
        $sensorTypeObjects->forAll(function ($key, AllSensorReadingTypeInterface $sensorTypeObject) {
            try {
                $this->getConstRecordService()->checkAndProcessConstRecord($sensorTypeObject);
            } catch (ConstRecordEntityException | ReadingTypeNotSupportedException $exception) {
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }
        });
    }

    private function handleOutOfBoundsReadingsCheck(ArrayCollection $sensorTypeObjects)
    {
        $sensorTypeObjects->forAll(function ($key, AllSensorReadingTypeInterface $sensorTypeObject) {
            try {
                $outOfBoundsEntity = $this->getOutOfBoundsService()->checkAndHandleSensorReadingOutOfBounds($sensorTypeObject);
                if ($outOfBoundsEntity !== null) {
                    $this->em->persist($outOfBoundsEntity);
                }
            } catch (OutOfBoundsEntityException | ReadingTypeNotSupportedException $exception) {
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }
        });
    }

    private function getOutOfBoundsService()
    {
        return $this->outOfBoundsServiceService;
    }


    private function getConstRecordService()
    {
        return $this->constantlyRecordService;
    }
}
