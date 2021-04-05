<?php


namespace App\Services\ESPDeviceSensor\SensorData;

use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class AbstractSensorService extends AbstractHomeAppUserSensorServiceCore
{
    /**
     * @var FormFactoryInterface
     */
    protected FormFactoryInterface $formFactory;

    /**
     * AbstractSensorService constructor.
     * @param EntityManagerInterface $em
     * @param Security $security
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(EntityManagerInterface $em, Security $security, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $security);

        $this->formFactory = $formFactory;
    }

    /**
     * @param array $sensorFormData
     * @param array $readingTypeObject
     */
    protected function processSensorForm(array $sensorFormData, array $readingTypeObject): void
    {
//        dd($sensorFormData, $readingTypeObject);
        foreach ($sensorFormData as $sensorType => $sensorData) {
            foreach ($readingTypeObject as $sensorObject) {
//                dd($sensorType, $sensorObject, $sensorType == $sensorObject::class);
                if ($sensorType === $sensorObject::class) {
//                    dd('success', $sensorData, $sensorFormData);
                    $sensorForm = $this->formFactory->create($sensorData['formToProcess'], $sensorObject, ['formSensorType' => new $sensorData['object']]);
//                    dd($sensorData['formToProcess'], $sensorObject, $sensorData['object'], $sensorData['formData']);
                    $handledForm = $this->processForm($sensorForm, $sensorData['formData']);

                   //     dd($handledForm);
                    if ($handledForm instanceof FormInterface) {
                        $this->processFormErrors($handledForm);
                    }

//                    $this->processForm($sensorForm, $this->em, $sensorData['formData']);
//                    if (!empty($this->returnAllFormInputErrors())) {
//                        $this->userInputErrors[] = $this->returnAllFormInputErrors();
//                    }
                    continue;
                }
            }
        }
        // dd($handledSensorForm, $this->userInputErrors);
    }

    /**
     * @param Request $request
     * @param SensorType $sensorType
     * @param string $formToProcess
     * @param array $readingNameOverRide
     * @return array
     */
    protected function prepareSensorFormData(Request $request, SensorType $sensorType, string $formToProcess, array $readingNameOverRide = []): array
    {
        // change req5uest to array
        $currentSensorType = $sensorType->getSensorType();

        foreach (self::SENSOR_TYPE_DATA as $sensorName => $sensorDataArrays) {
            if ($sensorName === $currentSensorType) {
                foreach ($sensorDataArrays['forms'] as $formType => $formData) {
                    if ($formType === $formToProcess) {

                        if ($formToProcess === SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY) {
                            foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                $highReading = $readingNameOverRide[$readingType.'highReading'] ?? $request->get($readingType . '-high-reading');
                                $lowReading =  $readingNameOverRide[$readingType.'lowReading'] ?? $request->get($readingType . '-low-reading');
                                $constRecord = $readingNameOverRide[$readingType.'constRecord'] ?? $request->get($readingType . '-const-record');

                                $errorMessage = "%s %s has no value";
                                !empty($highReading) ?: $this->userInputErrors[] = sprintf($errorMessage, ucfirst($readingType), 'high reading');
                                !empty($lowReading) ?: $this->userInputErrors[] = sprintf($errorMessage, ucfirst($readingType), 'low reading');
                                !empty($constRecord) ?: $this->userInputErrors[] = sprintf($errorMessage, ucfirst($readingType), 'constantly record');

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
                                $currentReading = $readingNameOverRide[$readingType.'currentReading'] ?? $request->get($readingType . '-high-reading');

                                $errorMessage = "%s %s has no value";
                                !empty($currentReading) ?: $this->userInputErrors[] = sprintf($errorMessage, ucfirst($readingType), 'current reading');

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
}