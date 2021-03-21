<?php


namespace App\Services;

use App\Entity\Devices\Devices;
use App\Entity\Sensors\ConstantRecording\ConstAnalog;
use App\Entity\Sensors\ConstantRecording\ConstHumid;
use App\Entity\Sensors\ConstantRecording\ConstTemp;
use App\Entity\Sensors\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Entity\Sensors\OutOfRangeRecordings\OutofRangeHumid;
use App\Entity\Sensors\OutOfRangeRecordings\OutOfRangeTemp;
use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\HomeAppSensorCore\AbstractHomeAppSensorServiceCore;
use App\HomeAppSensorCore\Interfaces\StandardReadingSensorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use http\Exception\RuntimeException;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class SensorDataService extends AbstractHomeAppSensorServiceCore
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(EntityManagerInterface $em, Security $security, FormFactoryInterface $formFactory)
    {
        parent::__construct($em, $security);

        $this->formFactory = $formFactory;

    }

    public function processSensorReadingUpdateRequest(Request $request): ?array
    {
        $secret = $request->request->get('secret');
        $deviceName = $request->request->get('device-name');
        $sensorType = $request->request->get('sensor-type');

        //  try {
            $deviceRepository = $this->em->getRepository(Devices::class);
            $device = $deviceRepository->findUsersDeviceAPIRequest($deviceName, $secret);

            if (!$device instanceof Devices) {
                $deviceByName = $this->em->getRepository(Devices::class)->findOneBy(['deviceName' => $deviceName]);

                if (!$deviceByName instanceof Devices) {
                    throw new BadRequestException('No Device found called \"'.$deviceName.'\"');
                }

                throw new BadRequestException('Incorrect secret provided for '.$deviceName);
            }

            $checkIfLegitimateUser = $deviceRepository->findUsersDeviceAPIRequestCheckUser($deviceName, $secret, $this->getGroupNameIDs());

            if (empty($checkIfLegitimateUser)) {
                throw new BadRequestException('user is probably false');
                //log ip and ban
            }

            if ($sensorType === SensorType::DHT_SENSOR) {
                $this->handleDhtUpdateRequest($request, $device);
            }
            if ($sensorType === SensorType::DALLAS_TEMPERATURE) {
                $this->handleDallasUpdateRequest($request, $device);
            }
            if ($sensorType === SensorType::BMP_SENSOR) {
                $this->handleBmpUpdateRequest($request, $device);
            }
            if ($sensorType === SensorType::SOIL_SENSOR) {
                $this->handleSoilUpdateRequest($request, $device);
            }

//        } catch (BadRequestException $exception) {
//            $this->userInputErrors[] = $exception->getMessage();
//        } catch (\RuntimeException $exception) {
//            $this->userInputErrors[] = 0;
//        } catch (ORMException $exception) {
//            $this->serverErrors[] = $exception->getMessage();
//        } catch (\Exception $exception) {
//            $this->serverErrors[] = $exception->getMessage();
//        }
    }

    private function findSensorForRequest(Devices $device, string $sensorName): ?Sensors
    {
        $sensor = $this->em->getRepository(Sensors::class)->findOneBy(
            [
                'sensorName' => $sensorName,
                'deviceNameID' => $device
            ]
        );

//            dd($sensorType);

        if (!$sensor instanceof Sensors) {
            throw new BadRequestException('no sensor called ' .$sensorName. ' exists');
        }
//        dd($sensor);
        return $sensor;
    }

    private function handleDhtUpdateRequest(Request $request, Sensors $sensor): void
    {

    }


    private function handleDallasUpdateRequest(Request $request, Devices $device): void
    {
        $sensorReadings = $this->multipleSensorsWithSingularReading($request, Dallas::MAX_POSSIBLE_SENSORS);

        foreach ($sensorReadings as $sensorReading) {
           // try {
                $sensor = $this->findSensorForRequest($device, $sensorReading['sensorName']);

                if ($sensor === null) {
                    throw new BadRequestException('sensor not recognised ' .$sensor->getSensorName());
                }
                if (empty($sensorReading['sensorReading'])) {
                    throw new BadRequestException('sensor reading is empty for' .$sensor->getSensorName());
                }

                $temperatureObject = $this->em->getRepository(Dallas::class)->findDallasSensor($sensor);

                if (!$temperatureObject instanceof Temperature) {
                    throw new BadRequestException('No temperature sensor can be found for '.$sensor->getSensorName());
                }

                $sensorFormData = $this->prepareSensorFormData($request, $temperatureObject->getSensorObject()->getSensorTypeID(), SensorType::UPDATE_CURRENT_READING_FORM_ARRAY_KEY, $sensorReading['sensorReading']);

                if (!empty($sensorFormData)) {
                    $this->processSensorForm($sensorFormData, [$temperatureObject]);
                    dd('here', $this->userInputErrors);
                }
                if (!empty($this->userInputErrors)) {
                    throw new BadRequestException('Sensor Data is not in line with the outlined specification');
                }

                $this->checkAndProcessConstRecord($temperatureObject, $sensor);

                $this->checkTemperatureOutOfBounds($temperatureObject, $sensor);

                $this->em->persist($temperatureObject);
                // here intentionally so if any sensor fails to persist other sensor data can still be processed
                $this->em->flush();

                dd('done');
//            } catch (BadRequestException $exception) {
//                $this->userInputErrors[] = $exception->getMessage();
//            } catch (\RuntimeException $exception) {
//                $this->userInputErrors[] = 0;
//            } catch (ORMException $exception) {
//                $this->serverErrors[] = $exception->getMessage();
//            } catch (\Exception $exception) {
//                $this->serverErrors[] = $exception->getMessage();
//            }

        }
    }

    private function processSensorForm(array $sensorFormData, $readingTypeObject): void
    {
        foreach ($sensorFormData as $sensorType => $sensorData) {
            foreach ($readingTypeObject as $sensorObject) {
//                dd($sensorType, $sensorObject, $sensorType == $sensorObject::class);
                if ($sensorType === $sensorObject::class) {
//                    dd('success', $sensorData, $sensorFormData);
                    $sensorForm = $this->formFactory->createBuilder($sensorData['formToProcess'], $sensorObject, ['formSensorType' => new $sensorData['object']])->getForm();
                    $handledSensorForm = $this->processForm($sensorForm, $sensorData['formData']);
//                    dd($sensorForm->getForm());

                    if ($handledSensorForm instanceof FormInterface) {
                        $this->processSensorFormErrors($handledSensorForm);
                    }
                    continue;
                }
            }
        }
       // dd($handledSensorForm, $this->userInputErrors);
    }

    private function checkTemperatureOutOfBounds(StandardReadingSensorInterface $readingTypeTypeObject, Sensors $sensor): void
    {
        if ($readingTypeTypeObject->isReadingOutOfBounds() === true) {
            if ($readingTypeTypeObject instanceof Temperature) {
                $outOfBounds = new OutOfRangeTemp();
            }
            if ($readingTypeTypeObject instanceof Humidity) {
                $outOfBounds = new OutofRangeHumid();
            }
            if ($readingTypeTypeObject instanceof Latitude) {
                //to do make table
            }
            if ($readingTypeTypeObject instanceof Analog) {
                $outOfBounds = new OutOfRangeAnalog();
            }

            if (!isset($outOfBounds)) {
                throw new BadRequestException('out of range table cannot be found you may need to update your application');
            }

            $outOfBounds->setSensorID($sensor);
            $outOfBounds->setSensorReading($readingTypeTypeObject->getCurrentReading());
            $outOfBounds->setTime();

            $this->em->persist($outOfBounds);
        }
    }

    private function checkAndProcessConstRecord(StandardReadingSensorInterface $readingTypeObject, Sensors $sensor)
    {
        if ($readingTypeObject->getConstRecord() === true) {
            if ($readingTypeObject instanceof Temperature) {
                $constObject = new ConstTemp();
            }
            if ($readingTypeObject instanceof Humidity) {
                $constObject = new ConstHumid();
            }
            if ($readingTypeObject instanceof Latitude) {
                //to do make table
            }
            if ($readingTypeObject instanceof Analog) {
                $constObject = new ConstAnalog();
            }

            if (!isset($constObject)) {
                throw new BadRequestException('constantly record table cannot be found you may need to update your application');
            }

            $constObject->setSensorReading($readingTypeObject->getCurrentReading());
            $constObject->setSensorID($sensor);
            $constObject->setTime();

            $this->em->persist($constObject);
        }
    }


    private function handleBmpUpdateRequest(Request $request, Sensors $sensors)
    {

    }

    private function handleSoilUpdateRequest(Request $request, Sensors $sensors)
    {

    }

    private function multipleSensorsWithSingularReading(Request $request, $maxPossibleReadings): array
    {
        $sensorUpdateDetails = [];

        for ($i = 1; $i < $maxPossibleReadings; ++$i) {
            $sensorName = $request->request->get('sensor-name'.$i);
            if (!empty($sensorName)) {
                $sensorReading = $request->request->get('sensor-reading'.$i);

                $sensorUpdateDetails[] = [
                    'sensorName' => $sensorName,
                    'sensorReading' => $sensorReading
                ];
            } else {
                break;
            }
        }

        return $sensorUpdateDetails;
    }


    /**
     * @param Request $request
     * @param SensorType $sensorType
     * @param string $formToProcess
     * @param string|null $reading
     * @return array
     */
    public function prepareSensorFormData(Request $request, SensorType $sensorType, string $formToProcess, ?string $reading = null): array
    {
        // change req5uest to array
        $currentSensorType = $sensorType->getSensorType();

        foreach (self::SENSOR_TYPE_DATA as $sensorName => $sensorDataArrays) {
            if ($sensorName === $currentSensorType) {
                foreach ($sensorDataArrays['forms'] as $formType => $formData) {
                    if ($formType === $formToProcess) {

                        if ($formToProcess === SensorType::OUT_OF_BOUND_FORM_ARRAY_KEY) {
                            foreach ($formData['readingTypes'] as $readingType => $readingTypeClass) {
                                $sensorFormsData[$readingTypeClass] = [
                                    'formToProcess' => $formData['form'],
                                    'object' => $sensorDataArrays['object'],
                                    'formData' => [
                                        'highReading' => $request->get($readingType . 'HighReading')
                                            ?? $this->userInputErrors[] = ucfirst($readingType) . 'High Reading Failed',
                                        'lowReading' => $request->get($readingType . 'LowReading')
                                            ?? $this->userInputErrors[] = ucfirst($readingType) . ' Low Reading Failed',
                                        'constRecord' => $request->get($readingType . 'ConstRecord')
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
                                        'currentReading' => $reading ?? $request->get($readingType . 'Reading')
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
}
