<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor\Form;

use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use App\ESPDeviceSensor\Forms\AddNewSensorForm;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\Traits\FormProcessorTrait;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NewSensorCreationFormService implements NewSensorCreationFormServiceInterface
{
    use FormProcessorTrait;

    private SensorRepositoryInterface $sensorRepository;

    private FormFactoryInterface $formFactory;

    private SensorTypeRepositoryInterface $sensorTypeRepository;

    private array $userInputErrors = [];

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        FormFactoryInterface $formFactory,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->formFactory = $formFactory;
    }

    public function createNewSensor(NewSensorDTO $newSensorDTO): Sensors
    {
        $newSensor = new Sensors();

        $addNewSensorForm = $this->formFactory->create(AddNewSensorForm::class, $newSensor);
        $handledForm = $this->processNewSensorForm($addNewSensorForm, $newSensorDTO);
        if ($handledForm === true) {
            $this->sensorRepository->persist($addNewSensorForm->getData());
            $this->sensorRepository->flush();
        }

        return $newSensor;
    }

    private function processNewSensorForm(FormInterface $addNewSensorForm, NewSensorDTO $newSensorDTO): bool
    {
        $processedFormResult = $this->processForm(
            $addNewSensorForm,
            [
                'sensorName' => $newSensorDTO->getSensorName(),
                'sensorTypeID' => $newSensorDTO->getSensorTypeID(),
                'deviceNameID' => $newSensorDTO->getDeviceNameID(),
            ]
        );
        if ($processedFormResult === true) {
            $this->duplicateSensorOnSameDeviceCheck($addNewSensorForm->getData());
        }

        return $processedFormResult;
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
        return array_merge($this->getAllFormInputErrors(), $this->userInputErrors);
    }
}
