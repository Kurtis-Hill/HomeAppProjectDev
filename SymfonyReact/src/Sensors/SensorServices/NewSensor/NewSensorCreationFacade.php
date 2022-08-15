<?php

namespace App\Sensors\SensorServices\NewSensor;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

class NewSensorCreationFacade implements NewSensorCreationInterface
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

    #[ArrayShape(['validationErrors'])]
    public function processNewSensor(NewSensorDTO $newSensorDTO): array
    {
        $sensor = $newSensorDTO->getSensor();
        $sensor->setSensorName($newSensorDTO->getSensorName());
        $sensor->setSensorTypeID($newSensorDTO->getSensorType());
        $sensor->setDeviceObject($newSensorDTO->getDevice());
        try {
            $sensor->setCreatedBy($newSensorDTO->getUser());
        } catch (TypeError) {
            throw new UserNotAllowedException(UserNotAllowedException::MESSAGE);
        }

        return $this->validateSensor($sensor);
    }

    private function validateSensor(Sensor $sensor): array
    {
        $validationErrors = $this->validator->validate($sensor);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            $errors = $this->getValidationErrorAsArray($validationErrors);
        }

        try {
            $this->duplicateSensorOnSameDeviceCheck($sensor);
        } catch (DuplicateSensorException $e) {
            $errors[] = $e->getMessage();
        }

        return $errors ?? [];
    }

    /**
     * @throws DuplicateSensorException
     */
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

    public function saveSensor(Sensor $sensor): bool
    {
        try {
            $this->sensorRepository->persist($sensor);
            $this->sensorRepository->flush();

            return true;
        } catch (ORMException|OptimisticLockException) {
            return false;
        }
    }
}
