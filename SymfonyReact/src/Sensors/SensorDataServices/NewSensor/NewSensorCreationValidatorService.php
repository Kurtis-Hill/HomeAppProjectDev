<?php

namespace App\Sensors\SensorDataServices\NewSensor;

use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\DTO\Sensor\NewSensorDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewSensorCreationValidatorService implements NewSensorCreationServiceInterface
{
    use ValidatorProcessorTrait;

    private SensorRepositoryInterface $sensorRepository;

    private ValidatorInterface $validator;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        ValidatorInterface $validator,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->validator = $validator;
    }

    public function validateNewSensorRequestDTO(AddNewSensorRequestDTO $addNewSensorRequestDTO): array
    {
        $validationErrors = $this->validator->validate($addNewSensorRequestDTO);

        return $this->getValidationErrorAsArray($validationErrors);
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
        } catch (ORMException | OptimisticLockException) {
            return false;
        }
    }
}
