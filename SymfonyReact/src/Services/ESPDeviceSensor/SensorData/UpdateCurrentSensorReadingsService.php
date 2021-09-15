<?php

namespace App\Services\ESPDeviceSensor\SensorData;

use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\ErrorLogs;
use App\Exceptions\ConstRecordEntityException;
use App\Exceptions\OutOfBoundsEntityException;
use App\Exceptions\ReadingTypeNotSupportedException;
use App\Exceptions\SensorNotFoundException;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\Services\ESPDeviceSensor\SensorData\ConstantlyRecord\SensorConstantlyRecordService;
use App\Services\ESPDeviceSensor\SensorData\Interfaces\UpdateCurrentSensorReadingInterface;
use App\Services\ESPDeviceSensor\SensorData\OutOfBounds\SensorOutOfBoundsServiceService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Exception;
use JetBrains\PhpStorm\Pure;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;


class UpdateCurrentSensorReadingsService extends AbstractSensorUpdateService implements UpdateCurrentSensorReadingInterface
{
    /**
     * @var SensorOutOfBoundsServiceService
     */
    private SensorOutOfBoundsServiceService $sensorOutOfBoundsService;

    /**
     * @var SensorConstantlyRecordService
     */
    private SensorConstantlyRecordService $sensorConstantlyRecordService;

    /**
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface $formFactory
     * @param SensorOutOfBoundsServiceService $sensorOutOfBoundsService
     * @param SensorConstantlyRecordService $sensorConstantlyRecordService
     */
    #[Pure] public function __construct(
        EntityManagerInterface          $em,
        FormFactoryInterface            $formFactory,
        SensorOutOfBoundsServiceService $sensorOutOfBoundsService,
        SensorConstantlyRecordService   $sensorConstantlyRecordService
    )
    {
        $this->sensorConstantlyRecordService = $sensorConstantlyRecordService;
        $this->sensorOutOfBoundsService = $sensorOutOfBoundsService;
        parent::__construct($em, $formFactory);
    }

    /**
     * @param UpdateSensorReadingDTO $sensorData
     * @param Devices $device
     * @return bool
     */
        public function handleUpdateCurrentReadingSensorData(UpdateSensorReadingDTO $sensorData, Devices $device): bool
        {
            try {
                return match ($sensorData->getSensorType()) {
                    SensorType::DALLAS_TEMPERATURE => $this->handleDallasUpdateRequest($sensorData, $device),
                    SensorType::DHT_SENSOR => $this->handleDhtUpdateRequest($sensorData, $device),
                    default => throw new UnexpectedValueException('No type has been added to handle this request')
                };

            } catch (
                BadRequestException
                | SensorNotFoundException
                | UnexpectedValueException $exception
            ) {
                $this->userInputErrors[] = $exception->getMessage();
                dd($exception->getMessage());
                error_log($exception->getMessage(), 0, ErrorLogs::USER_INPUT_ERROR_LOG_LOCATION);

            } catch (ORMException | RuntimeException $exception) {
                $this->serverErrors[] = $exception->getMessage();
                dd($exception->getMessage());
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }

            return true;
        }

    /**
     * @param UpdateSensorReadingDTO $updateSensorReadingDTO
     * @param Devices $device
     * @return bool
     * @throws SensorNotFoundException
     */
    private function handleDallasUpdateRequest(UpdateSensorReadingDTO $updateSensorReadingDTO, Devices $device): bool
    {
        $sensorTypeObjects = $this->getSensorReadingTypeObjects($updateSensorReadingDTO, $device);

        $updateData = [
                'currentReading' => $updateSensorReadingDTO->getCurrentReadings(),
                'sensorType' => $sensorTypeObjects->get(0)->getSensorTypeName()
        ];

        $this->prepareAndProcessSensorForms(
            $sensorTypeObjects,
            $updateSensorReadingDTO,
            [$updateData]
        );

        $this->handleExtraSensorDataChecks($sensorTypeObjects);

        try {
            $this->em->flush();
        } catch (Exception $exception) {
            error_log($exception->getMessage(), ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            return false;
        }

        return true;
    }

    /**
     * @param UpdateSensorReadingDTO $updateSensorReadingDTO
     * @param $device
     * @return bool
     */
    private function handleDhtUpdateRequest(UpdateSensorReadingDTO $updateSensorReadingDTO, $device): bool
    {
        $sensorTypeObjects = $this->getSensorReadingTypeObjects(
            $updateSensorReadingDTO,
            $device
        );
//        dd($sensorTypeObjects->get(0)->getSensorTypeName());

        $updateData = [
            [
                'currentReading' => $updateSensorReadingDTO->getCurrentReadings()['tempReading'],
                'sensorType' => $sensorTypeObjects->get(0)->getSensorTypeName()
            ],
            [
                'currentReading' => $updateSensorReadingDTO->getCurrentReadings()['humidReading'],
                'sensorType' => $sensorTypeObjects->get(1)->getSensorTypeName()
            ]
        ];

        $this->prepareAndProcessSensorForms(
            $sensorTypeObjects,
            $updateSensorReadingDTO,
            $updateData
        );

        $this->handleExtraSensorDataChecks($sensorTypeObjects);

        try {
            $this->em->flush();
//            dd('flushh');
        } catch (Exception $exception) {
//        dd('heere');
            error_log($exception->getMessage(), ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            return false;
        }

        return true;
    }


    private function handleBmpUpdateRequest(UpdateSensorReadingDTO $updateSensorReadingDTO, $device): bool
    {

    }

    private function handleSoilUpdateRequest(UpdateSensorReadingDTO $updateSensorReadingDTO, $device): bool
    {

    }

    private function prepareAndProcessSensorForms(
        ArrayCollection $sensorTypeObjects,
        UpdateSensorReadingDTO $updateSensorReadingDTO,
        array $updateData
    ): void
    {
        $sensorType = $sensorTypeObjects->get(0)->getSensorObject()->getSensorTypeID();

        $sensorFormData = $this->prepareSensorFormData(
            $sensorType,
            ['sensorData' => $updateData],
            SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY
        );

        if (empty($sensorFormData)) {
            throw new RuntimeException(
                'Sensor form has failed to process correctly for sensor ' . $updateSensorReadingDTO->getSensorName()
            );
        }

        $this->processSensorForm(
            $sensorFormData,
            $sensorTypeObjects->toArray()
        );
    }

    private function getSensorReadingTypeObjects(UpdateSensorReadingDTO $updateSensorReadingDTO, $device): ArrayCollection
    {
        $sensorTypeObjects = $this->getSensorReadingTypeObjectsToUpdate($device, $updateSensorReadingDTO->getSensorName());

        if ($sensorTypeObjects->isEmpty()) {
            throw new SensorNotFoundException(
                sprintf(
                    SensorNotFoundException::SENSOR_NOT_FOUND_WITH_SENSOR_NAME,
                    $updateSensorReadingDTO->getSensorName()
                )
            );
        }

        return $sensorTypeObjects;
    }

    /**
     * @param ArrayCollection<AllSensorReadingTypeInterface> $sensorTypeObjects
     */
    private function handleExtraSensorDataChecks(ArrayCollection $sensorTypeObjects): void
    {
        $sensorTypeObjects->forAll(function ($key, AllSensorReadingTypeInterface $sensorTypeObject) {
            try {
                $outOfBoundsEntity = $this->sensorOutOfBoundsService->checkAndHandleSensorReadingOutOfBounds($sensorTypeObject);
                if ($outOfBoundsEntity !== null) {
                    $this->em->persist($outOfBoundsEntity);
                }
            } catch (OutOfBoundsEntityException | ReadingTypeNotSupportedException $exception) {
                $this->serverErrors[] = $exception->getMessage();
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }

            try {
                $constRecordEntity = $this->sensorConstantlyRecordService->checkAndProcessConstRecord($sensorTypeObject);
                if ($constRecordEntity !== null) {
                    $this->em->persist($constRecordEntity);
                }
            } catch (ConstRecordEntityException | ReadingTypeNotSupportedException $exception) {
                $this->serverErrors[] = $exception->getMessage();
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }
        });
    }

}
