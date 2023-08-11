<?php

namespace App\Sensors\SensorServices\NewSensor;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use App\Sensors\SensorServices\UpdateSensor\DuplicateSensorCheckService;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

class NewSensorCreationHandler implements NewSensorCreationInterface
{
    use ValidatorProcessorTrait;

    private DeviceRepositoryInterface $deviceRepository;

    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private DuplicateSensorCheckService $duplicateSensorCheckService;

    private SensorSavingHandler $newSensorSavingHandler;

    private ValidatorInterface $validator;

    public function __construct(
        DuplicateSensorCheckService $duplicateSensorCheckService,
        SensorSavingHandler $newSensorSavingHandler,
        ValidatorInterface $validator,
    ) {
        $this->duplicateSensorCheckService = $duplicateSensorCheckService;
        $this->newSensorSavingHandler = $newSensorSavingHandler;
        $this->validator = $validator;
    }

    #[ArrayShape(['validationErrors'])]
    public function processNewSensor(NewSensorDTO $newSensorDTO): array
    {
        $sensor = $newSensorDTO->getSensor();
        try {
            $sensor->setCreatedBy($newSensorDTO->getUser());
        } catch (TypeError) {
            throw new UserNotAllowedException(UserNotAllowedException::MESSAGE);
        }
        $sensor->setSensorName($newSensorDTO->getSensorName());
        $sensor->setSensorTypeID($newSensorDTO->getSensorType());
        $sensor->setDevice($newSensorDTO->getDevice());
        $sensor->setPinNumber($newSensorDTO->getPinNumber());
        $sensor->setReadingInterval($newSensorDTO->getReadingInterval());

        return $this->validateSensor($sensor);
    }

    private function validateSensor(Sensor $sensor): array
    {
        $validationErrors = $this->validator->validate($sensor);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            $errors = $this->getValidationErrorAsArray($validationErrors);
        }

        try {
            $this->duplicateSensorCheckService->checkSensorForDuplicates(
                $sensor,
                $sensor->getDevice()->getDeviceID(),
                true,
            );
        } catch (DuplicateSensorException $e) {
            return [$e->getMessage()];
        }

        return $errors ?? [];
    }

    public function saveSensor(Sensor $sensor): bool
    {
        return $this->newSensorSavingHandler->saveSensor($sensor);
    }
}
