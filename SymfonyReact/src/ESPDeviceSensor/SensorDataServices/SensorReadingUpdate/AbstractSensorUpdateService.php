<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingDTO;
use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorInterface;
use App\ESPDeviceSensor\Exceptions\SensorNotFoundException;
use App\ESPDeviceSensor\Factories\ORMFactories\SensorReadingType\SensorReadingTypeFactoryInterface;
use App\ESPDeviceSensor\Forms\SensorReadingUpdateInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepository;
use App\Traits\FormProcessorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use JetBrains\PhpStorm\ArrayShape;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;

abstract class AbstractSensorUpdateService
{
    use FormProcessorTrait;

    protected SensorRepository $sensorRepository;

    protected FormFactoryInterface $formFactory;

    private SensorReadingTypeFactoryInterface $sensorReadingTypeFactory;

    public function __construct(
        SensorRepository $sensorRepository,
        SensorReadingTypeFactoryInterface $sensorReadingTypeFactory,
        FormFactoryInterface $formFactory
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->sensorReadingTypeFactory = $sensorReadingTypeFactory;
        $this->formFactory = $formFactory;
    }

    protected function processSensorForm(array $sensorFormData, array $readingTypeObjects): void
    {
        foreach ($sensorFormData as $sensorType => $sensorData) {
            foreach ($readingTypeObjects as $sensorObject) {
                if ($sensorType === $sensorObject::class) {
                    $sensorForm = $this->formFactory->create($sensorData['formToProcess'], $sensorObject, ['formSensorType' => new $sensorData['object']]);
                    $handledForm = $this->processForm($sensorForm, $sensorData['formData']);
                    if ($handledForm === true) {
                        $readingTypeRepository = $this->sensorReadingTypeFactory->getSensorReadingTypeRepository($sensorForm->getData()::class);
                        $readingTypeRepository->persist($sensorForm->getData());
                    }
                }
            }
        }
        $this->sensorRepository->flush();
    }


    #[ArrayShape([
        AllSensorReadingTypeInterface::class => [
            "formToProcess" => SensorReadingUpdateInterface::class,
            "object" => SensorInterface::class,
            "formData" => [
                "highReading" => "int|float",
                "lowReading" => "int|float",
                "constRecord" => "bool",
            ]
        ]
    ])]
    protected function prepareSensorFormData(SensorType $sensorType, array $readingsToUpdate, string $formToProcess): array
    {
        $sensorType = $sensorType->getSensorType();
        foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorName => $sensorDataArrays) {
            if ($sensorName === $sensorType) {
                foreach ($sensorDataArrays['forms'] as $formType => $formData) {
                    if ($formType === $formToProcess) {
                        if ($formToProcess === SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY) {
                            foreach ($readingsToUpdate['sensorData'] as $sensorData) {
                                foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                    if ($readingType === $sensorData['sensorType']) {
                                        $highReading = $sensorData['highReading'] ?: null;
                                        $lowReading = $sensorData['lowReading'] ?: null;
                                        $constRecord = $sensorData['constRecord'] ?? null;

                                        $sensorFormsData[$readingTypeClass] = [
                                            'formToProcess' => $formData['form'],
                                            'object' => $sensorDataArrays['object'],
                                            'formData' => [
                                                'highReading' => $highReading,
                                                'lowReading' => $lowReading,
                                                'constRecord' => $constRecord
                                            ]
                                        ];
                                    }
                                }
                            }
                            continue;
                        }

                        if ($formToProcess === SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY) {
                            foreach ($readingsToUpdate['sensorData'] as $sensorData) {
                                foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                    if ($readingType === $sensorData['sensorType']) {
                                        $currentReading = $sensorData['currentReading'];

                                        $readingErrorMessage = "%s %s has no value";
                                        !empty($currentReading) ?: $this->userInputErrors[] = sprintf($readingErrorMessage, ucfirst($readingType), 'current reading');

                                        $sensorFormsData[$readingTypeClass] = [
                                            'formToProcess' => $formData['form'],
                                            'object' => $sensorDataArrays['object'],
                                            'formData' => [
                                                'currentReading' => $currentReading,
                                            ]
                                        ];
                                    }
                                }
                            }
                        }
                        //Any other forms can be added here
                    }
                }
            }
        }

        return $sensorFormsData ?? [];
    }


    /**
     * @throws SensorNotFoundException
     */
    protected function getSensorReadingTypeObjects(UpdateSensorReadingDTO $updateSensorReadingDTO, $device): ArrayCollection
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
     * @param ArrayCollection $sensorTypeObjects
     * @param UpdateSensorReadingDTO $updateSensorReadingDTO
     * @param array $updateData
     */
    protected function prepareAndProcessSensorForms(
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

    /**
     * @param Devices $device
     * @param string $sensorName
     * @return ArrayCollection<AllSensorReadingTypeInterface>
     */
    protected function getSensorReadingTypeObjectsToUpdate(Devices $device, string $sensorName): ArrayCollection
    {
        return new ArrayCollection(
            $this->sensorRepository->getSensorReadingTypeObjectsBySensorNameAndDevice(
                $device,
                $sensorName,
                SensorType::SENSOR_READING_TYPE_DATA
            )
        );
    }
}
