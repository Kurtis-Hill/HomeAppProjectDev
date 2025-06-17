<?php

namespace App\Services\Sensor\NewSensor;

use App\DTOs\Sensor\Internal\Sensor\NewSensorDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Exceptions\Sensor\DuplicateSensorException;
use App\Exceptions\Sensor\UserNotAllowedException;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\Sensor\Sensors\SensorTypeRepositoryInterface;
use App\Services\Sensor\UpdateSensor\DuplicateSensorCheckService;
use App\Traits\ValidatorProcessorTrait;
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
        $sensor->setCreatedBy($newSensorDTO->getUser());
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
