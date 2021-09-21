<?php

namespace App\Services\ESPDeviceSensor\SensorData\CurrentReading\Sensors;

use App\Exceptions\ConstRecordEntityException;
use App\Exceptions\OutOfBoundsEntityException;
use App\Exceptions\ReadingTypeNotSupportedException;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\Services\ESPDeviceSensor\SensorData\AbstractSensorUpdateService;
use App\Services\ESPDeviceSensor\SensorData\ConstantlyRecord\SensorConstantlyRecordService;
use App\Services\ESPDeviceSensor\SensorData\OutOfBounds\SensorOutOfBoundsSensorService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;

abstract class AbstractUpdateCurrentReadingSensorService extends AbstractSensorUpdateService
{
    private SensorConstantlyRecordService $constantlyRecordService;

    private SensorOutOfBoundsSensorService $outOfBoundsServiceService;

    public function __construct(
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory,
        SensorConstantlyRecordService $constantlyRecordService,
        SensorOutOfBoundsSensorService $outOfBoundsServiceService
    )
    {
        $this->outOfBoundsServiceService = $outOfBoundsServiceService;
        $this->constantlyRecordService = $constantlyRecordService;
        parent::__construct($em, $formFactory);
    }


    protected function handleConstRecordReadingsCheck(ArrayCollection $sensorTypeObjects)
    {
        $sensorTypeObjects->forAll(function ($key, AllSensorReadingTypeInterface $sensorTypeObject) {
        try {
            $constRecordEntity = $this->getConstRecordService()->checkAndProcessConstRecord($sensorTypeObject);
            if ($constRecordEntity !== null) {
                $this->em->persist($constRecordEntity);
            }
        } catch (ConstRecordEntityException | ReadingTypeNotSupportedException $exception) {
            error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
        }
        });
    }

    protected function handleOutOfBoundsReadingsCheck(ArrayCollection $sensorTypeObjects)
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
