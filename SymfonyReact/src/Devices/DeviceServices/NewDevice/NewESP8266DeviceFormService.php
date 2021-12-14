<?php


namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\NewDeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use App\Devices\Forms\AddNewDeviceForm;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Traits\FormProcessorTrait;
use Doctrine\ORM\ORMException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class NewESP8266DeviceFormService implements NewDeviceServiceInterface
{
    use FormProcessorTrait;

    private FormFactoryInterface $formFactory;

    private DeviceRepositoryInterface $deviceRepository;

    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(
        DeviceRepositoryInterface $deviceRepository,
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->formFactory = $formFactory;
        $this->deviceRepository = $deviceRepository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function createNewDevice(NewDeviceDTO $deviceDTO): Devices
    {
        $newDevice = new Devices();
        $newDevice->setDeviceName($deviceDTO->getDeviceName());
        $newDevice->setCreatedBy($deviceDTO->getCreatedByUserObject());
        $newDevice->setGroupNameObject($deviceDTO->getGroupNameObject());
        $newDevice->setRoomObject($deviceDTO->getRoomObject());

        return $newDevice;
    }

    public function validateNewDevice(Devices $newDevice): array
    {
        $addNewDeviceForm = $this->formFactory->create(AddNewDeviceForm::class);
        $this->processNewDeviceForm($addNewDeviceForm, $newDevice);

        if (!empty($this->getAllFormInputErrors())) {
            return $this->getAllFormInputErrors();
        }

        $errors = [];
        try {
            $this->duplicateDeviceCheck($newDevice);
        }
        catch (DuplicateDeviceException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $errors;
    }

    private function duplicateDeviceCheck(Devices $deviceData): void
    {
        $currentUserDeviceCheck = $this->deviceRepository->findDuplicateDeviceNewDeviceCheck(
            $deviceData->getDeviceName(),
            $deviceData->getRoomID(),
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

    private function processNewDeviceForm(FormInterface $addNewDeviceForm, Devices $device): void
    {
        $addNewDeviceForm->submit([
            'deviceName' => $device->getDeviceName(),
            'groupNameObject' => $device->getGroupNameID(),
            'roomObject' => $device->getRoomID(),
        ]);

        if ($addNewDeviceForm->isSubmitted() && $addNewDeviceForm->isValid()) {
            $devicePasswordHash = $this->createDevicePasswordHash($device);

            $validFormData = $addNewDeviceForm->getData();
            $validFormData->setDeviceSecret($devicePasswordHash);
            $validFormData->setRoles([Devices::ROLE]);
        }
        else {
            $this->processFormErrors($addNewDeviceForm);
        }
    }

    private function createDevicePasswordHash(Devices $device): string
    {
        $secret = $device->getDeviceName();
        $secret .= time();

        return hash("md5", $secret);
    }

    public function encodeAndSaveNewDevice(Devices $newDevice): bool
    {
        $this->encodeDevicePassword($newDevice);
        try {
            $this->deviceRepository->persist($newDevice);
            $this->deviceRepository->flush();

            return true;
        } catch (ORMException) {
            return false;
        }
    }

    private function encodeDevicePassword(Devices $device): void
    {
        $device->setPassword(
            $this->passwordEncoder->encodePassword(
                $device,
                $device->getDeviceSecret()
            )
        );
    }
}
