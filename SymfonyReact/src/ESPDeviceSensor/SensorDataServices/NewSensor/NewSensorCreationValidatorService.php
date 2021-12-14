<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewSensorCreationValidatorService implements NewSensorCreationServiceInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private ValidatorInterface $validator;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        ValidatorInterface $validator,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->validator = $validator;
    }

    public function createNewSensor(NewSensorDTO $newSensorDTO): Sensor
    {
        $sensor = new Sensor();
        $sensor->setSensorName($newSensorDTO->getSensorName());
        $sensor->setSensorTypeID($newSensorDTO->getSensorType());
        $sensor->setDeviceObject($newSensorDTO->getDevice());
        $sensor->setCreatedBy($newSensorDTO->getUser());

        return $sensor;
    }

    public function validateSensor(Sensor $sensor): array
    {
        $errors = [];
        $validationErrors = $this->validator->validate($sensor);

        if ($validationErrors !== null) {
            foreach ($validationErrors as $error) {
                $errors[] = $error->getMessage();
            }
        }

        try {
            $this->duplicateSensorOnSameDeviceCheck($sensor);
        } catch (DuplicateSensorException $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    private function duplicateSensorOnSameDeviceCheck(Sensor $sensor): void
    {
        $currentUserSensorNameCheck = $this->sensorRepository->checkForDuplicateSensorOnDevice($sensor);

        if ($currentUserSensorNameCheck instanceof Sensor) {
            throw new DuplicateSensorException(
                sprintf(
                    DuplicateSensorException::MESSAGE,
                    $sensor->getSensorName()
                )
            );
        }
    }

    public function saveNewSensor(Sensor $sensor): bool
    {
        try {
            $this->sensorRepository->persist($sensor);
            $this->sensorRepository->flush();

            return true;
        } catch (ORMException) {
            return false;
        }
    }
}
