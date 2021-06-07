<?php


namespace App\Services\ESPDeviceSensor\SensorData;

use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\AnalogSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\HumiditySensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\LatitudeSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

abstract class AbstractSensorService implements APIErrorInterface
{
    use FormProcessorTrait;

    protected EntityManagerInterface $em;

    protected Security $security;

    /**
     * @var FormFactoryInterface
     */
    protected FormFactoryInterface $formFactory;

    protected array $userInputErrors = [];

    protected array $serverErrors = [];

    /**
     * AbstractSensorService constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, Security $security, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->security = $security;
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
                    $this->processForm($sensorForm, $this->em, $sensorData['formData']);
//                    dd($sensorType, $readingTypeObjects, $sensorForm->getData());
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
        $currentSensorType = $sensorType->getSensorType();
        foreach (SensorType::SENSOR_TYPE_DATA as $sensorName => $sensorDataArrays) {
            if ($sensorName === $currentSensorType) {
                foreach ($sensorDataArrays['forms'] as $formType => $formData) {
                    if ($formType === $formToProcess) {
                        if ($formToProcess === SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY) {
                            foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                $highReading = $readingsToUpdate[$readingType.'-high-reading'] ?: null;
                                $lowReading =  $readingsToUpdate[$readingType.'-low-reading'] ?: null;
                                $constRecord = $readingsToUpdate[$readingType.'-const-record'] ?: null;

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
                            continue;
                        }

                        if ($formToProcess === SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY) {
                            foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                $currentReading = $readingsToUpdate[$readingType.'currentReading'];

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
                            continue;
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
    public function getUserInputErrors(): array
    {
        return array_merge($this->getAllFormInputErrors(), $this->userInputErrors);
    }

    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }


//    /**
//     * @param FormInterface|FormFactoryInterface $form
//     * @param array $formData
//     * @return bool|FormInterface
//     */
//    private function processForm(FormInterface|FormFactoryInterface $form, array $formData): ?FormInterface
//    {
//        $form->submit($formData);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $validFormData = $form->getData();
//            try {
//                $this->em->persist($validFormData);
//            } catch (ORMException | \Exception $e) {
//                error_log($e->getMessage());
//                $this->serverErrors[] = 'Object persistence failed';
//            }
//
//            return null;
//        }
//
//        return $form;
//    }
//
//    /**
//     * @param FormInterface $form
//     */
//    private function processFormErrors(FormInterface $form): void
//    {
//        foreach ($form->getErrors(true, true) as $error) {
//            $this->userInputErrors[] = $error->getMessage();
//        }
//    }
}
