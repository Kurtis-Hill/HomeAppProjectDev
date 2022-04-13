<?php

namespace App\Sensors\SensorDataServices\NewSensor;

use App\API\Traits\FormProcessorTrait;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Forms\AddNewSensorForm;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormFactoryInterface;

class NewSensorCreationFormService implements NewSensorCreationServiceInterface
{
    use FormProcessorTrait;

    private SensorRepositoryInterface $sensorRepository;

    private FormFactoryInterface $formFactory;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        FormFactoryInterface $formFactory,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->formFactory = $formFactory;
    }

    public function createNewSensor(NewSensorDTO $newSensorDTO): Sensor
    {
        $sensor = new Sensor();
        $sensor->setSensorName($newSensorDTO->getSensorName());
        $sensor->setSensorType($newSensorDTO->getSensorType());
        $sensor->setDeviceObject($newSensorDTO->getDevice());
        $sensor->setCreatedBy($newSensorDTO->getUser());

        return $sensor;
    }

    public function validateSensor(Sensor $sensor): array
    {
        $errors = [];
        $validationErrors = $this->processNewSensorForm($sensor);

        if (!empty($validationErrors)) {
            return $validationErrors;
        }
        try {
            $this->duplicateSensorOnSameDeviceCheck($sensor);
        } catch (DuplicateSensorException $e) {
            $errors[] = $e->getMessage();
        }

        return $errors;
    }

    private function processNewSensorForm(Sensor $sensor): array
    {
        $addNewSensorForm = $this->formFactory->create(AddNewSensorForm::class, $sensor);

        return $this->processForm(
            $addNewSensorForm,
            [
                'sensorName' => $sensor->getSensorName(),
                'sensorTypeObject' => $sensor->getSensorTypeObject(),
                'deviceNameObject' => $sensor->getDeviceObject(),
            ]
        );
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

    public function validateNewSensorRequestDTO(AddNewSensorRequestDTO $addNewSensorRequestDTO): array
    {
        // TODO: Implement validateNewSensorRequestDTO() method.
    }

}
