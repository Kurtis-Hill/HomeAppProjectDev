<?php


namespace App\Services\ESPDeviceSensor\Devices;


use App\Entity\Devices\Devices;
use App\Form\SensorForms\AddNewDeviceForm;
use App\HomeAppSensorCore\Interfaces\APIErrorInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Security;


class DeviceServiceUser implements APIErrorInterface
{
    use FormProcessorTrait;

    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    private Security $security;

    /**
     * @var array
     */
    private array $userInputErrors = [];

    /**
     * @var array
     */
    private array $serverErrors = [];

    /**
     * DeviceServiceUser constructor.
     * @param EntityManagerInterface $em
     * @param FormFactoryInterface $formFactory
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, FormFactoryInterface $formFactory, Security $security)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @param array $deviceData
     * @return Devices|null
     */
    public function handleNewDeviceSubmission(array $deviceData): ?Devices
    {
        try {
            $newDevice = new Devices();
            $addNewDeviceForm = $this->formFactory->create(AddNewDeviceForm::class, $newDevice);

            $this->duplicateSensorCheck($deviceData);

            $this->processNewDeviceForm($addNewDeviceForm, $deviceData);
        }
        catch (BadRequestException $e) {
            $this->userInputErrors[] = $e->getMessage();
        }
        catch (ORMException $e) {
            $this->serverErrors[] = 'Failed to process device query';
            error_log($e->getMessage());
        }

        return $newDevice ?? null;
    }

    private function duplicateSensorCheck(array $deviceData): void
    {
        $currentUserDeviceCheck = $this->em->getRepository(Devices::class)->findDuplicateDeviceNewDeviceCheck($deviceData);

        if ($currentUserDeviceCheck instanceof Devices) {
            throw new BadRequestException(
                sprintf('Your group already has a device named %s that is in room %s',
                    $currentUserDeviceCheck->getDeviceName(),
                    $currentUserDeviceCheck->getRoomObject()->getRoom()
                )
            );
        }
    }

    /**
     * @param FormInterface $addNewDeviceForm
     * @param array $deviceData
     * @return void
     */
    private function processNewDeviceForm(FormInterface $addNewDeviceForm, array $deviceData): void
    {
        $addNewDeviceForm->submit($deviceData);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $secret = $deviceData['deviceName'];
            $secret .= time();
            $secret = hash("md5", $secret);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setDeviceSecret($secret);
            $validFormData->setCreatedBy($this->security->getUser());
            $validFormData->setRoles([Devices::ROLE]);
        }
        else {
            $this->processSensorFormErrors($addNewDeviceForm);
        }
    }

    /**
     * @return array
     */
    #[Pure] public function getUserInputErrors(): array
    {
        return array_merge($this->returnAllFormInputErrors(), $this->userInputErrors);
    }

    /**
     * @return array
     */
    public function getServerErrors(): array
    {
        return $this->serverErrors;
    }
}
