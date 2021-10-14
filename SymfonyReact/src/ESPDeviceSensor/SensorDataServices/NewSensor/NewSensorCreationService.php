<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\Core\APIInterface\APIErrorInterface;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Forms\AddNewSensorForm;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepository;
use App\Form\FormMessages;
use App\Traits\FormProcessorTrait;
use Exception;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class NewSensorCreationService implements NewSensorCreationServiceInterface, APIErrorInterface
{
    use FormProcessorTrait;

    private SensorRepository $sensorRepository;

    protected FormFactoryInterface $formFactory;

    private array $serverErrors = [];

    private array $userInputErrors = [];

    public function __construct(SensorRepository $sensorRepository, FormFactoryInterface $formFactory)
    {
        $this->sensorRepository = $sensorRepository;
        $this->formFactory = $formFactory;
    }

    public function createNewSensor(NewSensorDTO $newSensorDTO): ?Sensors
    {
        try {
            $newSensor = new Sensors();

            $addNewSensorForm = $this->formFactory->create(AddNewSensorForm::class, $newSensor);

            $handledForm = $this->processNewSensorForm($addNewSensorForm, $newSensorDTO);

            if ($handledForm === true) {
                $this->sensorRepository->persist($addNewSensorForm->getData());
                $this->sensorRepository->flush();
            }

            return $newSensor;
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        } catch (Exception $e) {
            error_log($e->getMessage());
            $this->serverErrors[] = FormMessages::FORM_QUERY_ERROR;
        }

        return null;
    }

    /**
     * @param FormInterface $addNewSensorForm
     * @param NewSensorDTO $newSensorDTO
     * @return bool
     */
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

    /**
     * @param Sensors $sensor
     */
    private function duplicateSensorOnSameDeviceCheck(Sensors $sensor): void
    {
        $currentUserSensorNameCheck = $this->sensorRepository->checkForDuplicateSensorOnDevice($sensor);

        if ($currentUserSensorNameCheck instanceof Sensors) {
            throw new BadRequestException('You already have a sensor named ' . $sensor->getSensorName());
        }
    }

    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }

    #[Pure] public function getUserInputErrors(): array
    {
        return array_merge($this->getAllFormInputErrors(), $this->userInputErrors);
    }
}
