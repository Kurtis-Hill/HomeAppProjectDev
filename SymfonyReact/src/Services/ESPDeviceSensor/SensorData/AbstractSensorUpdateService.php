<?php


namespace App\Services\ESPDeviceSensor\SensorData;


use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\Interfaces\AllSensorReadingTypeInterface;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Security;

abstract class AbstractSensorUpdateService implements APIErrorInterface
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
//                dd($sensorType, $sensorObject);
//        dd('hi');
                if ($sensorType === $sensorObject::class) {
                    $sensorForm = $this->formFactory->create($sensorData['formToProcess'], $sensorObject, ['formSensorType' => new $sensorData['object']]);
//                    dd($sensorData['formToProcess']);
//                    dd($sensorForm->getData());
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

    /**
     * @param SensorType $sensorType
     * @param array $readingsToUpdate
     * @param string $formToProcess
     * @return array
     */
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

        return $sensorFormsData ?? [];
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
