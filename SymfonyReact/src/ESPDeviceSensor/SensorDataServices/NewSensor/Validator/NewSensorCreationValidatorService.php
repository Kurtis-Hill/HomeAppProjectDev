<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\Validator;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewSensorCreationValidatorService implements NewSensorCreationValidatorServiceInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private ValidatorInterface $validator;

    private Security $security;

    private array $userInputErrors = [];

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        DeviceRepositoryInterface $deviceRepository,
        ValidatorInterface $validator,
        Security $security
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->validator = $validator;
        $this->security = $security;
    }

    public function createNewSensor(NewSensorDTO $newSensorDTO, Devices $device): ?Sensors
    {
        $sensorTypeObject = $this->sensorTypeRepository->findOneById($newSensorDTO->getSensorTypeId());
//        dd($sensorTypeObject);

        if (empty($sensorTypeObject)) {
            throw new SensorTypeException(SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED);
        }
        $newSensor = new Sensors();
        $newSensor->setSensorTypeID($sensorTypeObject);
        $newSensor->setSensorName($newSensorDTO->getSensorName());
        $newSensor->setCreatedBy($this->security->getUser());
        $newSensor->setDeviceNameID($device);

//dd('asd');
        if ($this->validateNewSensorEntity($newSensor) !== true) {
//            dd('wrong', $this->userInputErrors);
            return null;
        }

        $this->duplicateSensorOnSameDeviceCheck($newSensor);
        $this->sensorRepository->persist($newSensor);
        $this->sensorRepository->flush();
        return $newSensor;
    }

    private function validateNewSensorEntity(Sensors $sensor): bool
    {
        $errors = $this->validator->validate($sensor);

//        dd($errors);
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $this->userInputErrors[] = $error->getMessage();
            }
//            dd($this->userInputErrors, 'sdf');

            return false;
        }

        return true;
    }

    private function duplicateSensorOnSameDeviceCheck(Sensors $sensor): void
    {
        $currentUserSensorNameCheck = $this->sensorRepository->checkForDuplicateSensorOnDevice($sensor);

        if ($currentUserSensorNameCheck instanceof Sensors) {
            throw new DuplicateSensorException(
                sprintf(
                    DuplicateSensorException::MESSAGE,
                    $sensor->getSensorName()
                )
            );
        }
    }

    #[Pure] public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }
}
