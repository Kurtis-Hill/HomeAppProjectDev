<?php

namespace App\Services\ESPDeviceSensor\SensorData\NewSensor;

use App\Entity\Sensors\Sensors;
use App\Form\FormMessages;
use App\Form\SensorForms\AddNewSensorForm;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class NewSensorCreationService implements NewSensorCreationServiceInterface, APIErrorInterface
{
    use FormProcessorTrait;

    private EntityManagerInterface $em;

    protected FormFactoryInterface $formFactory;

    private array $serverErrors;

    private array $userInputErrors;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->em = $entityManager;
    }

    public function createNewSensor(array $sensorData): ?Sensors
    {
        try {
            $newSensor = new Sensors();

            $addNewSensorForm = $this->formFactory->create(AddNewSensorForm::class, $newSensor);

            $handledForm = $this->processNewSensorForm($addNewSensorForm, $sensorData);

            if ($handledForm === true) {
                $this->em->persist($addNewSensorForm->getData());
            }

            return $newSensor;
        } catch (BadRequestException $exception) {
            $this->userInputErrors[] = $exception->getMessage();
        } catch (ORMException $e) {
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
        $currentUserSensorNameCheck = $this->em->getRepository(Sensors::class)->checkForDuplicateSensorOnDevice($sensor);

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
