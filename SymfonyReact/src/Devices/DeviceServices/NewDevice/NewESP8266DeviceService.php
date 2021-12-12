<?php


namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\DeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Forms\AddNewDeviceForm;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\ORMException;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;


class NewESP8266DeviceService implements NewDeviceServiceInterface
{
    use FormProcessorTrait;

    private FormFactoryInterface $formFactory;

    private DeviceRepositoryInterface $deviceRepository;

    private Security $security;

    private UserPasswordEncoderInterface $passwordEncoder;

    private array $userInputErrors = [];

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        FormFactoryInterface $formFactory,
        Security $security,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->formFactory = $formFactory;
        $this->deviceRepository = $deviceRepository;
        $this->security = $security;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function handleNewDeviceCreation(DeviceDTO $deviceData): ?Devices
    {
        try {
            $newDevice = new Devices();
            $addNewDeviceForm = $this->formFactory->create(AddNewDeviceForm::class, $newDevice);

            $this->duplicateDeviceCheck($deviceData);
            $this->processNewDeviceForm($addNewDeviceForm, $deviceData);
        }
        catch (BadRequestException $e) {
            $this->userInputErrors[] = $e->getMessage();
        }

        return $newDevice ?? null;
    }

    private function duplicateDeviceCheck(DeviceDTO $deviceData): void
    {
        $currentUserDeviceCheck = $this->deviceRepository->findDuplicateDeviceNewDeviceCheck(
            $deviceData->getDeviceName(),
            $deviceData->getRoomId()
        );

        if ($currentUserDeviceCheck instanceof Devices) {
            throw new DuplicateDeviceException(
                sprintf(
                    DuplicateDeviceException::MESSAGE,
                    $currentUserDeviceCheck->getDeviceName(),
                    $currentUserDeviceCheck->getRoomObject()->getRoom()
                )
            );
        }
    }

    private function processNewDeviceForm(FormInterface $addNewDeviceForm, DeviceDTO $deviceData): void
    {
        $addNewDeviceForm->submit([
            'deviceName' => $deviceData->getDeviceName(),
            'groupNameObject' => $deviceData->getGroupNameId(),
            'roomObject' => $deviceData->getRoomId(),
        ]);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $secret = $deviceData->getDeviceName();
            $secret .= time();
            $secret = hash("md5", $secret);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setDeviceSecret($secret);
            $validFormData->setCreatedBy($this->security->getUser());
            $validFormData->setRoles([Devices::ROLE]);
        }
        else {
            $this->processFormErrors($addNewDeviceForm);
        }
    }

    public function encodeAndSaveNewDevice(Devices $newDevice): bool
    {
        $this->encodePassword($newDevice);
        try {
            $this->deviceRepository->persist($newDevice);
            $this->deviceRepository->flush();

            return true;
        } catch (ORMException) {
            return false;
        }
    }

    private function encodePassword(Devices $device): void
    {
        $device->setPassword(
            $this->passwordEncoder->encodePassword(
                $device,
                $device->getDeviceSecret()
            )
        );
    }

    #[Pure] public function getUserInputErrors(): array
    {
        return array_merge($this->getAllFormInputErrors(), $this->userInputErrors);
    }
}
