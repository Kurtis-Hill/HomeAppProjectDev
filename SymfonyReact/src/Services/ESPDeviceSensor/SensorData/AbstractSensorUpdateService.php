<?php


namespace App\Services\ESPDeviceSensor\SensorData;


use App\DTOs\SensorDTOs\UpdateSensorReadingDTO;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Exceptions\SensorNotFoundException;
use App\Form\SensorForms\SensorReadingUpdateInterface;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;

abstract class AbstractSensorUpdateService
{
    use FormProcessorTrait;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var FormFactoryInterface
     */
    protected FormFactoryInterface $formFactory;

    /**
     * @var array
     */
    protected array $userInputErrors = [];

    /**
     * @var array
     */
    protected array $serverErrors = [];

    /**
     * AbstractSensorService constructor.
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->formFactory = $formFactory;
    }

    /**
     * @param array $sensorFormData
     * @param array $readingTypeObjects
     */
    protected function processSensorForm(array $sensorFormData, array $readingTypeObjects): void
    {
        foreach ($sensorFormData as $sensorType => $sensorData) {
            foreach ($readingTypeObjects as $sensorObject) {
                if ($sensorType === $sensorObject::class) {
                    $sensorForm = $this->formFactory->create($sensorData['formToProcess'], $sensorObject, ['formSensorType' => new $sensorData['object']]);
                    $handledForm = $this->processForm($sensorForm, $sensorData['formData']);
//                    dd($this->formInputErrors);
                    if ($handledForm === true) {
//                        dd('it did');
                        $this->em->persist($sensorForm->getData());
                    }
//                    dd('did not');
                }
            }
        }
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
//        dd($readingsToUpdate);
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
//                                        dd($sensorFormsData);
                                    }
                                }
                            }
                            continue;
                        }
//dd($sensorType);
                        if ($formToProcess === SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY) {
                            foreach ($readingsToUpdate['sensorData'] as $sensorData) {
                                foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
//                                dd('di');
//                                    dd($sensorData);
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
//                                        dd($sensorFormsData);
                                        continue;
                                    }
                                }
                            }
                        }
                        //Any other forms can be added here
                    }
                }
            }
        }
//    dd($sensorFormsData);
        return $sensorFormsData ?? [];
    }

    /**
     * @param UpdateSensorReadingDTO $updateSensorReadingDTO
     * @param $device
     * @return ArrayCollection
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

//        dd($updateData, 'hi2');
        $sensorFormData = $this->prepareSensorFormData(
            $sensorType,
            ['sensorData' => $updateData],
            SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY
        );
//dd($sensorFormData);
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
            $this->em->getRepository(Sensors::class)->getSensorReadingTypeObjectsBySensorNameAndDevice(
                $device,
                $sensorName,
                SensorType::SENSOR_READING_TYPE_DATA
            )
        );
    }

    /**
     * @return array
     */
    #[Pure] public function getUserInputErrors(): array
    {
        return array_merge($this->getAllFormInputErrors(), $this->userInputErrors);
    }

    /**
     * @return array
     */
    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }
}
