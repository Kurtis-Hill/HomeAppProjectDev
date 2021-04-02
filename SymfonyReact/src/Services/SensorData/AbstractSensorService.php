<?php


namespace App\Services\SensorData;


use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Form\CardViewForms\StandardSensorOutOFBoundsForm;
use App\Form\SensorForms\UpdateReadingForm;
use App\Services\AbstractHomeAppUserSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class AbstractSensorService implements APIErrorInterface
{
   // use FormProcessorTrait;
    public const SENSOR_TYPE_DATA = [
        SensorType::DHT_SENSOR => [
            'alias' => 'dht',
            'object' => Dht::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' => Humidity::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' => Humidity::class,
                    ],
                ]
            ]
        ],

        SensorType::DALLAS_TEMPERATURE => [
            'alias' => 'dallas',
            'object' => Dallas::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                    ],
                ]
            ]
        ],

        SensorType::SOIL_SENSOR => [
            'alias' => 'soil',
            'object' => Soil::class,
            'readingTypes' => [
                'analog' =>  Analog::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'analog' =>  Analog::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'analog' =>  Analog::class,
                    ],
                ]
            ]
        ],

        SensorType::BMP_SENSOR => [
            'alias' => 'bmp',
            'object' => Bmp::class,
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' =>  Humidity::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' =>  Humidity::class,
                        'latitude' => Latitude::class,
                    ],
                ]
            ]
        ],
    ];

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $em;

    /**
     * @var Devices|Security|User
     */
    protected Security|Devices|User $user;

    /**
     * @var FormFactoryInterface
     */
    protected FormFactoryInterface $formFactory;

    /**
     * @var array
     */
    protected array $serverErrors = [];

    /**
     * @var array
     */
    protected array $userInputErrors = [];


    public function __construct(EntityManagerInterface $em, Security $security, FormFactoryInterface $formFactory)
    {
        $this->em = $em;
        $this->user = $security;
        $this->formFactory = $formFactory;

    }

    protected function findSensorForRequest(Devices $device, string $sensorName): ?Sensors
    {
        $sensor = $this->em->getRepository(Sensors::class)->findOneBy(
            [
                'sensorName' => $sensorName,
                'deviceNameID' => $device
            ]
        );

        if (!$sensor instanceof Sensors) {
            throw new BadRequestException('no sensor named ' .$sensorName. ' exists');
        }

        return $sensor;
    }

    protected function processSensorForm(array $sensorFormData, $readingTypeObject): void
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
                        $this->processSensorFormErrors($handledForm);
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
     * @param string|null $readings
     * @return array
     */
    protected function prepareSensorFormData(Request $request, SensorType $sensorType, string $formToProcess, array $readings = []): array
    {
        // change req5uest to array
        $currentSensorType = $sensorType->getSensorType();

        foreach (self::SENSOR_TYPE_DATA as $sensorName => $sensorDataArrays) {
            if ($sensorName === $currentSensorType) {
                foreach ($sensorDataArrays['forms'] as $formType => $formData) {
                    if ($formType === $formToProcess) {

                        if ($formToProcess === SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY) {
                            foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                $highReading = $request->get($readingType . '-high-reading');
                                $lowReading =  $request->get($readingType . '-low-reading');
                                $constRecord = $request->get($readingType . '-const-record');

                                $errorMessage = "%s %s is not complete please fill all of the form in";
                                !empty($highReading) ?: $this->userInputErrors[] = sprintf($errorMessage, ucfirst($readingType), 'high reading');
                                !empty($lowReading) ?: $this->userInputErrors[] = sprintf($errorMessage, ucfirst($readingType), 'low reading');
                                !empty($constRecord) ?: $this->userInputErrors[] = sprintf($errorMessage, ucfirst($readingType), 'constantly record');

                                $sensorFormsData[$readingTypeClass] = [
                                    'formToProcess' => $formData['form'],
                                    'object' => $sensorDataArrays['object'],
                                    'formData' => [
                                        'highReading' => $readings['highReading'] ?? $highReading,
                                        'lowReading' => $readings['lowReadingReading'] ?? $lowReading,
                                        'constRecord' => $readings['constRecord'] ?? $constRecord
                                    ]
                                ];
                            }
                            continue;
                        }

                        if ($formToProcess === SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY) {
                            foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                $sensorFormsData[$readingTypeClass] = [
                                    'formToProcess' => $formData['form'],
                                    'object' => $sensorDataArrays['object'],
                                    'formData' => [
                                        'currentReading' => $readings['currentReading'] ?? (float)$request->get($readingType . 'Reading')
                                            ?? $this->userInputErrors[] = ucfirst($readingType) . ' Current Reading Failed',
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
     * @param FormInterface $form
     * @param array $formData
     * @return bool|FormInterface
     */
    public function processForm(Form|FormFactoryInterface $form, array $formData): ?FormInterface
    {
        $form->submit($formData);

        if ($form->isSubmitted() && $form->isValid()) {
            $validFormData = $form->getData();

            try {
                $this->em->persist($validFormData);
            } catch (ORMException | \Exception $e) {
                error_log($e->getMessage());
                $this->serverErrors[] = 'Object persistence failed';
            }

            return null;
        }

        return $form;
    }

    /**
     * @param FormInterface $form
     */
    public function processSensorFormErrors(FormInterface $form): void
    {
        foreach ($form->getErrors(true, true) as $error) {
            $this->userInputErrors[] = $error->getMessage();
        }
    }

    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }

    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }


}
