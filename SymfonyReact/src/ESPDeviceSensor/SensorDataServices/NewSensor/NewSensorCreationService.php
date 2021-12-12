<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\Core\APIInterface\APIErrorInterface;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
use App\ESPDeviceSensor\Forms\AddNewSensorForm;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepository;
use App\Form\FormMessages;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\ORMException;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class NewSensorCreationService implements NewSensorCreationServiceInterface
{
    use FormProcessorTrait;

    private SensorRepository $sensorRepository;

    private FormFactoryInterface $formFactory;

    private array $userInputErrors = [];

    public function __construct(SensorRepository $sensorRepository, FormFactoryInterface $formFactory)
    {
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
