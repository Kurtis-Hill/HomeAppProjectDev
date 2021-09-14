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
use App\Services\ESPDeviceSensor\SensorData\OutOfBounds\SensorOutOfBoundsServiceService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;


class UpdateCurrentSensorReadingsService extends AbstractSensorUpdateService
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
                    default => throw new UnexpectedValueException('No type has been added to handle this request')
                };

            } catch (
                BadRequestException
                | SensorNotFoundException
                | UnexpectedValueException $exception
            ) {
                $this->userInputErrors[] = $exception->getMessage();
                error_log($exception->getMessage(), 0, ErrorLogs::USER_INPUT_ERROR_LOG_LOCATION);

            } catch (ORMException | RuntimeException $exception) {
                $this->serverErrors[] = $exception->getMessage();
                error_log($exception->getMessage(), 0, ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }

            return true;
        }

    /**
     * @param UpdateSensorReadingDTO $sensorData
     * @param Devices $device
     * @return bool
     * @throws SensorNotFoundException
     */
    private function handleDallasUpdateRequest(UpdateSensorReadingDTO $sensorData, Devices $device): bool
    {
        $sensorTypeObjects = new ArrayCollection(
            $this->em->getRepository(Sensors::class)->getSensorTypeObjectsBySensor(
            $device,
            $sensorData->getSensorName(),
            SensorType::SENSOR_READING_TYPE_DATA
            )
        );

        if ($sensorTypeObjects->isEmpty()) {
            throw new SensorNotFoundException(
                sprintf(
                    SensorNotFoundException::SENSOR_NOT_FOUND_WITH_SENSOR_NAME,
                    $sensorData->getSensorName()
                )
            );
        }
        $sensorType = $sensorTypeObjects->get(0)->getSensorObject()->getSensorTypeID();

        $updateData = [
                'currentReading' => $sensorData->getCurrentReadings()['currentReading'],
                'sensorType' => Temperature::READING_TYPE
        ];

        $sensorFormData = $this->prepareSensorFormData(
            $sensorType,
            ['sensorData' => [$updateData]],
            SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY
        );

        if (empty($sensorFormData)) {
            throw new RuntimeException(
                'Sensor form has failed to process correctly for sensor ' . $sensorData->getSensorName()
            );
        }

        $this->processSensorForm(
            $sensorFormData,
            $sensorTypeObjects->toArray()
        );

        $this->handleExtraSensorDataChecks($sensorTypeObjects);

        $this->em->flush();

        return true;
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



    private function handleBmpUpdateRequest(Request $request)
    {

    }

    private function handleSoilUpdateRequest(Request $request)
    {

    }

    private function handleDhtUpdateRequest(Request $request)
    {

    }
}
