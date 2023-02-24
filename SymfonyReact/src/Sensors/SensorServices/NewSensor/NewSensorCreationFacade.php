<?php

namespace App\Sensors\SensorServices\NewSensor;

use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\Builders\SensorCreationBuilders\NewSensorDTOBuilder;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Exceptions\DeviceNotFoundException;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use App\User\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TypeError;

class NewSensorCreationFacade implements NewSensorCreationInterface
{
    use ValidatorProcessorTrait;

    private SensorRepositoryInterface $sensorRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private ValidatorInterface $validator;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        DeviceRepositoryInterface $deviceRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        ValidatorInterface $validator,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->deviceRepository = $deviceRepository;
        $this->sensorTypeRepository = $sensorTypeRepository;
        $this->validator = $validator;
    }

    /**
     * @throws DeviceNotFoundException
     * @throws SensorTypeNotFoundException
     */
    public function buildNewSensorDTO(AddNewSensorRequestDTO $newSensorRequestDTO, User $user): NewSensorDTO
    {
        $deviceObject = $this->deviceRepository->findOneById($newSensorRequestDTO->getDeviceNameID());
        if (!$deviceObject instanceof Devices) {
            throw new DeviceNotFoundException(
                sprintf(
                    DeviceNotFoundException::DEVICE_NOT_FOUND_FOR_ID,
                    $newSensorRequestDTO->getDeviceNameID()
                )
            );
        }

        $sensorTypeObject = $this->sensorTypeRepository->findOneById($newSensorRequestDTO->getSensorTypeID());
        if (!$sensorTypeObject instanceof SensorType) {
            throw new SensorTypeNotFoundException(
                sprintf(
                    SensorTypeNotFoundException::SENSOR_TYPE_NOT_FOUND_FOR_ID,
                    $newSensorRequestDTO->getSensorTypeID()
                )
            );
        }

        return NewSensorDTOBuilder::buildNewSensorDTO(
            $newSensorRequestDTO->getSensorName(),
            $sensorTypeObject,
            $deviceObject,
            $user
        );
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
