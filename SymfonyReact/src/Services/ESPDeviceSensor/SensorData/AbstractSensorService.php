<?php


namespace App\Services\ESPDeviceSensor\SensorData;


use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\Security;

abstract class AbstractSensorService implements APIErrorInterface
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
                    if ($handledForm === true) {
                        $this->em->persist($sensorForm->getData());
                    }
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
    protected function  prepareSensorFormData(SensorType $sensorType, array $readingsToUpdate, string $formToProcess): array
    {
        $currentSensorType = $sensorType->getSensorType();
        foreach (SensorType::ALL_SENSOR_TYPE_DATA as $sensorName => $sensorDataArrays) {
            if ($sensorName === $currentSensorType) {
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
                                        continue;
                                    }
                                }
                            }
                            continue;
                        }

                        if ($formToProcess === SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY) {
                            foreach ($readingsToUpdate['sensorData'] as $sensorData) {
                                foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                    if ($readingType === $sensorData['sensorType']) {
                                        $currentReading = $readingsToUpdate[$readingType . 'currentReading'];

                                        $readingErrorMessage = "%s %s has no value";
                                        !empty($currentReading) ?: $this->userInputErrors[] = sprintf($readingErrorMessage, ucfirst($readingType), 'current reading');

                                        $sensorFormsData[$readingTypeClass] = [
                                            'formToProcess' => $formData['form'],
                                            'object' => $sensorDataArrays['object'],
                                            'formData' => [
                                                'currentReading' => $currentReading,
                                            ]
                                        ];
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
