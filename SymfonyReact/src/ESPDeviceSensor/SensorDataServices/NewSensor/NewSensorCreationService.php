<?php

namespace App\ESPDeviceSensor\SensorDataServices\NewSensor;

use App\Entity\Sensors\Sensors;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepository;
use App\Form\FormMessages;
use App\Form\SensorForms\AddNewSensorForm;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\Traits\FormProcessorTrait;
use Exception;
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

    public function createNewSensor(array $sensorData): ?Sensors
    {
        try {
            $newSensor = new Sensors();

            $addNewSensorForm = $this->formFactory->create(AddNewSensorForm::class, $newSensor);

            $handledForm = $this->processNewSensorForm($addNewSensorForm, $sensorData);

            if ($handledForm === true) {
                $this->sensorRepository->persist($addNewSensorForm->getData());
            }

            $this->sensorRepository->flush();

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
     * @param array $sensorData
     * @return bool
     */
    private function processNewSensorForm(FormInterface $addNewSensorForm, array $sensorData): bool
    {
        $processedFormResult = $this->processForm($addNewSensorForm, $sensorData);

        $this->duplicateSensorOnSameDeviceCheck($addNewSensorForm->getData());

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

    public function getUserInputErrors(): array
    {
        return $this->userInputErrors;
    }
}
